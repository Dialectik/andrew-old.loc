## Контекст проекта: персональный сайт Andrew Old (Laravel + Voyager)

Сайт `andrew-old.dib.in.ua` — персональный мультиязычный сайт-портфолио: биография, «Путь», стихи, музыка (видео/аудио с субтитрами), друзья, семья, ссылки, форма обратной связи. Контент стихов и музыки редактируется в админ-панели Voyager.

Стек: **Laravel 8.83**, **Voyager 1.7**, PHP 8.2 на production (контейнер `php:8.2-apache`; локально OpenServer PHP 8.1; `composer.json` — `php ^8.1`), MySQL 8 (production 8.4, локально 8.0), Blade + HTML5 UP («Big Picture», «Future Imperfect»), jQuery, Plyr 3.7.8, Apache 2 в Docker за nginx Hestia.

---

## Что читать перед правками

| Тип правки | Читать |
|---|---|
| Маршруты, локали, редиректы | `routes/web.php` целиком + секцию «Маршрутизация и мультиязычность» |
| Стихи / музыка (списки, карточки, пагинация) | `PoemController.php`, `MusicController.php`, `resources/views/{poems,music}/*` |
| Видео / аудио / субтитры | `resources/views/{poems,music}/onemain.blade.php` + секцию «Медиафайлы и субтитры» |
| Админка Voyager (BREAD, форма страницы) | секцию «Voyager и доработки в vendor» + `resources/views/vendor/voyager/pages/edit-add.blade.php` |
| Переводы интерфейса | `resources/lang/{en,uk}.json`, команды `translations:find` / `translations:update` |
| Форма обратной связи | `ContactController.php`, `resources/views/home/links.blade.php`, `emails/contact.blade.php` |
| Схема БД | секцию «Структура данных» — часть схемы создана вне миграций |
| Docker / production | раздел 11 в `README.md` + `docs/context/Подключение к production-серверу.local.md` |

---

## Общая архитектура

Монолит Laravel 8 без API и фоновых процессов:

* **Публичная часть** — Blade-шаблоны, рендер на сервере:
  * главная (`home`) и «Путь» (`path`) — статичные секции в шаблонах, тексты через `__()`;
  * «Стихи» и «Музыка» — динамические страницы из таблицы `pages`.

* **Админ-панель** — Voyager (`/admin`): BREAD для `pages`, `avmedia`, `subtitles`, `users`, `roles`, `menus`, `posts`, `categories`, настройки, медиа-менеджер.

* **Console** — две служебные команды для ключей перевода; планировщик не используется.

* **Хранилище файлов** — `storage/app/public` (диск `public`), доступ через симлинк `public/storage`.

---

## Основное назначение системы

1. Публикация биографии и сопутствующих разделов (статичный контент в шаблонах).
2. Публикация стихов и музыки с медиафайлами (контент в БД).
3. Мультиязычный вывод (ru / en / uk).
4. Приём сообщений посетителей через форму обратной связи.

---

## Ключевая бизнес-логика

### 1. Маршрутизация и мультиязычность

* Поддерживаемые локали: `ru` (по умолчанию и `fallback_locale`), `en`, `uk`. Список захардкожен в `routes/web.php` (`$supportedLocales` и `where('locale', 'ru|en|uk')`), в `config/voyager.php` (`multilingual.locales`) и в переключателе языка шапки.
* Маршруты стихов и музыки — замыкания: каждое сохраняет локаль в сессии (`app_locale`), вызывает `app()->setLocale()` и затем метод контроллера через `app(PoemController::class)`.
* Middleware `SetLocale` (группа `web`) применяет `session('app_locale')`, иначе ставит `ru`. Middleware выполняется до замыкания маршрута, поэтому язык из URL перекрывает язык из сессии.
* Обобщённый маршрут `/{locale}/{page}` рендерит любое существующее Blade-представление с именем `{page}`, иначе отдаёт 404. Имя не проверяется по белому списку: в него может попасть имя частичного шаблона с точкой (например, `home.intro`).
* Последний маршрут `/{page}` с `where('page', '.*')` перенаправляет любой путь без локали на `/ru/{page}`; пути `admin*` и `.well-known/acme-challenge/*` исключены (замыкание ничего не возвращает). Порядок маршрутов важен: Voyager (`/admin`) регистрируется раньше этого маршрута. Коммит `e8d0168` исправил бесконечные редиректы именно здесь.
* `GET /` → редирект на `/ru/home`.
* `LanguageController::switchLanguage()` не используется (маршрут закомментирован).

