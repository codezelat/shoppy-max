<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reseller Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('resellers.dashboard') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="reseller_id" class="block text-sm font-medium text-gray-700">Select Reseller</label>
                            <select id="reseller_id" name="reseller_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Resellers</option>
                                @foreach($resellers as $reseller)
                                    <option value="{{ $reseller->id }}" {{ $selectedResellerId == $reseller->id ? 'selected' : '' }}>
                                        {{ $reseller->name }} ({{ $reseller->user_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Filter
                        </button>
                    </form>
                </div>
            </div>

            <!-- KPI Boxes -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-100 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-blue-800">Total Sales</h3>
                    <p class="text-2xl font-bold mt-2">{{ $stats['total_sales_count'] }} <span class="text-sm font-normal">({{ number_format($stats['total_sales_value'], 2) }})</span></p>
                </div>
                <div class="bg-yellow-100 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-yellow-800">Pending Orders</h3>
                    <p class="text-2xl font-bold mt-2">{{ $stats['pending_orders'] }}</p>
                </div>
                <div class="bg-green-100 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-green-800">Confirmed Orders</h3>
                    <p class="text-2xl font-bold mt-2">{{ $stats['confirmed_orders'] }}</p>
                </div>
                <div class="bg-red-100 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-red-800">Hold Orders</h3>
                    <p class="text-2xl font-bold mt-2">{{ $stats['hold_orders'] }}</p>
                </div>
                <div class="bg-purple-100 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-purple-800">Total Commission</h3>
                    <p class="text-2xl font-bold mt-2">{{ number_format($stats['total_commission'], 2) }}</p>
                </div>
                <div class="bg-indigo-100 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-indigo-800">Delivered Comm.</h3>
                    <p class="text-2xl font-bold mt-2">{{ number_format($stats['total_delivered_commission'], 2) }}</p>
                </div>
                <div class="bg-teal-100 p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-teal-800">Paid Commission</h3>
                    <p class="text-2xl font-bold mt-2">{{ number_format($stats['paid_commission'], 2) }}</p>
                </div>
            </div>

            <!-- Graph Area (Placeholder for now as libraries like Chart.js need setup) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Last 30 Days Sales</h3>
                    <div class="h-64 bg-gray-50 flex items-center justify-center border border-dashed border-gray-300 rounded">
                        <span class="text-gray-500">Sales Trend Graph will appear here</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
