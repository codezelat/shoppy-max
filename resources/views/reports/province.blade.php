<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Province Wise Sales Report') }}
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
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Province</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-gray-800">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Table -->
            <div class="relative overflow-x-auto sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white">Sales Data</h3>
                </div>
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Province</th>
                            <th scope="col" class="px-6 py-3 text-right">Total Sales</th>
                            <th scope="col" class="px-6 py-3 text-center">Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($provinceSales as $row)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                    {{ $row->province ?? 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-green-600 dark:text-green-400">
                                    {{ number_format($row->total_sales, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-900 dark:text-white">
                                    {{ $row->order_count }}
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Visualization -->
             <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-6">Distribution Analysis</h3>
                <div class="space-y-6">
                    @php $maxSales = $provinceSales->max('total_sales') ?: 1; @endphp
                    @foreach($provinceSales as $row)
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $row->province ?? 'Unassigned' }}</span>
                            <span class="font-bold text-xs text-gray-500 dark:text-gray-400">{{ round(($row->total_sales / $maxSales) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($row->total_sales / $maxSales) * 100 }}%"></div>
                        </div>
                        <div class="text-xs text-right mt-1 text-gray-400 dark:text-gray-500">
                            {{ number_format($row->total_sales, 2) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
