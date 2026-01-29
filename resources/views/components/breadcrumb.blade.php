@props(['items' => []])

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('welcome') }}" class="text-decoration-none">
                <i class="fas fa-home me-1"></i>Home
            </a>
        </li>
        @foreach($items as $item)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    @if(isset($item['icon']))
                        <i class="{{ $item['icon'] }} me-1"></i>
                    @endif
                    {{ $item['label'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] }}" class="text-decoration-none">
                        @if(isset($item['icon']))
                            <i class="{{ $item['icon'] }} me-1"></i>
                        @endif
                        {{ $item['label'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
