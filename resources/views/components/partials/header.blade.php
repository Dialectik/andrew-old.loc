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
        </ul>
    </nav>
</header>