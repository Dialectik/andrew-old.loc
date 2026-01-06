<section>
    <ul class="posts">
        <?php $i = 1; ?>
        <!-- Mini Post -->
        @foreach($listPages as $page)
            @if($i > 4)
                <li>
                    <article>
                        <header>
                            <h3><a href="/<?= app()->getLocale() ?>/poems/{{ $page->slug }}">{{ $page->getTranslatedAttribute('title', app()->getLocale()) }}</a></h3>
                            <time class="published" datetime="{{ $page->datetime }}">{{ $page->formatted_date }}</time>
                        </header>
                        <a href="/<?= app()->getLocale() ?>/poems/{{ $page->slug }}" class="image"><img src="{{ asset('./storage/' . $page->image) }}?v=1" alt="" /></a>
                    </article>
                </li>
            @endif
            <?php $i++; ?>
        @endforeach
    </ul>
</section>