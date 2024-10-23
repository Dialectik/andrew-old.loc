<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    // public function switchLanguage($locale)
    // {
    //     // Проверяем, поддерживается ли выбранный язык
    //     if (in_array($locale, ['ru', 'en', 'uk', ])) {
    //         // Сохраняем выбранный язык в сессии
    //         Session::put('app_locale', $locale);
    //     }

    //     return redirect()->back(); // Возвращаемся на предыдущую страницу
    // }

    public function switchLanguage($locale)
    {
        // Проверяем, поддерживается ли выбранный язык
        if (in_array($locale, ['en', 'uk'])) {
            Session::put('app_locale', $locale);
            
            // Получаем текущий путь без локали
            $currentPath = request()->path();

            // Перенаправляем на ту же страницу с новой локалью
            return redirect()->to('/' . $locale . '/');
        }

        // Если выбран русский, перенаправляем на корень
        return redirect()->to('/');
    }

    // public function switchLanguage($locale)
    // {
    //     // Проверяем, поддерживается ли выбранный язык
    //     if (in_array($locale, ['ru', 'en', 'uk'])) {
    //         Session::put('app_locale', $locale);
            
    //         // Перенаправляем на ту же страницу с новой локалью
    //         return redirect()->back();
    //     }

    //     // Если выбран русский, перенаправляем на корень
    //     return redirect()->to('/');
    // }
}
