# Backlog — Персональный сайт Andrew Old

Статусы: `todo` · `in-progress` · `done`  
Приоритеты: `critical` · `high` · `medium` · `low`

Выполненные задачи переносятся в `CHANGELOG.md`.

---

## CRITICAL

### BL-001 · Закрыть публичный доступ к phpMyAdmin с автологином
**Статус:** `todo` · **Приоритет:** `critical`  
**Источник:** аудит доступа при настройке пользователя `aiops` (2026-11-02); `/home/dialectik/docker-sites/andrew-old.dib.in.ua/docker-compose.yml` (сервис `pma_ao`)

**Проблема:**
Контейнер `pma_ao` публикует порт на всех интерфейсах (`0.0.0.0:6086`, `[::]:6086`) и отвечает HTTP 200 из интернета. В `docker-compose.yml` для него заданы `PMA_USER` и `PMA_PASSWORD`, поэтому вход выполняется автоматически, без ввода пароля: любой посетитель `http://84.32.57.95:6086/` получает доступ к БД `admin_andrew_old` с правами пользователя приложения. Это чтение и изменение всех таблиц, включая `users` (email и хэши паролей администратора Voyager). Порты, опубликованные Docker, обходят цепочку `INPUT` firewall хоста, поэтому правил `ufw`/Hestia недостаточно. Та же конфигурация у `pma_md` проекта Moodle (`0.0.0.0:6085`).

**Цель:**
phpMyAdmin недоступен из интернета и не выполняет автоматический вход. Администрирование БД — только через SSH-туннель или с ручным вводом учётных данных.

**Что проанализировать перед реализацией:**

1. Используется ли phpMyAdmin владельцем на практике и как (откуда, как часто). От этого зависит выбор: удалить сервис, привязать к localhost или оставить за nginx Hestia с дополнительной защитой.
2. Логи `pma_ao` (`sudo aiops-docker logs pma_ao --since …`) на запросы с внешних IP с момента запуска контейнера: был ли фактический доступ посторонних.
3. Признаки несанкционированных изменений в БД: новые или изменённые записи `users` и `user_roles`, `permission_role`, `settings`, изменения `pages` (только SELECT, через `dib-aiops-db`).
4. Нет ли в Hestia или nginx проксирования на порт `6086`, которое сломается при смене привязки.

**Что сделать:**

1. В `docker-compose.yml` на production заменить публикацию порта `"${PHPMYADMIN_PORT}:80"` на `"127.0.0.1:${PHPMYADMIN_PORT}:80"` либо удалить сервис `pma_ao`, если он не нужен.
2. Удалить из окружения `pma_ao` переменные `PMA_USER`, `PMA_PASSWORD` и `MYSQL_ROOT_PASSWORD`, чтобы вход всегда требовал учётных данных.
3. Пересоздать только контейнер `pma_ao` (`docker compose up -d pma_ao`), не трогая `web_ao` и `db_ao`.
4. Если по результатам анализа (п. 2–3) доступ посторонних не исключён: сменить пароль MySQL-пользователя приложения (`.env` стека + `source/.env`) и пароль администратора Voyager.
5. Описать доступ к phpMyAdmin через SSH-туннель в `README.md` (раздел 11) и в `docs/context/Подключение к production-серверу.local.md`.
6. Аналогичную задачу завести в проекте Moodle для `pma_md`.

Все изменения на production выполняются владельцем сервера (учётка `diba`) или с его явного разрешения по правилам `PRODUCTION-SERVER-SAFETY.md`; файл `docker-compose.yml` в репозиторий не входит.

**Критерии приёмки:**

* `curl -I http://84.32.57.95:6086/` снаружи не устанавливает соединение.
* `ss -ltn` на сервере не показывает `0.0.0.0:6086` / `[::]:6086`.
* При открытии phpMyAdmin через туннель запрашиваются логин и пароль.
* Сайт `https://andrew-old.dib.in.ua` и админка Voyager работают как прежде.

**Тесты:**

* Проверка доступности порта снаружи (HEAD-запрос) до и после изменения.
* Проверка списка слушающих портов на сервере (`ss -ltn`) и `sudo aiops-docker ps`.
* HEAD-запросы к главной странице и `/admin/login` сайта после пересоздания контейнера.

