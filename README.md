# Персональный сайт Andrew Old (`andrew-old.dib.in.ua`)

<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300">
    </a>
    <h1 align="center">Персональный сайт-портфолио Andrew Old</h1>
    <br>
</p>

Сайт `andrew-old.dib.in.ua` — персональный мультиязычный сайт-портфолио автора: биография, «Путь», стихи, музыка (видео и аудио с субтитрами), друзья, семья, ссылки и форма обратной связи. Контент стихов и музыки хранится в БД и редактируется через админ-панель.

Решение построено на **Laravel 8** с админ-панелью **Voyager**. Публичная часть — Blade-шаблоны на основе HTML5 UP («Big Picture», «Future Imperfect»), три языка интерфейса и контента (ru / en / uk). На production сайт работает в Docker за reverse-proxy панели Hestia.

---

## 1. Назначение и ключевая функциональность

### Основные задачи сайта

* Публичная презентация автора:

  * главная страница-лендинг: интро, «О себе», «Путь», анонсы стихов и музыки, друзья, семья, ссылки;
  * страница «Путь» — пошаговое повествование (вступление, старт и десять шагов);
  * форма обратной связи (отправка письма автору через SMTP).

* Каталоги произведений:

  * «Стихи» и «Музыка» — список опубликованных страниц с AJAX-пагинацией и блоком случайных публикаций;
  * карточка произведения: текст, дата написания, видео (несколько разрешений), аудио, субтитры;
  * плеер Plyr 3.7.8 (CDN) для видео и аудио.

* Мультиязычность:

  * интерфейс и контент на русском, английском и украинском;
  * язык задаётся префиксом URL (`/ru/...`, `/en/...`, `/uk/...`) и запоминается в сессии;
  * переводы контента — средствами Voyager (таблица `translations`), переводы интерфейса — JSON-файлы `resources/lang/*.json`.

* Администрирование:

  * админ-панель Voyager по адресу `/admin`: страницы (стихи/музыка), медиафайлы, субтитры, меню, пользователи, роли, настройки;
  * загрузка медиафайлов в `storage/app/public` (публикуются через симлинк `public/storage`).

---

## 2. Архитектура и технологии

### 2.1. Технологический стек

* Основной язык программирования: **PHP 8** (`composer.json`: `^8.1`; локально OpenServer PHP 8.1; production-контейнер — `php:8.2-apache`).
* Фреймворк: **Laravel 8.83**.
* Админ-панель: **Voyager 1.7** (BREAD, мультиязычность, медиа-менеджер).
* База данных: **MySQL 8** (локально MySQL 8.0, production — MySQL 8.4 в Docker; 21 таблица).
* Web-сервер: **Apache 2** в контейнере, снаружи — nginx панели **Hestia** (reverse-proxy, TLS).
* Операционная система (production): **Ubuntu 24.04**.

Фронтенд:

* Blade-шаблоны, HTML5 UP «Big Picture» (главная, «Путь») и «Future Imperfect» (стихи, музыка) — статика в `public/big-picture` и `public/future-imperfect`;
* jQuery и плагины шаблонов (poptrox, scrolly, scrollex);
* Plyr 3.7.8 (CDN) — видео/аудиоплеер;
* Laravel Mix 6 (`resources/css/app.css`, `resources/js/app.js`).

Пакеты и библиотеки:

* `tcg/voyager` — админ-панель;
* `laravel-lang/common`, `laravel-lang/lang` — переводы системных сообщений Laravel;
* `symfony/mailer` — отправка писем формы обратной связи;
* установлены, но в коде сайта не используются: `laravel/jetstream`, `inertiajs/inertia-laravel`, `laravel/sanctum` (только `GET /api/user`), `darkaonline/l5-swagger` (аннотаций OpenAPI нет).

### 2.2. Структура проекта (логическая)

* `app/Http/Controllers/`:

  * `PoemController`, `MusicController` — список, AJAX-пагинация и карточка произведения;
  * `ContactController` — отправка формы обратной связи;
  * `LanguageController` — устаревшее переключение языка (маршрут отключён).
* `app/Http/Middleware/SetLocale.php` — установка языка из сессии (по умолчанию `ru`).
* `app/Models/` — `Page`, `Avmedia`, `Subtitle`, `Translation` (`transloc`), `User` (наследует `TCG\Voyager\Models\User`).
* `app/Console/Commands/` — `translations:find`, `translations:update` (сбор ключей перевода из шаблонов).
* `app/Services/DatabaseLoader.php` — загрузчик переводов из БД (сейчас отключён).
* `resources/views/` — шаблоны: `home/*`, `path/*`, `poems/*`, `music/*`, `layouts/*`, `emails/contact.blade.php`, `vendor/voyager/pages/edit-add.blade.php` (доработанная форма страницы в админке).
* `routes/web.php` — все публичные маршруты и маршруты Voyager.
* `database/migrations/` — миграции Laravel, Voyager и собственных таблиц.

Основные точки входа:

