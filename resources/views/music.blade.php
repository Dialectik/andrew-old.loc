<x-app-poems-layout>
    {{-- <div class="index-blog page-content page-blog"> --}}
        
        <!-- Wrapper -->
		<div id="wrapper">

            <!-- Main -->
            <div id="main">

                <!-- Posts -->
                @include('music.main', ['allPages' => $allPages]) 

                <!-- Elements - образцы элементов верстки "future-imperfect" -->
                {{-- @include('poems.elements') --}}

                

            </div>

            <!-- Sidebar -->
            <section id="sidebar">

                <!-- Intro -->
                @include('music.intro')

                <!-- Mini Posts -->
                @include('music.mini_posts', ['listPages' => $listPages])
                
                <!-- Posts List -->
                @include('music.posts_list', ['listPages' => $listPages])
                
                <!-- About -->
                {{-- @include('music.about') --}}

            </section>

        </div>


        {{-- @include('home.links') --}}
    {{-- </div> --}}
</x-app-poems-layout>