**Файлы:** production: `/home/dialectik/docker-sites/andrew-old.dib.in.ua/docker-compose.yml`, `.env` (при смене паролей), `source/.env`; документация: `README.md`, `docs/context/Подключение к production-серверу.local.md`.

---

## HIGH

### BL-002 · Ограничить права на файлы с секретами
**Статус:** `todo` · **Приоритет:** `high`  
**Источник:** аудит доступа при настройке пользователя `aiops` (2026-11-02)

**Проблема:**
Файлы с паролями доступны на чтение всем локальным пользователям сервера:

* `andrew-old.dib.in.ua/.env` — права `644` (`diba:docker`), содержит `MYSQL_ROOT_PASSWORD` и пароль пользователя приложения;
* `andrew-old.dib.in.ua/source/.env` — права `664` (`www-data:www-data`), содержит `APP_KEY`, `DB_PASSWORD`, `MAIL_PASSWORD`;
* `moodle.dib.in.ua/source/config.php` (проект Moodle) — права `775`, содержит пароль БД Moodle.

Сервер общий (Hestia, несколько проектов и системных пользователей). Для `aiops` эти файлы закрыты запрещающими ACL; права для остальных пользователей не менялись.

**Цель:**
Файлы с секретами читают только владелец, процессы, которым они нужны (Docker Compose, Apache в контейнере), и администраторы.

**Что проанализировать перед реализацией:**

1. Кто читает каждый файл: `docker compose` запускается от `dialectik`/`diba` (группа `docker`); `source/.env` читает PHP в контейнере `web_ao` от `www-data` (uid 33).
2. Список локальных пользователей с shell и членов групп `docker` и `www-data` на сервере.
3. Не меняет ли `entrypoint.sh` права при старте контейнера: он делает `chown -R www-data` для `/var/www/html`, но `chmod` применяет только к `storage` и `bootstrap/cache`.
4. Нужна ли ротация секретов: были ли на сервере недоверенные локальные пользователи за время, пока файлы были открыты.

**Что сделать:**

1. `chmod 640 andrew-old.dib.in.ua/.env` (владелец `diba`, группа `docker`).
2. `chmod 640 andrew-old.dib.in.ua/source/.env` (владелец и группа `www-data`).
3. Проверить, что после перезапуска `web_ao` права `source/.env` сохраняются и сайт работает.
4. Для Moodle завести отдельную задачу в его проекте (`config.php` → `640`, `www-data:www-data`).
5. Если по п. 4 анализа ротация нужна — сменить `APP_KEY` (с учётом сброса сессий и зашифрованных данных), пароли MySQL и SMTP.

**Критерии приёмки:**

* `stat -c '%a %U:%G'` для обоих файлов показывает `640` и ожидаемых владельцев.
* Пользователь вне групп `docker`/`www-data` (например, `aiops` без ACL-исключения) не может прочитать файлы.
* Сайт и форма обратной связи работают; `docker compose config` в каталоге стека выполняется у владельца.

**Тесты:**

* Проверка `test -r` от имени непривилегированного пользователя до и после изменения.
* HEAD-запросы к сайту и проверка `laravel.log` на ошибки чтения `.env` после перезапуска контейнера.

**Файлы:** production: `/home/dialectik/docker-sites/andrew-old.dib.in.ua/.env`, `/home/dialectik/docker-sites/andrew-old.dib.in.ua/source/.env`.

---

### BL-003 · Закрыть доступ к резервным копиям и файлам MySQL
**Статус:** `todo` · **Приоритет:** `high`  
**Источник:** аудит доступа при настройке пользователя `aiops` (2026-11-02)

**Проблема:**
Каталоги `andrew-old.dib.in.ua/archive/` (`755`, `www-data`) и `andrew-old.dib.in.ua/database_ao/` (`755`, владелец — uid MySQL контейнера) доступны на чтение всем локальным пользователям. В `archive/` находятся полные дампы БД (`archive/database/*.sql.gz`, включая таблицу `users`) и архивы исходников с `.env`; в `database_ao/` часть файлов MySQL читается напрямую. Для `aiops` каталоги закрыты запрещающими ACL; права для остальных не менялись.

