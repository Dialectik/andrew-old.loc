@if ($paginator->hasPages())
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li>
                <a href="#" class="disabled button large previous"><?php echo __('Предыдущая страница'); ?></a>
            </li>
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" class="button large previous"><?php echo __('Предыдущая страница'); ?></a>
            </li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" class="button large next"><?php echo __('Следующая страница'); ?></a>
            </li>
        @else
            <li>
                <a href="#" class="disabled button large next"><?php echo __('Следующая страница'); ?></a>
            </li>
        @endif
@endif