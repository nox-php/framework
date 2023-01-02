<?php

namespace Nox\Framework\Updater\Http\Controllers;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Routing\Controller;
use Nox\Framework\Updater\Jobs\NoxUpdateJob;

class NoxUpdaterController extends Controller
{
    public function __invoke(string $version)
    {
        $user = Filament::auth()->user();

        abort_if($user === null || !$user->can('view_admin'), 401);

        NoxUpdateJob::dispatch($user, $version);

        Notification::make()
            ->success()
            ->title('Nox is updating in the background')
            ->body('You will be notified once it has finished')
            ->send();

        return redirect()->back();
    }
}
