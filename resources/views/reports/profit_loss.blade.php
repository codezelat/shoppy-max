<x-app-layout>
    <x-slot name="header">
         <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Profit & Loss Statement') }}
            </h2>
             <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="inline-flex items-center">
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            <svg class="w-3 h-3 me-2.5 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            Reports
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">P&L</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    
                    <!-- Filter Form -->
                    <form method="GET" class="mb-8 flex flex-col md:flex-row gap-4 items-end no-print bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded px-3 py-2 w-full dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">End Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded px-3 py-2 w-full dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-bold transition-colors w-full md:w-auto">Filter Report</button>
                    </form>

                    <div class="text-center mb-10">
                        <h3 class="font-bold text-3xl mb-2 text-gray-800 dark:text-white">Income Statement</h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            @if(request('start_date'))
                                <span class="bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ request('start_date') }} <span class="mx-2">to</span> {{ request('end_date') ?? 'Now' }}
                                </span>
                            @else
                                <span class="bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full text-sm font-medium">All Time</span>
                            @endif
                        </p>
                    </div>

                    <div class="space-y-6">
                        <!-- Revenue -->
                         <div class="group">
                             <div class="flex justify-between items-center border-b dark:border-gray-600 pb-2 mb-2">
                                <span class="font-bold text-lg text-gray-800 dark:text-gray-200">Revenue</span>
                            </div>
                            <div class="flex justify-between items-center pl-4 mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Total Revenue Sales</span>
                                <span class="font-bold text-lg text-gray-900 dark:text-white">{{ number_format($data['total_sales'], 2) }}</span>
                            </div>
                         </div>

                        <!-- COGS -->
                         <div class="group">
                            <div class="flex justify-between items-center pl-4 text-red-600 dark:text-red-400 mb-4">
                                <span>Cost of Goods Sold (FIFO)</span>
                                <span>- {{ number_format($data['cogs'], 2) }}</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center border-t border-b border-gray-300 dark:border-gray-600 py-4 bg-gray-50 dark:bg-gray-700/50 rounded px-2">
                            <span class="font-bold text-xl text-gray-800 dark:text-white uppercase tracking-wide">Gross Profit</span>
                            <span class="font-bold text-xl {{ $data['gross_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($data['gross_profit'], 2) }}
                            </span>
                        </div>

                        <!-- Expenses -->
                         <div class="group mt-6">
                            <div class="flex justify-between items-center border-b dark:border-gray-600 pb-2 mb-2">
                                <span class="font-bold text-lg text-gray-800 dark:text-gray-200">Expenses & Logistics</span>
                            </div>
                            
                            <div class="flex justify-between items-center pl-4 text-red-600 dark:text-red-400 mb-2">
                                <span>Courier Costs (Paid)</span>
                                <span>- {{ number_format($data['courier_cost'], 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center pl-4 text-gray-500 dark:text-gray-400 text-sm italic">
                                <span>(Delivery Fees Collected - Included in Revenue)</span>
                                <span>{{ number_format($data['delivery_income'], 2) }}</span>
                            </div>
                         </div>

                        <!-- Net Profit -->
                        <div class="flex justify-between items-center border-t-2 border-gray-900 dark:border-gray-400 py-6 mt-8 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg px-4 shadow-sm">
                            <span class="font-bold text-2xl text-gray-900 dark:text-white uppercase tracking-wider">NET PROFIT</span>
                            <span class="font-extrabold text-3xl {{ $data['net_profit'] >= 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                {{ number_format($data['net_profit'], 2) }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