* Публичная часть: `public/index.php` → `https://andrew-old.dib.in.ua` (редирект на `/ru/home`).
* Админ-панель: `https://andrew-old.dib.in.ua/admin`.

---

## 3. Структура данных

Основная БД (production — `admin_andrew_old`, локально — `diba_andrew_old`), MySQL 8, InnoDB, 21 таблица.

Ключевые таблицы:

* `pages` — произведения: заголовок, текст (`body`), изображение, `slug`, статус (`ACTIVE`/`INACTIVE`), тип `source` (`POEM` — стихи, `MUSIC` — музыка), дата написания `date_writing`, `likes`. Колонки `source`, `likes`, `date_writing` добавлены вне миграций.
* `avmedia` — медиафайлы страницы: `type` (`VIDEO`/`AUDIO`), `file` (JSON Voyager со ссылкой на файл), `resolution` (для видео).
* `subtitles` — субтитры видео: `label`, `srclang`, `src` (JSON Voyager со ссылкой на `.vtt`).
* `translations` — переводы полей моделей Voyager (en/uk для `pages.title`, `slug`, `body`, `meta_description` и др.).
* `transloc` — переводы интерфейса в БД (используются только командой `translations:find`).
* `users`, `roles`, `permissions`, `permission_role`, `user_roles` — пользователи и права Voyager.
* `data_types`, `data_rows`, `menus`, `menu_items`, `settings` — служебные таблицы Voyager.
* `posts`, `categories` — демонстрационные таблицы Voyager, на сайте не выводятся.

---

## 4. Роли, права и доступы

### 4.1. Роли

* `admin` — полный доступ к админ-панели Voyager.
* `user` — стандартная роль Voyager без доступа к управлению контентом.

Публичная часть доступна без авторизации. Регистрация пользователей на сайте не предусмотрена.

### 4.2. Управление контентом

* Вход: `/admin/login`.
* Стихи и музыка — раздел «Pages»: тип задаётся полем `source`, публикация — статусом `ACTIVE`, переводы — переключателем языка в форме Voyager.
* Медиафайлы и субтитры — разделы «Avmedia» и «Subtitles»; в форме страницы выводится список привязанных файлов со ссылками на редактирование.

---

## 5. Мультиязычность

* Поддерживаемые языки: `ru` (по умолчанию и fallback), `en`, `uk`.
* Язык задаётся первым сегментом URL. Маршрут сохраняет его в сессии (`app_locale`) и вызывает `app()->setLocale()`; middleware `SetLocale` применяет язык из сессии к остальным запросам.
* Переключатель языка в шапке ведёт на ту же страницу с другим префиксом.
* Интерфейсные строки — `__('Русская фраза')`, переводы в `resources/lang/en.json` и `resources/lang/uk.json`.
* Контент страниц — `getTranslatedAttribute()` трейта `Translatable` Voyager. Адрес карточки всегда строится по базовому (русскому) `slug`.
* Даты выводятся через `Carbon::translatedFormat()` в текущей локали.

---

## 6. Основные разделы интерфейса

| Раздел | URL | Шаблон |
|---|---|---|
| Главная (интро, о себе, друзья, семья, ссылки) | `/{locale}/home` | `home.blade.php` + `home/*` |
| Путь | `/{locale}/path` | `path.blade.php` + `path/*` |
| Стихи | `/{locale}/poems` | `poems.blade.php` + `poems/*` |
| Стихотворение | `/{locale}/poems/{slug}` | `onepoem.blade.php` |
| Музыка | `/{locale}/music` | `music.blade.php` + `music/*` |
| Музыкальное произведение | `/{locale}/music/{slug}` | `onemusic.blade.php` |
| Админ-панель | `/admin` | Voyager |

---

## 7. Маршруты

Все маршруты описаны в `routes/web.php`:

* `GET /` — редирект на `/ru/home`.
* `GET /{locale}/poems`, `GET /{locale}/poems/{slug}` — стихи.
* `GET /{locale}/ajaxpoem/{page}` — AJAX-пагинация стихов (JSON: `pages`, `pagination`).
* `GET /{locale}/music`, `GET /{locale}/music/{slug}` — музыка.
* `GET /{locale}/ajaxmusic/{page}` — AJAX-пагинация музыки.
* `GET /{locale}/{page}` — обобщённый маршрут: выводит Blade-представление с именем `{page}`, если оно существует, иначе 404.
* `POST /contact/send` — форма обратной связи (`name`, `email`, `message`).
* `/admin/*` — маршруты Voyager.
* `GET /{page}` (любой путь без локали) — редирект на `/ru/{page}`; исключения — `admin*` и `.well-known/acme-challenge/*`.

REST API сайта отсутствует. В `routes/api.php` есть только стандартный `GET /api/user` (Sanctum).

---

## 8. Фоновые процессы, мониторинг и резервное копирование

### 8.1. Фоновые задачи

* Планировщик Laravel не используется: `app/Console/Kernel.php::schedule()` пуст, cron для `schedule:run` в контейнере закомментирован.
* Очереди не используются (`QUEUE_CONNECTION=sync`). Письмо формы обратной связи отправляется синхронно.

