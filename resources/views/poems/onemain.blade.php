<article class="post">
    <header>
        <div class="title">
            <ul class="actions">
                <li><a href="{{ url()->previous() }}" class="button large back"><?php echo __('Назад'); ?></a></li>
            </ul>
            <h2><a href="/<?= app()->getLocale() ?>/poems/{{ $page->slug }}">{{ $page->getTranslatedAttribute('title', app()->getLocale()) }}</a></h2>
            <p>{{ $page->getTranslatedAttribute('meta_description', app()->getLocale()) }}</p>
        </div>
        <div class="meta">
            <time class="published" datetime="{{ $page->datetime }}">{{ $page->formatted_date }}</time>
            <a href="#" class="author"><span class="name">Andrew Old</span><img src="{{ asset('future-imperfect/images/avatar.jpg') }}?v=2" alt="" /></a>
        </div>
    </header>

    @if(!empty($page->image))
        <span class="image featured"><img src="{{ asset('./storage/' . $page->image) }}?v=1" alt="" /></span>
    @endif
    
    @if(!empty($page->image))
        <div style="text-align: center;">{!! $page->body !!}</div>
    @endif

    @if(!empty($audioItems) && $audioItems->count() > 0)
        @foreach ($audioItems as $media)
            @php
                // Декодируем JSON из поля 'file'
                $files = json_decode($media->file, true);
            @endphp

            @foreach ($files as $file)
                <!-- Аудио-плеер -->
                <div class="audio-container" style="text-align: center;">
                    <audio class="js-player" controls>
                        <source src="{{ asset('storage/' . $file['download_link']) }}" type="audio/mp3">
                        <?php echo __('Ваш браузер не поддерживает тег audio'); ?>.
                    </audio>
                    <p>{{ $file['original_name'] }}</p>
                </div>
            @endforeach
        @endforeach
    @endif

    @if(!empty($videoItems) && $videoItems->count() > 0)
        <p>
            <!-- Видео-плеер -->
            <div class="video-container">

                @php 
                    $poster = ''; 
        
                    foreach ($videoItems as $media) {
                        if(!empty($media->poster)) {
                            $poster = $media->poster;
                            break;
                        }
                    }
                @endphp
                
                <video
                    class="js-player"
                    controls
                    crossorigin
                    playsinline
                    poster="{{ !empty($poster) ? asset('storage/' . $poster) : (!empty($page->image) ? asset('./storage/' . $page->image) : '') }}"
                    id="player"
                >
                    
                    @php $v = 1; @endphp
    
                    @foreach ($videoItems as $media)
                        @php
                            // Декодируем JSON из поля 'file'
                            $files = json_decode($media->file, true);
                        @endphp
                            
                        @if(!empty($files))
                            @foreach ($files as $file)
                                @if(!empty($media->resolution))
                                    <source
                                        src="{{ asset('storage/' . $file['download_link']) }}"
                                        type="video/mp4"
                                        size="{{ $media->resolution }}"
                                    />
                                @else
                                    <source src="{{ asset('storage/' . $file['download_link']) }}" type="video/mp4">
                                @endif
                            @endforeach
                        @endif
    
                        @if($v == 1)
                            <?php echo __('Ваш браузер не поддерживает video'); ?>.
                            <!-- Fallback for browsers that don't support the <video> element -->
                            <a href="{{ asset('storage/' . $file['download_link']) }}" download>{{ $file['original_name'] }}</a>
                        @endif
    
                        @php $v++; @endphp
                    @endforeach
                    
                    @foreach ($subtitleItems as $subtitle)
                        @php
                            // Декодируем JSON из поля 'src'
                            $files = json_decode($subtitle->src, true);
                        @endphp
                            
                        <!-- Caption files -->
                        @if(!empty($files))
                            @foreach ($files as $file)
                                <track
                                    kind="captions"
                                    label="{{ $subtitle->label }}"
                                    srclang="{{ $subtitle->srclang }}"
                                    src="{{ asset('storage/' . $file['download_link']) }}"
                                    {{ $subtitle->srclang == 'ru' || $subtitleItems->count() == 1 ? 'default' : '' }}
                                />
                            @endforeach
                        @endif
                    @endforeach
    
                </video>
            </div>
        </p>
    @endif

    <ul class="actions">
        <li><a href="{{ url()->previous() }}" class="button large back"><?php echo __('Назад'); ?></a></li>
    </ul>
    <footer>
        <ul class="stats">
            <li><a href="#">General</a></li>
            <li><a href="#" class="icon solid fa-heart">28</a></li>
            <li><a href="#" class="icon solid fa-comment">128</a></li>
        </ul>
    </footer>
</article>

@section('javascript')
    <script>
        $(window).on('load', function () {
            $('body').addClass('single');
        });
    </script>
@stop

@if(!empty($audioItems) && $audioItems->count() > 0 || !empty($videoItems) && $videoItems->count() > 0)
    @section('styles_plyr')
        <!-- Docs styles -->
        {{-- <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css?v=1.0.0"> --}}

        <style>
            .video-container {
                position: relative;
                width: 100%;
                max-width: 800px;
                margin: 0 auto;
            }

            .video-container video {
                width: 100%;
                height: auto;
                max-height: 500px;
                border-radius: 10px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }

            /* Снимаем ограничения в полноэкранном режиме */
            .video-container video:fullscreen,
            .video-container video:-webkit-full-screen,
            .video-container video:-moz-full-screen,
            .video-container video:-ms-fullscreen {
                max-height: none;
                width: 100%;
                height: 100%;
                border-radius: 0;
                box-shadow: none;
            }
        </style>
    @stop

    @section('javascript_plyr')
        <script src="https://cdn.plyr.io/3.7.8/plyr.js?v=1.0.0"></script>

        <script>
            // document.addEventListener('DOMContentLoaded', function () {
            //     const players_audio = Plyr.setup('audio');
            //     const players_video = Plyr.setup('video');
            // });

            // document.addEventListener('DOMContentLoaded', function () {
            //     const videoElement = document.querySelector('.js-player');

            //     // Создаем обертку для видео
            //     const wrapper = document.createElement('div');
            //     // if (videoElement) {
            //         videoElement.parentNode.insertBefore(wrapper, videoElement);
            //         wrapper.appendChild(videoElement);
    
            //         // Подключаем Shadow DOM к обертке
            //         const shadowRoot = wrapper.attachShadow({ mode: 'open' });
    
            //         // Добавляем стили Plyr в Shadow DOM
            //         fetch('https://cdn.plyr.io/3.7.8/plyr.css')
            //             .then(response => response.text())
            //             .then(css => {
            //                 const style = document.createElement('style');
            //                 style.textContent = css;
            //                 shadowRoot.appendChild(style);
    
            //                 // Помещаем видео в Shadow DOM
            //                 const clonedVideo = videoElement.cloneNode(true);
            //                 shadowRoot.appendChild(clonedVideo);
    
            //                 // Инициализация плеера
            //                 Plyr.setup(clonedVideo);
            //             });
            //     // }
            // });
        </script>
    @stop
@endif