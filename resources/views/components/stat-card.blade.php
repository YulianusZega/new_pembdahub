<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6']) }}>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 mb-1">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $value }}</p>

            @if($trend)
            <div class="mt-2 flex items-center text-sm">
                @if(str_starts_with($trend, '+'))
                <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="text-green-600 font-medium">{{ $trend }}</span>
                @else
                <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                </svg>
                <span class="text-red-600 font-medium">{{ $trend }}</span>
                @endif
                @if($trendLabel)
                <span class="text-gray-500 ml-1">{{ $trendLabel }}</span>
                @endif
            </div>
            @endif
        </div>

        <div class="{{ $getIconClasses() }}">
            {!! $getIconSvg() !!}
        </div>
    </div>

    @if(isset($footer))
    <div class="mt-4 pt-4 border-t border-gray-100">
        {{ $footer }}
    </div>
    @endif
</div>