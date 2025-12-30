<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Welcome Section with Roles -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl mb-6 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">{{ __("Welcome back, ") }}<span class="text-primary-600 dark:text-primary-400">{{ Auth::user()->name }}</span>! 👋</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ __("Here's what's happening with your store today.") }}</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
                            @forelse(Auth::user()->roles as $role)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200 rounded-full text-sm font-medium border border-blue-200 dark:border-blue-800">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-gray-500 dark:text-gray-400 text-sm italic">{{ __('No roles assigned') }}</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Progress Section -->
            @if($target)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl mb-8 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Current Target Progress ({{ $target->start_date->format('M d') }} - {{ $target->end_date->format('M d') }})
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Sales Target -->
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-base font-medium text-gray-700 dark:text-gray-300">Sales Target</span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ number_format($targetProgress['sales_amount'], 2) }} / {{ number_format($targetProgress['target_amount'], 2) }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $targetProgress['percentage'] }}%"></div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ number_format($targetProgress['percentage'], 1) }}% Achieved
                            </p>
                        </div>
                        
                        <!-- Placeholder for other targets if needed -->
                         <div>
                             <!-- Additional stats like Return Rate target could go here -->
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- KPI Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Sales -->
                <x-stats-card title="Total Sales" value="{{ $stats['total_sales_count'] }}" color="blue">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="mt-1 text-xs text-blue-600 dark:text-blue-300">
                        Vol: {{ number_format($stats['total_sales_value'], 2) }}
                    </div>
                </x-stats-card>

                <!-- Pending Orders -->
                <x-stats-card title="Pending Orders" value="{{ $stats['pending_orders'] }}" color="yellow">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </x-stats-card>

                <!-- Confirmed Orders -->
                <x-stats-card title="Confirmed Orders" value="{{ $stats['confirmed_orders'] }}" color="green">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </x-stats-card>

                <!-- Hold Orders -->
                <x-stats-card title="Hold Orders" value="{{ $stats['hold_orders'] }}" color="red">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </x-stats-card>

                <!-- Total Commission -->
                <x-stats-card title="Total Commission" value="{{ number_format($stats['total_commission'], 2) }}" color="purple">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </x-stats-card>

                <!-- Paid Commission -->
                <x-stats-card title="Paid Commission" value="{{ number_format($stats['paid_commission'], 2) }}" color="teal">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </x-stats-card>
            </div>
            
            @can('view users')
            <div class="mt-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700/50 rounded-lg flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                        {{ __('Admin Access Granted') }}
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                        <p>{{ __('You have elevated privileges. Toggle between user and admin views as needed.') }}</p>
                    </div>
                </div>
            </div>
            @endcan

        </div>
    </div>
</x-app-layout>