### 2. Стихи и музыка

* Одна модель `App\Models\Page`; тип определяется полем `source`: `POEM` — стихи, `MUSIC` — музыка (scopes `poems()` и `music()`). Публикация — `status = 'ACTIVE'` (`published()`).
* Список (`index()`): 9 случайных опубликованных страниц (блок mini-posts) и основной список с пагинацией по 3, сортировка `date_writing DESC`.
* AJAX-пагинация (`getPagesAjax($locale, $page)`): JSON `{pages: html, pagination: html}`; пагинатор — `vendor.pagination.custom`, `onEachSide(0)`.
* Карточка (`show($locale, $slug)`): поиск по базовой колонке `pages.slug` (русский slug) + `published()` + тип; иначе 404. Ссылки в шаблонах строятся по `$page->slug`, переводы slug из `translations` в маршрутизации не участвуют.
* Даты: `datetime` (`Y-m-d`) и `formatted_date` (`translatedFormat('F j, Y')`) вычисляются в контроллере через `Carbon` в текущей локали.
* Логика `PoemController` и `MusicController` продублирована; различаются только scope и имена шаблонов. Правки нужно вносить в оба контроллера.

### 3. Медиафайлы и субтитры

* `avmedia.type` — `VIDEO` или `AUDIO` (scopes `video()` и `audio()`). `avmedia.file` и `subtitles.src` хранят JSON поля «file» Voyager (`[{download_link, original_name}]`). Шаблон декодирует JSON и строит URL `asset('storage/' . download_link)`.
* Несколько записей `VIDEO` одной страницы — источники разного разрешения (`resolution` → атрибут `size` у `<source>`); постер — изображение страницы.
* Субтитры выводятся как `<track>` (`label`, `srclang`).
* Плеер — Plyr 3.7.8 с CDN `cdn.plyr.io` (секции `styles_plyr` / `javascript_plyr` в `layouts/app-poems.blade.php`).
* Production: 17 записей `VIDEO`, аудио и субтитров нет (на 2026-11-02).

### 4. Форма обратной связи

* `POST /contact/send` (`ContactController::send`), валидация: `name` required|max:255, `email` required|email, `message` required.
* Письмо по шаблону `emails.contact` отправляется синхронно (`QUEUE_CONNECTION=sync`) на адрес, захардкоженный в контроллере. Тема — «Новое сообщение с сайта Andrew Old».
* `from` письма — адрес и имя посетителя. SMTP-провайдер (например, Gmail) может переписать отправителя или отклонить письмо (SPF/DMARC).
* Рабочая форма — в `home/links.blade.php`. Форма в `layouts/partials/contact.blade.php` отправляется на `#` и не работает.
* Защиты от спама (капча, rate limit) нет, только CSRF.

### 5. Переводы интерфейса

* Строки в шаблонах — `__('Русская фраза')`. Ключ — русский текст, переводы — `resources/lang/en.json` и `uk.json`; для `ru` перевод не нужен.
* `php artisan translations:update` дописывает новые ключи из шаблонов в `en.json`/`uk.json` с пустыми значениями. Шаблон поиска — `__('...')` только в одинарных кавычках, вызовы `<?php echo __('...') ?>` тоже находит.
* `php artisan translations:find` записывает ключи в таблицу `transloc`. `App\Services\DatabaseLoader` (подмешивание переводов из `transloc`) отключён в `AppServiceProvider::register()`. При включении он пишет в лог каждый вызов `load()`.

