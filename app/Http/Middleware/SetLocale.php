<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Получаем язык из сессии или устанавливаем язык по умолчанию
        // $locale = Session::get('app_locale', config('app.locale'));
        // App::setLocale($locale);

        if (session()->has('app_locale')) {
            App::setLocale(session('app_locale'));
        } else {
            App::setLocale('ru'); // Установите русский язык по умолчанию
        }

        return $next($request);
    }
}
