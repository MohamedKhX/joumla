<x-filament-panels::page>
    <div class="flex flex-col items-center justify-center space-y-4">
        <div class="p-6 bg-danger-50 dark:bg-danger-950 rounded-lg text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-danger-100 dark:bg-danger-900 text-danger-600 dark:text-danger-400 mb-4">
                <x-heroicon-o-exclamation-triangle class="w-8 h-8"/>
            </div>
            
            <h2 class="text-2xl font-bold text-danger-700 dark:text-danger-300 mb-2">
                انتهى الاشتراك
            </h2>
            
            <p class="text-danger-600 dark:text-danger-400">
                عذراً، لقد انتهت صلاحية اشتراكك. يرجى تجديد الاشتراك للاستمرار في استخدام النظام.
            </p>

            @if($lastSubscription)
                <div class="mt-4 text-sm text-danger-600 dark:text-danger-400">
                    تاريخ انتهاء آخر اشتراك: {{ $lastSubscription->end_date->format('Y-m-d') }}
                </div>
            @endif

            <div class="mt-6">
                <a href="mailto:support@example.com" 
                   class="inline-flex items-center justify-center gap-1 px-4 py-2 text-sm font-medium text-white transition-colors bg-danger-600 rounded-lg hover:bg-danger-500 focus:outline-none focus:ring-2 focus:ring-danger-500 focus:ring-offset-2 dark:hover:bg-danger-700 dark:focus:ring-offset-danger-800">
                    <x-heroicon-m-envelope class="w-5 h-5"/>
                    تواصل مع الدعم الفني
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page> 