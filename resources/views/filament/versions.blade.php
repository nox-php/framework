<div @class([
    'py-3 px-6 mt-auto -mb-6 text-xs border-t filament-versions-nav-component',
    'dark:border-gray-700' => config('filament.dark_mode'),
])
     x-data
     x-show="$store.sidebar.isOpen">
    <ul class="flex flex-wrap items-center gap-x-4 gap-y-2">
        <li class="flex-shrink-0">Nox {{ $versions['nox'] }}</li>
        <li class="flex-shrink-0">PHP v{{ $versions['php'] }}</li>
    </ul>
</div>
