<x-app-poems-layout>
    {{-- <div class="index-blog page-content page-blog"> --}}
        
        <!-- Wrapper -->
		<div id="wrapper">

            <!-- Main -->
            <div id="main">

                <!-- Posts -->
                @include('poems.main', ['allPages' => $allPages]) 

                <!-- Elements - образцы элементов верстки "future-imperfect" -->
                {{-- @include('poems.elements') --}}

                

            </div>

            <!-- Sidebar -->
            <section id="sidebar">

                <!-- Intro -->
                @include('poems.intro')

                <!-- Mini Posts -->
                @include('poems.mini_posts', ['listPages' => $listPages])
                
                <!-- Posts List -->
                @include('poems.posts_list', ['listPages' => $listPages])
                
                <!-- About -->
                {{-- @include('poems.about') --}}

            </section>

        </div>


        {{-- @include('home.links') --}}
    {{-- </div> --}}
</x-app-poems-layout>