**Цель:**
Резервные копии и файлы данных MySQL доступны только владельцам процессов, которые их создают и используют, и администраторам.

**Что проанализировать перед реализацией:**

1. Кто пишет в `archive/`: контейнер `backup_db_ao` (root, volume `./archive/database`) и `backup_source.sh` (cron хоста, root). Кто читает (владелец при восстановлении, внешнее копирование бэкапов, если есть).
2. Владелец и права внутри `database_ao/`: MySQL в контейнере работает от собственного uid и требует доступа только для него.
3. Права создаваемых файлов: дампы и архивы создаются с umask `022` (`644`), поэтому ограничивать нужно каталоги.

**Что сделать:**

1. `chmod 750` для `andrew-old.dib.in.ua/archive/` и вложенных `archive/database/`, `archive/source/` (группа — администраторы, например `docker`).
2. `chmod 700` для `andrew-old.dib.in.ua/database_ao/` (владелец — uid MySQL контейнера).
3. Проверить, что очередной бэкап `backup_db_ao` (04:30) и `backup_source.sh` создаются успешно, а `db_ao` работает после изменения.
4. Аналогичную проверку выполнить для `moodle.dib.in.ua/archive/` и `database_md/` в проекте Moodle.

**Критерии приёмки:**

* Пользователь вне группы администраторов не может открыть `archive/` и `database_ao/`.
* Новый дамп появляется в `archive/database/` после 04:30; `db_ao` в статусе `Up`, сайт работает.

**Тесты:**

* `test -r`/`ls` от имени непривилегированного пользователя до и после изменения.
* `sudo aiops-docker logs backup_db_ao` и `sudo aiops-docker ps` после ближайшего запуска бэкапа.

**Файлы:** production: `/home/dialectik/docker-sites/andrew-old.dib.in.ua/archive/`, `/home/dialectik/docker-sites/andrew-old.dib.in.ua/database_ao/`.

---

## MEDIUM

### BL-004 · Перенести модели Voyager `Avmedia` и `Subtitle` из vendor в проект
**Статус:** `todo` · **Приоритет:** `medium`  
**Источник:** `docs/context/CONTEXT.md` §«Voyager и доработки в vendor»; сравнение локального и production `vendor/tcg/voyager/src/Models/` (2026-11-02)

**Проблема:**
BREAD-разделы `avmedia` и `subtitles` в `data_types.model_name` ссылаются на `TCG\Voyager\Models\Avmedia` и `TCG\Voyager\Models\Subtitle`. Эти классы созданы вручную в локальном `vendor/tcg/voyager/src/Models/`, который не хранится в git. На production их нет, поэтому разделы «Avmedia» и «Subtitles» админки там, вероятно, не открываются. Локальный `composer install`/`update` удаляет эти файлы. Кроме того, локально изменён `vendor/.../Models/Page.php` (`meta_description` в `$translatable`), а на production используется оригинальный файл, так что перевод `meta_description` в админке production не редактируется.

**Цель:**
Классы `TCG\Voyager\Models\Avmedia` и `TCG\Voyager\Models\Subtitle` хранятся и загружаются непосредственно из кода проекта, без файлов в `vendor/`. `composer install`/`update` на них не влияет, `data_types.model_name` не меняется, поведение одинаково локально и на production.

**Выбранный подход — автозагрузка из проекта (`classmap`):**

Классы с исходным namespace `TCG\Voyager\Models` размещаются в отдельном каталоге проекта вне `app/`, например `voyager/Models/`, и подключаются через `autoload.classmap` в `composer.json`:

```json
"autoload": {
    "psr-4": { "App\\": "app/", "...": "..." },
    "classmap": ["voyager/Models"]
}
```

