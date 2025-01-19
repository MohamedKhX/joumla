<div>
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <button
                class="relative flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-500/5 focus:bg-gray-500/5 focus:outline-none"
            >
                <x-heroicon-o-bell class="w-5 h-5" />
                
                @php
                    $unreadCount = auth()->user()->unreadNotifications()->count();
                @endphp
                
                @if($unreadCount > 0)
                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>
        </x-slot>

        <x-filament::dropdown.list class="w-80">
            <div class="px-4 py-2 text-sm font-medium text-gray-900 border-b dark:text-white">
                الإشعارات
            </div>

            @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
                <x-filament::dropdown.list.item
                    :color="is_null($notification->read_at) ? 'primary' : 'gray'"
                    class="flex items-center gap-3 py-3"
                    :href="isset($notification->data['order_id']) ? route('filament.wholesale-store.resources.orders.view', ['record' => $notification->data['order_id']]) : '#'"
                >
                    <div class="flex-1 space-y-1">
                        <p class="text-sm font-medium">
                            {{ $notification->data['title'] }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $notification->data['message'] }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </x-filament::dropdown.list.item>
            @empty
                <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                    لا توجد إشعارات
                </div>
            @endforelse

            @if(auth()->user()->notifications->count() > 5)
                <x-filament::dropdown.list.item
                    href="{{ route('filament.wholesale-store.pages.notifications') }}"
                    class="text-center text-sm font-medium text-primary-600 hover:text-primary-500"
                >
                    عرض كل الإشعارات
                </x-filament::dropdown.list.item>
            @endif
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div> 