@php
    use Nox\Framework\Admin\Filament\Notifications\Notification;

    $notifications = collect(session('filament.notifications', []))
        ->filter(static fn(array $notification): bool => $notification['banner'] ?? false);
@endphp

@if($notifications->isNotEmpty())
    <div>
        @foreach($notifications as $notification)
            <x-nox::filament.notifications.banner :notification="Notification::fromArray($notification)"/>
        @endforeach
    </div>
@endif
