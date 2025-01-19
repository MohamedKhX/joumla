<x-filament-panels::page>
    <div class="space-y-4">
        @if($notifications->count() > 0)
            <div class="flex justify-end">
                <x-filament::button wire:click="markAllAsRead">
                    تحديد الكل كمقروء
                </x-filament::button>
            </div>

            <div class="space-y-2">
                @foreach($notifications as $notification)
                    <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800 {{ is_null($notification->read_at) ? 'border-r-4 border-primary-500' : '' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $notification->data['title'] }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $notification->data['message'] }}
                                </p>
                            </div>
                            
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                @if(is_null($notification->read_at))
                                    <x-filament::button 
                                        size="sm"
                                        wire:click="markAsRead('{{ $notification->id }}')"
                                    >
                                        تحديد كمقروء
                                    </x-filament::button>
                                @endif
                                
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{ $notifications->links() }}
        @else
            <div class="text-center py-12">
                <x-heroicon-o-bell class="mx-auto h-12 w-12 text-gray-400"/>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">لا توجد إشعارات</h3>
            </div>
        @endif
    </div>
</x-filament-panels::page> 