---

## Структура данных (ключевые сущности)

* `pages` — произведения: `title`, `excerpt`, `body`, `image`, `slug` (unique), `meta_description`, `meta_keywords`, `status` enum(`ACTIVE`,`INACTIVE`), `source` varchar(50) default `POEM`, `likes` int null, `date_writing` timestamp null, `author_id`.
* `avmedia` — `page_id` (FK → `pages.id`, cascade), `type`, `file` (JSON), `resolution`.
* `subtitles` — `page_id` (FK → `pages.id`, cascade), `label`, `srclang`, `src` (JSON).
* `translations` — переводы Voyager (`table_name`, `column_name`, `foreign_key`, `locale`, `value`).
* `transloc` — `locale`, `key`, `value` (переводы интерфейса в БД, сейчас не используются при рендере).
* `users`, `roles` (`admin`, `user`), `permissions`, `permission_role`, `user_roles`.
* Служебные таблицы Voyager: `data_types`, `data_rows`, `menus`, `menu_items`, `settings`.
* `posts`, `categories` — демо-данные Voyager, на сайте не используются.

Переводимые поля `App\Models\Page`: `title`, `slug`, `meta_description`, `source` (`$translatable`). Шаблоны получают переводы через `getTranslatedAttribute()`.

### Расхождения схемы и миграций

* Колонки `pages.source`, `pages.likes`, `pages.date_writing` отсутствуют в миграциях: они добавлены через инструменты БД Voyager или вручную. `php artisan migrate` на пустой БД не воспроизводит рабочую схему.
* В таблице `migrations` production есть запись `2025_01_05_124605_create_media_table`, файла с таким именем в репозитории нет.
* Миграция `create_pages_table` использует `TCG\Voyager\Models\Page::$statuses`, то есть зависит от пакета Voyager.

---

## Voyager и доработки в vendor

* BREAD `avmedia` и `subtitles` в `data_types.model_name` ссылаются на `TCG\Voyager\Models\Avmedia` и `TCG\Voyager\Models\Subtitle`. Эти классы созданы вручную в локальном `vendor/tcg/voyager/src/Models/`. Там же изменена `vendor/.../Models/Page.php`: в `$translatable` добавлен `meta_description`.
* `vendor/` исключён из git (`.gitignore`). На production `vendor/` установлен через composer, и этих файлов там **нет** (проверено 2026-11-02). Разделы «Avmedia» и «Subtitles» админки production, вероятно, не открываются (класс не найден); переводы `meta_description` в админке production не редактируются.
* Публичная часть сайта использует собственные модели `App\Models\Avmedia`, `App\Models\Subtitle`, `App\Models\Page` и от этих файлов в `vendor` не зависит.
* Планируемое исправление — перенести `Avmedia` и `Subtitle` (с namespace `TCG\Voyager\Models`) в каталог проекта и подключить через `autoload.classmap`, не меняя `data_types` (задача BL-004 в `docs/context/BACKLOG.md`). До исправления `composer install` или `composer update` локально удалят доработки.
* Форма редактирования страницы переопределена в `resources/views/vendor/voyager/pages/edit-add.blade.php`: выводит привязанные медиафайлы и субтитры со ссылками на их редактирование.
* `App\Models\User` наследует `TCG\Voyager\Models\User`.

---

## Фоновые процессы

* Планировщик и очереди не используются: `schedule()` пуст, cron `schedule:run` в Dockerfile закомментирован, `QUEUE_CONNECTION=sync`.
* Резервное копирование на production вынесено в инфраструктуру:
  * контейнер `backup_db_ao`: `mysqldump | gzip` ежедневно в 04:30 в `archive/database/`, хранение 4 дня;
  * `backup_source.sh` (хост): tar-архив `source/` в `archive/source/`, хранение 2 дня. Расписание задаётся cron хоста и в репозитории не описано.

