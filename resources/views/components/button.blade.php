<button type="{{ $type }}" {{ $attributes->merge(['class' => $getClasses()]) }}>
    {{ $slot }}
</button>