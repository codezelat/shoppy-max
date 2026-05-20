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
                            <p class="text-gray-600 dark:text-gray-400">{{ __("Here is an overview of your account today.") }}</p>
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
            
            @if($resellerAccount)
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 lg:col-span-2">
                        <div class="p-6">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <p class="text-sm font-semibold uppercase text-primary-600 dark:text-primary-400">{{ $resellerStats['label'] }}</p>
                                    <h3 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $resellerAccount->business_name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $resellerAccount->name }} · {{ $resellerAccount->email }}</p>
                                </div>
                                <div class="flex flex-col items-stretch gap-3 sm:items-end">
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-right dark:border-gray-700 dark:bg-gray-900/40">
                                        <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Current Due</p>
                                        <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">Rs. {{ number_format($resellerAccount->due_amount, 2) }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @canany(['view own orders', 'view orders'])
                                            <a href="{{ route('orders.index') }}" class="inline-flex items-center justify-center rounded-lg bg-gray-100 px-3 py-2 text-xs font-medium text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                                My Orders
                                            </a>
                                        @endcanany
                                        @canany(['create own orders', 'create orders'])
                                            <a href="{{ route('orders.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-3 py-2 text-xs font-medium text-white hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700">
                                                Add Order
                                            </a>
                                        @endcanany
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-5">
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                                    <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Orders</p>
                                    <p class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($resellerStats['total_orders']) }}</p>
                                </div>
                                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900 dark:bg-yellow-900/20">
                                    <p class="text-xs font-medium uppercase text-yellow-700 dark:text-yellow-300">Pending</p>
                                    <p class="mt-2 text-xl font-semibold text-yellow-800 dark:text-yellow-200">{{ number_format($resellerStats['pending_orders']) }}</p>
                                </div>
                                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-900/20">
                                    <p class="text-xs font-medium uppercase text-blue-700 dark:text-blue-300">Confirmed</p>
                                    <p class="mt-2 text-xl font-semibold text-blue-800 dark:text-blue-200">{{ number_format($resellerStats['confirmed_orders']) }}</p>
                                </div>
                                <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-900 dark:bg-green-900/20">
                                    <p class="text-xs font-medium uppercase text-green-700 dark:text-green-300">Delivered</p>
                                    <p class="mt-2 text-xl font-semibold text-green-800 dark:text-green-200">{{ number_format($resellerStats['delivered_orders']) }}</p>
                                </div>
                                <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900 dark:bg-red-900/20">
                                    <p class="text-xs font-medium uppercase text-red-700 dark:text-red-300">Returned</p>
                                    <p class="mt-2 text-xl font-semibold text-red-800 dark:text-red-200">{{ number_format($resellerStats['returned_orders']) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                        <div class="p-6 space-y-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Account Details</h3>
                            <div>
                                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Mobile</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $resellerAccount->mobile }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Location</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ collect([$resellerAccount->city, $resellerAccount->district])->filter()->join(', ') ?: '-' }}</p>
                            </div>
                            @if($resellerAccount->reseller_type === \App\Models\Reseller::TYPE_RESELLER)
                                <div>
                                    <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Return Fee</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">Rs. {{ number_format($resellerAccount->return_fee ?? 0, 2) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

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
                        <p>{{ __('You have elevated privileges.') }}</p>
                    </div>
                </div>
            </div>
            @endcan

        </div>
    </div>
</x-app-layout>