Composer включает эти файлы в `vendor/composer/autoload_classmap.php` при каждом `install`, `update` и `dump-autoload`; в самом пакете Voyager таких классов нет, поэтому конфликта имён не возникает. Каталог должен быть вне `app/`: внутри него namespace файлов не совпал бы с PSR-4-правилом `App\`, и `dump-autoload -o` выдавал бы предупреждения о несоответствии PSR-4.

**Рассмотренные альтернативы:**

* **Перевести BREAD на `App\Models\Avmedia` / `App\Models\Subtitle`** (модели уже есть в проекте). Чище архитектурно, но требует изменения `data_types.model_name` в БД production (через настройки BREAD или миграцию) с разрешения владельца.
* **Хранить копии в проекте и восстанавливать их в `vendor/`** скриптом в `pre-autoload-dump`. Работает, но продолжает зависеть от правки каталога пакета; использовать, только если `classmap` окажется неприменим.

**Что проанализировать перед реализацией:**

1. Текущие `data_types.model_name` для `pages`, `avmedia`, `subtitles` на production и локально (SELECT через `dib-aiops-db`).
2. Фактическую ошибку на production при открытии `/admin/avmedia` и `/admin/subtitles` (лог `storage/logs/laravel.log` после запроса владельца).
3. Отсутствие предупреждений Composer: выполнить `composer dump-autoload -o` локально и убедиться, что нет сообщений `does not comply with psr-4` и `Ambiguous class resolution`.
4. Решение по `Page.php` (`meta_description` в `$translatable`). Переопределить `TCG\Voyager\Models\Page` через `classmap` нельзя: класс есть в пакете, и при оптимизированном автозагрузчике возникнет неоднозначность. Варианты: перевести BREAD `pages` на `App\Models\Page`, унаследованный от `TCG\Voyager\Models\Page` (с сохранением `save()`, который проставляет `author_id` — колонка `NOT NULL` без значения по умолчанию), либо оставить перевод `meta_description` только в публичной части (`App\Models\Page` уже содержит поле в `$translatable`).
5. Связь `page()` в обоих классах: сейчас `belongsTo(Page::class, 'id', 'page_id')`, ключи перепутаны (для `belongsTo` должно быть `page_id` → `id`). Проверить, используется ли связь в BREAD.
6. Порядок выката на production: после деплоя кода нужен `composer dump-autoload -o` в контейнере `web_ao` (`entrypoint.sh` запускает `composer install` только при отсутствии `vendor/`).

**Что сделать:**

1. Перенести `Avmedia.php` и `Subtitle.php` (namespace `TCG\Voyager\Models`) из локального `vendor/` в выбранный каталог проекта; добавить docblock к классам и методам, исправить ключи связи `page()`.
2. Добавить каталог в `autoload.classmap` в `composer.json` и убедиться в отсутствии предупреждений (см. п. 3 анализа).
3. Удалить ручные `Avmedia.php` и `Subtitle.php` из локального `vendor/`, выполнить `composer install` (или `composer dump-autoload -o`) и убедиться, что классы загружаются из проекта.
4. По результатам п. 4 анализа реализовать выбранное решение для `Page.php`; восстановить оригинальный `vendor/.../Models/Page.php` переустановкой пакета.
5. Production: после деплоя кода владелец (или агент с явного разрешения) выполняет `composer dump-autoload -o` в контейнере `web_ao`.
6. Обновить `docs/context/CONTEXT.md` (раздел «Voyager и доработки в vendor», «Запрещено без явного одобрения») и примечание в `README.md` (раздел 10.2).

**Критерии приёмки:**

* В `vendor/tcg/voyager/src/Models/` нет файлов проекта; `Avmedia` и `Subtitle` загружаются из каталога проекта (`(new ReflectionClass(...))->getFileName()`).
* `composer install` на чистой копии и `composer dump-autoload -o` выполняются без предупреждений PSR-4 и неоднозначности классов.
* Разделы «Avmedia» и «Subtitles» открываются, создают и редактируют записи локально и на production.
* Публичные страницы стихов и музыки и раздел «Pages» админки работают без изменений.

**Тесты:**

* Unit-тест: `class_exists()` для `TCG\Voyager\Models\Avmedia` и `TCG\Voyager\Models\Subtitle`, файл класса находится вне `vendor/`.
* Feature-тест на изолированной тестовой БД: связь `page()` возвращает страницу, к которой привязан медиафайл или субтитр.
* Ручная проверка разделов админки после деплоя. Рабочую и production-БД в тестах не использовать.

**Файлы:** `voyager/Models/Avmedia.php`, `voyager/Models/Subtitle.php`, `composer.json`, `tests/Unit/*`, `tests/Feature/*`, `docs/context/CONTEXT.md`, `README.md`; при выборе варианта для `Page` — `app/Models/Page.php`.
