@php
    $notifications = collect(session('filament.notifications', []))
        ->filter(static fn(array $notification): bool => $notification['banner'] ?? false);
@endphp

<div>
    @foreach($notifications as $notification)
        Notification!
    @endforeach
</div>
