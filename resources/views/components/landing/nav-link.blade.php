@props([
    'to' => null
])

@php
    $classes = 'font-medium text-white transition-colors hover:text-primary-600 dark:hover:text-primary-500';
@endphp

<li>
    @if($to === null)
        <button
            {{$attribute->class($classes)}}
        >
            {{ $slot }}
        </button>
    @else
        <a
            {{$attributes->merge(['href' => $to])->class($classes)}}
        >
            {{ $slot }}
        </a>
    @endif
</li>
