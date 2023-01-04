<?php

namespace Nox\Framework\Admin\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public function render(): View
    {
        return view('nox::livewire.language-switcher', [
            'locales' => collect(config('localisation', []))
                ->filter(static fn(array $locale): bool => $locale['enabled'] ?? false)
                ->all()
        ]);
    }

    public function changeLocale(string $locale)
    {
        session()->put('locale', $locale);

        return redirect(request()->header('Referer'));
    }
}
