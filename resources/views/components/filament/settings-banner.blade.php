<div
    class="pointer-events-auto invisible flex gap-3 w-full transition duration-300 shadow-lg bg-white rounded-xl p-4 border border-gray-200 dark:border-gray-700 dark:bg-gray-800"
>
    @svg('heroicon-o-check-circle', 'h-6 w-6 text-success-400')

    <div class="grid flex-1">
        <div
            class="flex h-6 items-center text-sm font-medium text-gray-900 dark:text-gray-100"
        >
            <p>
                {{ $slot }}
            </p>
        </div>
    </div>
</div>
