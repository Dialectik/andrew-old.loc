<section>
    <div class="mini-posts">
        
        <?php $i = 1; ?>
        <!-- Mini Post -->
        @foreach($listPages as $page)
            @if($i <= 4)
                <article class="mini-post">
                    <header>
                        <h3><a href="/<?= app()->getLocale() ?>/music/{{ $page->slug }}">{{ $page->getTranslatedAttribute('title', app()->getLocale()) }}</a></h3>
                        <time class="published" datetime="{{ $page->datetime }}">{{ $page->formatted_date }}</time>
                        <a href="#" class="author"><img src="{{ asset('future-imperfect/images/avatar_music.jpg') }}?v=2" alt="" /></a>
                    </header>
                    <a href="/<?= app()->getLocale() ?>/music/{{ $page->slug }}" class="image"><img src="{{ asset('./storage/' . $page->image) }}?v=1" alt="" /></a>
                </article>
            @endif
            <?php $i++; ?>
        @endforeach

    </div>
</section>