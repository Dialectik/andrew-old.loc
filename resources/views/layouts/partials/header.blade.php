@php
    // Получаем текущий путь без первого сегмента (локали)
    $currentPath = implode('/', array_slice(explode('/', Request::path()), 1));
@endphp 

<header id="header">
    <h1>
        <a href="/<?= app()->getLocale() ?>/home" style="font-size: 1em;">{{ config('app.name', 'Andrew Old') }}</a>
    </h1>
    <nav>
        <ul>
            <li>
                <div class="language-switcher">
                    <select onchange="location = this.value;" class="language-dropdown">
                        <!-- Основной выбранный язык -->
                        <option value="{{ route('page', ['locale' => app()->getLocale(), 'page' => $currentPath]) }}" selected>
                            @if(app()->getLocale() === 'ru') RU
                            @elseif(app()->getLocale() === 'en') EN
                            @elseif(app()->getLocale() === 'uk') UA
                            @endif
                        </option>
                
                        <!-- Остальные языки -->
                        @if(app()->getLocale() !== 'ru')
                            <option value="{{ route('page', ['locale' => 'ru', 'page' => $currentPath]) }}">RU</option>
                        @endif
                        @if(app()->getLocale() !== 'en')
                            <option value="{{ route('page', ['locale' => 'en', 'page' => $currentPath]) }}">EN</option>
                        @endif
                        @if(app()->getLocale() !== 'uk')
                            <option value="{{ route('page', ['locale' => 'uk', 'page' => $currentPath]) }}">UA</option>
                        @endif
                    </select>
                </div>
            </li>
            <li><a href="/<?= app()->getLocale() ?>/home" class="{{ request()->is(app()->getLocale() . '/home') ? 'active' : '' }}"><?php echo __('Интро'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/home#about_me" class="{{ request()->is(app()->getLocale() . '/home#about_me') ? 'active' : '' }}"><?php echo __('О себе'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/path" class="{{ request()->is(app()->getLocale() . '/path') ? 'active' : '' }}"><?php echo __('Путь'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/poems" class="{{ request()->is(app()->getLocale() . '/poems*') ? 'active' : '' }}"><?php echo __('Стихи'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/music" class="{{ request()->is(app()->getLocale() . '/music*') ? 'active' : '' }}"><?php echo __('Музыка'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/home#friends" class="{{ request()->is(app()->getLocale() . '/home#friends') ? 'active' : '' }}"><?php echo __('Друзья'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/home#family" class="{{ request()->is(app()->getLocale() . '/home#family') ? 'active' : '' }}"><?php echo __('Семья'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/home#links" class="{{ request()->is(app()->getLocale() . '/home#links') ? 'active' : '' }}"><?php echo __('Ссылки'); ?></a></li>
        </ul>
    </nav>

    <!-- Иконка "гамбургер" -->
    <div id="hamburger-icon" onclick="toggleMenu()">☰</div>
    <nav id="menu-popup">
        <div class="language-switcher">
                
            <select onchange="location = this.value;" class="language-dropdown">
                <!-- Основной выбранный язык -->
                <option value="{{ route('page', ['locale' => app()->getLocale(), 'page' => $currentPath]) }}" selected>
                    @if(app()->getLocale() === 'ru') RU
                    @elseif(app()->getLocale() === 'en') EN
                    @elseif(app()->getLocale() === 'uk') UA
                    @endif
                </option>
        
                <!-- Остальные языки -->
                @if(app()->getLocale() !== 'ru')
                    <option value="{{ route('page', ['locale' => 'ru', 'page' => $currentPath]) }}">RU</option>
                @endif
                @if(app()->getLocale() !== 'en')
                    <option value="{{ route('page', ['locale' => 'en', 'page' => $currentPath]) }}">EN</option>
                @endif
                @if(app()->getLocale() !== 'uk')
                    <option value="{{ route('page', ['locale' => 'uk', 'page' => $currentPath]) }}">UA</option>
                @endif
            </select>
        </div>
        <!-- Левая стрелка -->
        <div id="left-arrow" onclick="scrollMenu(-1)">&#10094;</div>
        <ul id="menu-items">
            <li>&nbsp;</li>
            <li><a href="/<?= app()->getLocale() ?>/home" class="{{ request()->is(app()->getLocale() . '/home') ? 'active' : '' }}"><?php echo __('Интро'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/home#about_me" class="{{ request()->is(app()->getLocale() . '/home#about_me') ? 'active' : '' }}"><?php echo __('О себе'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/path" class="{{ request()->is(app()->getLocale() . '/path') ? 'active' : '' }}"><?php echo __('Путь'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/poems" class="{{ request()->is(app()->getLocale() . '/poems*') ? 'active' : '' }}"><?php echo __('Стихи'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/music" class="{{ request()->is(app()->getLocale() . '/music*') ? 'active' : '' }}"><?php echo __('Музыка'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/home#friends" class="{{ request()->is(app()->getLocale() . '/home#friends') ? 'active' : '' }}"><?php echo __('Друзья'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/home#family" class="{{ request()->is(app()->getLocale() . '/home#family') ? 'active' : '' }}"><?php echo __('Семья'); ?></a></li>
            <li><a href="/<?= app()->getLocale() ?>/home#links" class="{{ request()->is(app()->getLocale() . '/home#links') ? 'active' : '' }}"><?php echo __('Ссылки'); ?></a></li>
            <li>&nbsp;</li>
        </ul>
        <!-- Правая стрелка -->
        <div id="right-arrow" onclick="scrollMenu(1)">&#10095;</div>
    </nav>
</header>

<script>
    function toggleMenu() {
        var menu = document.getElementById("menu-popup");
        if (menu.style.display === "flex") {
            menu.style.display = "none";
        } else {
            menu.style.display = "flex";
        }
    }

    function scrollMenu(direction) {
        var menuItems = document.getElementById("menu-items");
        var scrollAmount = 100; // на сколько пикселей прокрутить

        menuItems.scrollBy({
            left: direction * scrollAmount,
            behavior: "smooth"
        });
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var currentHash = window.location.hash;
        console.log(currentHash);
        
        if (currentHash) {
            var link = document.querySelector('a[href$="' + currentHash + '"]');
            if (link) {
                link.classList.add("active");
            }
        }
    });

    document.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            document.querySelectorAll('a').forEach(function (el) {
                el.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        var currentHash = window.location.hash;
        console.log(currentHash);
        
        if (currentHash) {
            var link = document.querySelector('#menu-items a[href$="' + currentHash + '"]');
            if (link) {
                link.classList.add("active");
            }
        }
    });

    document.querySelectorAll('#menu-items a').forEach(function (link) {
        link.addEventListener('click', function () {
            document.querySelectorAll('a').forEach(function (el) {
                el.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

</script>

<style>
    a.active {
        background-color: #b5aeae;
        color: #ffffff!important;
        border-radius: 4px;
        text-decoration: none;
    }
</style>
