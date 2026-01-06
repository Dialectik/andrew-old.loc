@foreach ($allPages as $page)
    <article class="post">
        <header>
            <div class="title">
                <h2><a href="/<?= app()->getLocale() ?>/music/{{ $page->slug }}">{{ $page->getTranslatedAttribute('title', app()->getLocale()) }}</a></h2>
                <p>{{ $page->getTranslatedAttribute('meta_description', app()->getLocale()) }}</p>
            </div>
            <div class="meta">
                <time class="published" datetime="{{ $page->datetime }}">{{ $page->formatted_date }}</time>
                <a href="#" class="author"><span class="name">Andrew Old</span><img src="{{ asset('future-imperfect/images/avatar_music.jpg') }}?v=2" alt="" /></a>
            </div>
        </header>
        <a href="/<?= app()->getLocale() ?>/music/{{ $page->slug }}" class="image featured"><img src="{{ asset('./storage/' . $page->image) }}?v=1" alt="" /></a>
        <p>{!! $page->excerpt !!}</p>
        <footer>
            <ul class="actions">
                <li><a href="/<?= app()->getLocale() ?>/music/{{ $page->slug }}" class="button large"><?php echo __('Слушать'); ?></a></li>
            </ul>
            <ul class="stats">
                <li><a href="#">General</a></li>
                <li><a href="#" class="icon solid fa-heart">32</a></li>
                <li><a href="#" class="icon solid fa-comment">3</a></li>
            </ul>
        </footer>
    </article>
@endforeach