---

## Production-окружение

* Сервер `server.dib.in.ua` (84.32.57.95), Ubuntu 24.04, панель Hestia; общий с проектами Moodle и Open WebUI.
* Каталог: `/home/dialectik/docker-sites/andrew-old.dib.in.ua`, код — `source/` (git-клон `github.com/Dialectik/andrew-old.loc`; на 2026-11-02 HEAD `e8d0168` совпадает с `main`).
* Контейнеры: `web_ao` (Apache + PHP 8.2, `127.0.0.6:8086`), `db_ao` (MySQL 8.4, `127.0.0.6:3308`, БД `admin_andrew_old`), `pma_ao` (phpMyAdmin), `backup_db_ao`.
* Цепочка запроса: nginx Hestia (TLS) → `127.0.0.6:8086` → Apache (`DocumentRoot /var/www/html/public`). `TrustProxies::$proxies = '*'`.
* `entrypoint.sh` при старте контейнера делает `chown -R www-data` для кода, `chmod -R 775` для `storage` и `bootstrap/cache`, `composer install` (если нет `vendor/`) и `storage:link` (если нет симлинка).
* PHP: `upload_max_filesize`/`post_max_size` 64M, `memory_limit` 512M, `date.timezone = Europe/Kiev` (в `config/app.php` — `UTC`).
* Медиафайлы на production: `storage/app/public` ≈ 157 МБ (`avmedia`, `pages`, `posts`, `settings`, `subtitles`, `users`).
* Доступ агента и правила работы — `docs/context/Подключение к production-серверу.local.md`.

---

## Бизнес-правила

* **Тип произведения** определяется только `pages.source` (`POEM` / `MUSIC`); новые типы требуют scope в модели, маршрутов, контроллера и шаблонов.
* **Публикация** — только `status = 'ACTIVE'`; неопубликованные страницы не выводятся ни в списках, ни по прямой ссылке.
* **URL карточки** строится по базовому (русскому) `slug` во всех языках; переводы slug на маршрутизацию не влияют.
* **Язык по умолчанию** — `ru`; корень сайта и пути без локали ведут на `/ru/...`.
* **Тексты интерфейса** пишутся на русском в `__()`; переводы добавляются в `en.json` и `uk.json`.

---

## Критические инварианты

* Порядок маршрутов в `routes/web.php`: Voyager `admin` раньше catch-all `/{page}`; catch-all всегда последний. Нарушение ломает админку или даёт бесконечные редиректы.
* Список локалей синхронизирован в `routes/web.php`, `config/voyager.php` и шапке (`layouts/partials/header.blade.php`).
* Поля `avmedia.file` и `subtitles.src` — JSON-формат поля «file» Voyager; шаблоны рассчитывают на ключи `download_link` и `original_name`.
* Медиафайлы доступны только через симлинк `public/storage` → `storage/app/public`.
* `vendor/` не хранится в git; доработки пакетов в `vendor/` не попадают на production.

---

## Запрещено без явного одобрения

* Деструктивный SQL (`DELETE`/`TRUNCATE`/`DROP`, `migrate:fresh`, `migrate:refresh`) на рабочей БД.
* `php artisan migrate` на production без проверки: миграции не совпадают с рабочей схемой.
* `composer install`/`composer update` локально без сохранения доработок `vendor/tcg/voyager/src/Models` (см. «Voyager и доработки в vendor»).
* Изменение структуры URL (`/{locale}/...`, slug) — сломает внешние ссылки и индексацию.
* Изменение формата JSON в `avmedia.file` / `subtitles.src`.
* Удаление или переименование таблиц и колонок Voyager.

---

## Разрешено

