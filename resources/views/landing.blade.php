@php use Filament\Facades\Filament;use Illuminate\Support\Facades\Route; @endphp
<x-filament::layouts.base :title="$title">
    <div class="h-screen w-full flex items-center justify-between">
        <div class="flex items-center justify-end">
            <ul class="flex items-center gap-4">
                @guest
                    @if(Route::has('auth.discord.redirect'))
                        <x-nox::landing.nav-link :to="route('auth.discord.redirect')">
                            Login
                        </x-nox::landing.nav-link>
                    @endif
                @else
                    @if(auth()->user()->can('view_admin') && $filamentUrl = Filament::getUrl())
                        <x-nox::landing.nav-link :to="$filamentUrl">
                            Administration
                        </x-nox::landing.nav-link>
                    @endif

                    @if(Route::has('filament.auth.logout'))
                        <form method="POST" action="{{ route('filament.auth.logout') }}">
                            <x-nox::landing.nav-link type="submit">
                                Logout
                            </x-nox::landing.nav-link>
                        </form>
                    @endif
                @endguest
            </ul>
        </div>

        <div>
            <h1 class="text-8xl font-bold text-primary-600 dark:text-primary-500 motion-safe:animate-pulse">
                {{ $title }}
            </h1>
        </div>

        <div class="flex items-center justify-end">
            <ul class="flex items-center gap-4">
                <x-nox::landing.nav-link to="https://github.com/nox-php/framework">
                    Documentation
                </x-nox::landing.nav-link>

                <x-nox::landing.nav-link to="https://github.com/nox-php/framework">
                    <x-nox::icons.github class="h-5 w-5"/>
                </x-nox::landing.nav-link>
            </ul>
        </div>
    </div>
</x-filament::layouts.base>
