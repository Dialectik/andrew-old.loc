<header id="header">
    <h1>{{ config('app.name', 'Andrew Old') }}</h1>
    <nav>
        <ul>
            <li>
                <div class="language-switcher">
                    <select onchange="location = this.value;" class="language-dropdown">
                        <!-- Основной выбранный язык -->
                        <option value="{{ route('page', ['locale' => app()->getLocale(), 'page' => Request::segment(2)]) }}" selected>
                            @if(app()->getLocale() === 'ru') RU
                            @elseif(app()->getLocale() === 'en') EN
                            @elseif(app()->getLocale() === 'uk') UA
                            @endif
                        </option>
                
                        <!-- Остальные языки -->
                        @if(app()->getLocale() !== 'ru')
                            <option value="{{ route('page', ['locale' => 'ru', 'page' => Request::segment(2)]) }}">RU</option>
                        @endif
                        @if(app()->getLocale() !== 'en')
                            <option value="{{ route('page', ['locale' => 'en', 'page' => Request::segment(2)]) }}">EN</option>
                        @endif
                        @if(app()->getLocale() !== 'uk')
                            <option value="{{ route('page', ['locale' => 'uk', 'page' => Request::segment(2)]) }}">UA</option>
                        @endif
                    </select>
                </div>
            </li>
            <li><a href="#intro">Intro</a></li>
            <li><a href="#one">What I Do</a></li>
            <li><a href="#two">Who I Am</a></li>
            <li><a href="#work">My Work</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="#intro">My Way</a></li>
        </ul>
    </nav>

    <!-- Иконка "гамбургер" -->
    <div id="hamburger-icon" onclick="toggleMenu()">☰</div>
    <nav id="menu-popup">
        <div class="language-switcher">
            <select onchange="location = this.value;" class="language-dropdown">
                <!-- Основной выбранный язык -->
                <option value="{{ route('page', ['locale' => app()->getLocale(), 'page' => Request::segment(2)]) }}" selected>
                    @if(app()->getLocale() === 'ru') RU
                    @elseif(app()->getLocale() === 'en') EN
                    @elseif(app()->getLocale() === 'uk') UA
                    @endif
                </option>
        
                <!-- Остальные языки -->
                @if(app()->getLocale() !== 'ru')
                    <option value="{{ route('page', ['locale' => 'ru', 'page' => Request::segment(2)]) }}">RU</option>
                @endif
                @if(app()->getLocale() !== 'en')
                    <option value="{{ route('page', ['locale' => 'en', 'page' => Request::segment(2)]) }}">EN</option>
                @endif
                @if(app()->getLocale() !== 'uk')
                    <option value="{{ route('page', ['locale' => 'uk', 'page' => Request::segment(2)]) }}">UA</option>
                @endif
            </select>
        </div>
        <!-- Левая стрелка -->
        <div id="left-arrow" onclick="scrollMenu(-1)">&#10094;</div>
        <ul id="menu-items">
            <li>
                
            </li>
            <li><a href="#intro">Intro</a></li>
            <li><a href="#one">What I Do</a></li>
            <li><a href="#two">Who I Am</a></li>
            <li><a href="#work">My Work</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="#intro">My Way</a></li>
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