* Аддитивные миграции (`createTable`, `addColumn`, `createIndex`), в том числе миграции, фиксирующие уже существующие колонки (с проверкой `Schema::hasColumn`).
* Новые Blade-секции и разделы с переводами через `__()`.
* Новые BREAD-разделы Voyager с моделями в `App\Models`.
* Рефакторинг дублирующейся логики `PoemController`/`MusicController` без изменения URL и вывода.
* Покрытие тестами и добавление runbook-ов.

---

## Правила выполнения задач

* Писать чистый код с соблюдением стиля проекта (Laravel 8 conventions, PSR-12).
* **Перед каждым новым методом или функцией** — краткий docblock с описанием цели, параметров и возвращаемого значения.
* После модификации файлов проверять `README.md` на необходимость обновления.
* После изменения архитектуры, функциональности, данных, маршрутов или эксплуатационного поведения — обновлять этот файл.
* Отмечать изменения в `docs/context/CHANGELOG.md`, если файл существует (структура — по образцу, предоставленному пользователем).
* В финальном ответе предлагать название коммита для внесённых изменений.
* Сохранять кодировку файлов (UTF-8): шаблоны и `lang/*.json` содержат кириллицу.
* PHP для локальных команд: `A:\OpenServer\modules\PHP-8.1\PHP\php.exe`; MySQL: `A:\OpenServer\modules\MySQL-8.0\bin\mysql.exe` (параметры — из `.env`).
* Для нового поведения добавлять Feature- или Unit-тесты (PHPUnit 9, `tests/Feature`, `tests/Unit`). Тесты не используют рабочую и dev-БД: только отдельную тестовую БД или SQLite in-memory; внешние HTTP-запросы — через `Http::fake()`, письма — через `Mail::fake()`.

---

## Особенности проекта (важно для доработок)

* сайт **уже в production**, небольшой трафик, один администратор;
* контент стихов и музыки меняется через админку, статичные разделы — только правкой шаблонов;
* часть схемы БД и моделей Voyager существует вне репозитория (см. выше);
* в шаблонах подключение CSS/JS идёт с `?<?php echo time(); ?>` — браузерный кэш статики фактически отключён;
* в `routes/web.php` и контроллерах много закомментированного кода прежних вариантов локализации.

---

## Типовые задачи доработок

* добавление и редактирование разделов главной и «Пути»;
* новые типы контента или поля страниц;
* доработка медиаплеера, субтитров, разрешений видео;
* перевод интерфейса и контента;
* исправление админки Voyager (BREAD, модели, формы);
* SEO: мета-теги, canonical, hreflang, sitemap;
* Docker/деплой и резервное копирование.

---

## Ограничения и риски

* обобщённый маршрут `/{locale}/{page}` рендерит любое существующее представление по имени — нужен белый список страниц;
* `composer install` локально удаляет ручные модели Voyager из `vendor/`, а на production они уже отсутствуют;
* миграции не воспроизводят рабочую схему `pages`;
* форма обратной связи без капчи и лимита запросов; письмо отправляется от имени адреса посетителя;
* зависимость от CDN `cdn.plyr.io`;
* Laravel 8 и PHP 8.1 вне поддержки (security fixes прекращены);
* в зависимостях есть неиспользуемые пакеты (Jetstream, Inertia, Sanctum, L5-Swagger);
* важно не ломать:
  * порядок маршрутов и редиректы локалей;
  * адреса карточек (`/{locale}/poems|music/{slug}`);
  * формат JSON медиаполей Voyager;
  * симлинк `public/storage`.

---

## Что важно учитывать при постановке задач

* где меняется контент: шаблон (статичные разделы) или БД через Voyager (стихи, музыка, медиа);
* какие языки затрагиваются и где добавить переводы (`lang/*.json` или `translations`);
* влияние на URL и порядок маршрутов;
* нужна ли миграция и совпадает ли она с рабочей схемой production;
* зависит ли изменение от кода в `vendor/`.
