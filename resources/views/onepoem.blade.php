<x-app-poems-layout>
    {{-- <div class="index-blog page-content page-blog"> --}}
        
        <!-- Wrapper -->
		<div id="wrapper">

            <!-- Main -->
            <div id="main">

                <!-- Post -->
                @include('poems.onemain', ['page' => $page, 'audioItems' => $audioItems, 'videoItems' => $videoItems, 'subtitleItems' => $subtitleItems]) 

            </div>

        </div>


        {{-- @include('home.links') --}}
    {{-- </div> --}}
</x-app-poems-layout>