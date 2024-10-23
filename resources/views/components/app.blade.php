<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Andrew Old') }}</title>

        <!-- Fonts -->
        {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap"> --}}

        <link rel="icon" type="image/x-icon" href="{{ asset('./favicon.ico') }}">


        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('big-picture/assets/css/main.css') }}?<?php echo time(); ?>" />
        <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}?<?php echo time(); ?>" />
        <noscript><link rel="stylesheet" href="{{ asset('big-picture/assets/css/noscript.css') }}?<?php echo time(); ?>" /></noscript>

        <link rel="canonical" target="_blank" href="https://andrew-old.dib.in.ua" />
    </head>

    <body class="is-preload">

        <style>
            .language-dropdown {
                padding: 5px;
                font-size: 14px;
                background-color: #f8f9fa;
                border: 1px solid #ced4da;
                border-radius: 4px;
                cursor: pointer;
                font-family: "Source Sans Pro", "sans-serif"!important;
                font-weight: 300;
                font-size: 0.9em;
            }

            .language-dropdown option {
                padding: 5px;
                font-family: "Source Sans Pro", "sans-serif"!important;
                font-weight: 300;
            }

        </style>
        

        @include('components.partials.header')

        @include('components.partials.intro')

        @include('components.partials.one')

        @include('components.partials.two')

        @include('components.partials.work')


        {{-- @yield('hero') --}}

        <!-- Page Content -->
        <main>
            {{ $slot }}

            {{-- @livewire('navigation-menu') --}}
        </main>

        @include('components.partials.contact')

        @include('components.partials.footer')


        {{-- @livewireScripts --}}

        {{-- <script src="https://cdn.jsdelivr.net/npm/simple-parallax-js@5.6.1/dist/simpleParallax.min.js"></script> --}}

        <!-- Scripts -->
        <script src="{{ asset('big-picture/assets/js/jquery.min.js') }}?<?php echo time(); ?>"></script>
        <script src="{{ asset('big-picture/assets/js/jquery.poptrox.min.js') }}?<?php echo time(); ?>"></script>
        <script src="{{ asset('big-picture/assets/js/jquery.scrolly.min.js') }}?<?php echo time(); ?>"></script>
        <script src="{{ asset('big-picture/assets/js/jquery.scrollex.min.js') }}?<?php echo time(); ?>"></script>
        <script src="{{ asset('big-picture/assets/js/browser.min.js') }}?<?php echo time(); ?>"></script>
        <script src="{{ asset('big-picture/assets/js/breakpoints.min.js') }}?<?php echo time(); ?>"></script>
        <script src="{{ asset('big-picture/assets/js/util.js') }}?<?php echo time(); ?>"></script>
        <script src="{{ asset('big-picture/assets/js/main.js') }}?<?php echo time(); ?>"></script>

    </body>
</html>
