@php
    use Nox\Framework\Admin\Filament\Notifications\Notification;

    $notifications = collect(session('filament.notifications', []))
        ->filter(static fn(array $notification): bool => $notification['banner'] ?? false);
@endphp

@if($notifications->isNotEmpty())
    <div>
        @foreach($notifications as $notification)
            <x-notifications::notification :notification="Notification::fromArray($notification)"/>
        @endforeach
    </div>
@endif
