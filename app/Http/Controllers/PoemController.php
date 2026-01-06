<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Page;

class PoemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locale = app()->getLocale();
        
        // Устанавливаем локаль для Carbon
        Carbon::setLocale($locale);
        
        // Получаем страницы для mini_posts и posts_list
        $listPages = Page::published()->poems()->inRandomOrder()->take(9)->get();

        // Форматирование даты для списков страниц
        $listPages->each(function ($page) use ($locale) {
            $page->datetime = Carbon::parse($page->date_writing)->format('Y-m-d'); // Для атрибута datetime
            $page->formatted_date = Carbon::parse($page->date_writing)->translatedFormat('F j, Y'); // Читаемый формат
        });

        // Получаем все страницы, отсортированные по дате написания (с пагинацией)
        $allPages = Page::published()->poems()->latest('date_writing')->paginate(3);

        // Форматирование даты для всех страниц
        $allPages->each(function ($page) use ($locale) {
            $page->datetime = Carbon::parse($page->date_writing)->format('Y-m-d'); // Для атрибута datetime
            $page->formatted_date = Carbon::parse($page->date_writing)->translatedFormat('F j, Y'); // Читаемый формат
        });

        return view('poems', compact('listPages', 'allPages'));
    }

    /**
     * Обновление перечня страниц при пагинации
     */
    public function getPagesAjax($locale, $page)
    {
        // $locale = app()->getLocale();
        
        // Устанавливаем локаль для Carbon
        Carbon::setLocale($locale);
        
        // Получаем все посты, отсортированные по дате обновления
        $allPages = Page::published()->poems()->latest('date_writing')->paginate(3, ['*'], 'page', $page);

        // Форматирование даты для всех страниц
        $allPages->each(function ($page) use ($locale) {
            $page->datetime = Carbon::parse($page->date_writing)->format('Y-m-d'); // Для атрибута datetime
            $page->formatted_date = Carbon::parse($page->date_writing)->translatedFormat('F j, Y'); // Читаемый формат
        });

        $paginationHtml = $allPages->onEachSide(0)->links('vendor.pagination.custom')->toHtml();

        return response()->json(['pages' => view('poems.pages_ajax', compact('allPages'))->render(), 'pagination' => $paginationHtml]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($locale, $slug)
    {
        // Устанавливаем локаль для Carbon
        Carbon::setLocale($locale);
        
        // Получаем страницу
        $page = Page::where('slug', $slug)->published()->poems()->firstOrFail();

        // Форматирование даты для страницы
        $page->datetime = Carbon::parse($page->date_writing)->format('Y-m-d'); // Для атрибута datetime
        $page->formatted_date = Carbon::parse($page->date_writing)->translatedFormat('F j, Y'); // Читаемый формат

        // Получаем все связанные медиа-файлы для текущей страницы
        $audioItems = \App\Models\Avmedia::where('page_id', $page->id)->audio()->get();
        $videoItems = \App\Models\Avmedia::where('page_id', $page->id)->video()->get();
        // Получаем все субтитры
        $subtitleItems = \App\Models\Subtitle::where('page_id', $page->id)->get();

        // dd($page->body);

        return view('onepoem', compact('page', 'audioItems', 'videoItems', 'subtitleItems'));
    }
}