### 8.2. Резервное копирование (production)

* Контейнер `backup_db_ao` (Ubuntu 22.04 + cron) ежедневно в **04:30** (время контейнера) выполняет `mysql_backup.sh`: `mysqldump | gzip` в `archive/database/` и удаляет дампы старше 4 дней.
* `backup_source.sh` на хосте архивирует каталог `source` в `archive/source/` и удаляет архивы старше 2 дней. Расписание запуска (cron хоста) в репозитории не зафиксировано.

### 8.3. Логи

* Laravel: `storage/logs/laravel.log` (канал `stack`).
* Apache: `docker logs web_ao`.

---

## 9. Требования к окружению

### 9.1. Программное окружение

* PHP 8.1+ с расширениями:

  * `pdo_mysql`
  * `mbstring`
  * `intl`
  * `zip`
  * `gd`
  * `bcmath`
  * `exif`
  * `opcache`
* MySQL 8.x
* Apache 2.x (модули `mod_rewrite`, `headers`, `remoteip`)
* Composer 2
* Node.js + npm (сборка Laravel Mix — при изменении `resources/css` или `resources/js`)

### 9.2. Ресурсы production-сервера

Сервер `server.dib.in.ua` общий с другими проектами (Moodle, Open WebUI):

* Операционная система: **Ubuntu 24.04**
* Процессор: 4 ядра
* Память: 4 ГБ
* Диск: 80 ГБ

---

## 10. Установка и запуск (локально)

### 10.1. Клонирование репозитория

```bash
git clone https://github.com/Dialectik/andrew-old.loc.git
cd andrew-old.loc
```

### 10.2. Установка зависимостей

```bash
composer install
npm install
npm run dev
```

> Модели `TCG\Voyager\Models\Avmedia` и `TCG\Voyager\Models\Subtitle`, на которые ссылаются BREAD-разделы «Avmedia» и «Subtitles», сейчас лежат только в локальном `vendor/` (он не хранится в git). После чистого `composer install` их нет. Подробности — в `docs/context/CONTEXT.md`.

### 10.3. Конфигурация окружения

1. Скопируйте `.env.example` в `.env` и выполните `php artisan key:generate`.
2. Укажите подключение к БД (`DB_*`). Для OpenServer: `DB_HOST=MySQL-8.0`.
3. Укажите SMTP (`MAIL_*`) для формы обратной связи.
4. Создайте симлинк на медиафайлы: `php artisan storage:link`.
5. Не коммитьте `.env` и другие файлы с паролями и ключами.

На OpenServer используется `A:\OpenServer\modules\PHP-8.1\PHP\php.exe`.

### 10.4. Виртуальный хост

* Локально: `http://dib.andrew-old.loc` → `public/`.
* Production: `https://andrew-old.dib.in.ua` → nginx Hestia → `127.0.0.6:8086` → Apache в контейнере `web_ao` (`DocumentRoot /var/www/html/public`, `AllowOverride All`).

---

## 11. Production-развёртывание (Docker)

### 11.1. Расположение

```
/home/dialectik/docker-sites/andrew-old.dib.in.ua/
├── docker-compose.yml, .env       # конфигурация стека
├── Dockerfile                     # php:8.2-apache + расширения + composer
├── Dockerfile.backupdb, mysql_backup.sh
├── entrypoint.sh, php-overrides.ini
├── backup_source.sh
├── archive/                       # резервные копии БД и исходников
├── database_ao/                   # данные MySQL (volume)
└── source/                        # код сайта (git-клон этого репозитория)
```

### 11.2. Контейнеры

| Контейнер | Назначение | Порт на хосте |
|---|---|---|
| `web_ao` | Apache + PHP 8.2, код из `source/` | `127.0.0.6:8086` |
| `db_ao` | MySQL 8.4 | `127.0.0.6:3308` |
| `pma_ao` | phpMyAdmin | см. `docker-compose.yml` |
| `backup_db_ao` | ежедневный дамп БД | — |

### 11.3. Особенности запуска

* `entrypoint.sh` при каждом старте выполняет `chown -R www-data` для кода, создаёт `storage` и `bootstrap/cache` и при отсутствии `vendor/` запускает `composer install`, а при отсутствии `public/storage` — `php artisan storage:link`.
* `php-overrides.ini`: `upload_max_filesize`/`post_max_size` = 64M, `memory_limit` = 512M, `max_execution_time` = 300, `date.timezone = Europe/Kiev`.
* `TrustProxies` доверяет всем прокси (`*`), чтобы Laravel корректно обрабатывал `X-Forwarded-*` от Hestia.

Порядок подключения к серверу и правила безопасности — в `docs/context/Подключение к production-серверу.local.md` (локальный файл, не хранится в git).

---

## 12. Тесты

В проекте есть только стандартные примеры Laravel (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`), собственных тестов пока нет.

```bash
php artisan test
# или
vendor/bin/phpunit
```

Тесты нельзя запускать на production и против рабочей БД.
