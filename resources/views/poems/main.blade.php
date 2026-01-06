<div class="all-pages-flex" style="padding: 0%; margin: 0%;">
    @foreach ($allPages as $page)
        <article class="post">
            <header>
                <div class="title">
                    <h2><a href="/<?= app()->getLocale() ?>/poems/{{ $page->slug }}">{{ $page->getTranslatedAttribute('title', app()->getLocale()) }}</a></h2>
                    <p>{{ $page->getTranslatedAttribute('meta_description', app()->getLocale()) }}</p>
                </div>
                <div class="meta">
                    <time class="published" datetime="{{ $page->datetime }}">{{ $page->formatted_date }}</time>
                    <a href="#" class="author"><span class="name">Andrew Old</span><img src="{{ asset('future-imperfect/images/avatar.jpg') }}?v=2" alt="" /></a>
                </div>
            </header>
            <a href="/<?= app()->getLocale() ?>/poems/{{ $page->slug }}" class="image featured"><img src="{{ asset('./storage/' . $page->image) }}?v=1" alt="" /></a>
            <p>{!! $page->excerpt !!}</p>
            <footer>
                <ul class="actions">
                    <li><a href="/<?= app()->getLocale() ?>/poems/{{ $page->slug }}" class="button large"><?php echo __('Читать далее'); ?></a></li>
                </ul>
                <ul class="stats">
                    <li><a href="#">General</a></li>
                    <li><a href="#" class="icon solid fa-heart">15</a></li>
                    <li><a href="#" class="icon solid fa-comment">3</a></li>
                </ul>
            </footer>
        </article>
    @endforeach
</div>


<!-- Pagination -->
<ul id="pagination-container" class="actions pagination">
    {{ $allPages->onEachSide(0)->links('vendor.pagination.custom') }}
</ul>



<style>
    .sm\:justify-between p {
        display: none !important;
    }
</style>

@section('javascript')
    <script>
        $(document).ready(function () {
            // Обработка кликов по ссылкам пагинации
            $('#pagination-container').on('click', 'a', function (e) {
                e.preventDefault();
                
                // Получение URL из атрибута href
                var href = $(this).attr('href');
                // Используем URLSearchParams для извлечения номера страницы
                let params = new URLSearchParams(href.split('?')[1]);
                let page = params.get('page') ? parseInt(params.get('page')) : 1;
                // var page = $(this).text();
                var locale = '<?php echo app()->getLocale() ?>';
                var url = '/' + locale + '/ajaxpoem/' + page;
                
                // console.log(url);

                // Добавляем номер страницы в URL без перезагрузки
                history.pushState(null, '', '?page=' + page);
            
                // Отправляем AJAX-запрос
                $.ajax({
                    url: url,
                    success: function (data) {
                        // Обновляем содержимое блока страниц
                        $('.all-pages-flex').html(data.pages);

                        // Обновляем HTML-код пагинации
                        $('#pagination-container').html(data.pagination);

                        // Прокручиваем страницу вверх
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    }
                });
            });
        });
    </script>
@stop