<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

// Route::get('/main', function () {
//     return view('main');
// });

// Route::get('/{locale}', function ($locale) {
//     $availableLocales = [/* 'ru',  */'uk', 'en'];
//     if (in_array($locale, $availableLocales)) {
//         App::setLocale($locale);
//     }

//     return view('main');
// });

// Метод переключения языка
// Route::get('/lang/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

// // Оберните маршруты в группу с префиксом локали
// Route::group(['prefix' => '{locale}', 'where' => ['locale' => 'ru|en|uk']], function () {
//     Route::get('/main', function () {
//         return view('main');
//     });
// });


// Массив доступных локалей
$supportedLocales = ['ru', 'en', 'uk'];

// Обработчик для всех страниц без префикса "admin"
Route::get('/', function () {
    return redirect()->route('page', ['locale' => 'ru', 'page' => 'home']);
});

// Обобщенный маршрут для страниц с локалями
Route::get('/{locale}/{page}', function ($locale, $page) use ($supportedLocales) {
    // Убедитесь, что выбранная локаль допустима
    if (in_array($locale, $supportedLocales)) {
        session(['app_locale' => $locale]);
        app()->setLocale($locale);
    }

    // Проверяем, существует ли указанная страница
    if (view()->exists($page)) {
        return view($page);
    }

    abort(404); // Если представление не найдено, возвращаем 404
})->where('locale', 'ru|en|uk')->name('page');

// Группа маршрутов для префикса 'admin' (без локалей)
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

// Установите маршрут по умолчанию для русского языка
Route::get('/{page}', function ($page) {
    // Исключаем страницы с префиксом "admin" из обработки локалей
    if (!str_starts_with($page, 'admin')) {
        return redirect()->route('page', ['locale' => 'ru', 'page' => $page]);
    }

    // Если страница начинается с 'admin', пропускаем её
    // abort(404);
})->where('page', '.*');


