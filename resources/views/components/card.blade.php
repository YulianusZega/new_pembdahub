<div {{ $attributes->merge(['class' => $getClasses()]) }}>
    @if($title)
    <div class="border-b border-gray-200 pb-4 mb-4">
        <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
    </div>
    @endif

    <div>
        {{ $slot }}
    </div>
</div>