<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Seller Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl mb-8 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter Data
                    </h3>
                    <form method="GET" action="{{ route('sellers.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                        <div class="md:col-span-2">
                            <label for="seller_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Seller</label>
                            <select id="seller_id" name="seller_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="">All Sellers</option>
                                @foreach($sellers as $seller)
                                    <option value="{{ $seller->id }}" {{ $selectedSellerId == $seller->id ? 'selected' : '' }}>
                                        {{ $seller->name }} ({{ $seller->user_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        </div>
                        <div>
                            <label for="end_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">End Date</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        </div>
                        <div class="md:col-span-4 flex justify-end">
                            <button type="submit" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800 shadow-md transition-transform hover:scale-105 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            <!-- Target Progress Section -->
            @if(isset($target) && $target)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl mb-8 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Current Target Progress ({{ $target->start_date->format('M d') }} - {{ $target->end_date->format('M d') }})
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
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
            </div>

            <!-- Graph Area -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Last 30 Days Sales</h3>
                    <div class="h-64 bg-gray-50 dark:bg-gray-700 flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Sales Trend Graph</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Chart.js integration required.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
