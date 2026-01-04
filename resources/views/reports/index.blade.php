<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Reports & Analytics') }}
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
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Reports</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Quick Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Delivered Sales -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-5 shadow-lg text-white relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                   <div class="relative z-10">
                        <p class="text-green-100 text-xs font-bold uppercase tracking-wider mb-1">Total Delivered Sales</p>
                        <h3 class="text-2xl font-bold">{{ number_format($totalSales, 2) }}</h3>
                   </div>
                   <div class="absolute right-0 bottom-0 opacity-20 transform translate-x-2 translate-y-2 group-hover:scale-110 transition-transform">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"></path></svg>
                   </div>
                </div>

                <!-- Today's Sales -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-5 shadow-lg text-white relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="relative z-10">
                         <p class="text-blue-100 text-xs font-bold uppercase tracking-wider mb-1">Today's Sales</p>
                         <h3 class="text-2xl font-bold">{{ number_format($todaySales, 2) }}</h3>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-20 transform translate-x-2 translate-y-2 group-hover:scale-110 transition-transform">
                         <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                    </div>
                 </div>

                <!-- Pending Orders -->
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-5 shadow-lg text-white relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="relative z-10">
                         <p class="text-yellow-100 text-xs font-bold uppercase tracking-wider mb-1">Pending Orders</p>
                         <h3 class="text-2xl font-bold">{{ $pendingOrders }}</h3>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-20 transform translate-x-3 translate-y-2 group-hover:scale-110 transition-transform">
                         <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                    </div>
                 </div>

                <!-- Total Orders -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-5 shadow-lg text-white relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="relative z-10">
                         <p class="text-purple-100 text-xs font-bold uppercase tracking-wider mb-1">Total Orders</p>
                         <h3 class="text-2xl font-bold">{{ $totalOrders }}</h3>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-20 transform translate-x-2 translate-y-2 group-hover:scale-110 transition-transform">
                         <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg>
                    </div>
                 </div>
            </div>

            <!-- Report Links Grid -->
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mt-8 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Detailed Reports
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <a href="{{ route('reports.province') }}" class="group block p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:border-blue-500 dark:hover:border-blue-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 dark:bg-gray-700 rounded-full blur-2xl opacity-50 transform translate-x-10 -translate-y-10 group-hover:bg-blue-100 dark:group-hover:bg-gray-600 transition-colors"></div>
                    <div class="relative z-10 flex items-start">
                        <div class="p-3 bg-blue-100 rounded-xl mr-4 text-blue-600 dark:bg-blue-900 dark:text-blue-300 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Province Sales</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Geographical breakdown of sales performance.</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('reports.profit-loss') }}" class="group block p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:border-green-500 dark:hover:border-green-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 dark:bg-gray-700 rounded-full blur-2xl opacity-50 transform translate-x-10 -translate-y-10 group-hover:bg-green-100 dark:group-hover:bg-gray-600 transition-colors"></div>
                    <div class="relative z-10 flex items-start">
                         <div class="p-3 bg-green-100 rounded-xl mr-4 text-green-600 dark:bg-green-900 dark:text-green-300 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Profit & Loss</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Financial analysis of sales vs COGS and logistics.</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('reports.stock') }}" class="group block p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:border-yellow-500 dark:hover:border-yellow-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-yellow-50 dark:bg-gray-700 rounded-full blur-2xl opacity-50 transform translate-x-10 -translate-y-10 group-hover:bg-yellow-100 dark:group-hover:bg-gray-600 transition-colors"></div>
                     <div class="relative z-10 flex items-start">
                         <div class="p-3 bg-yellow-100 rounded-xl mr-4 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors">Stock Report</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Inventory valuation and batch aging analysis.</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('reports.product-sales') }}" class="group block p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:border-indigo-500 dark:hover:border-indigo-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 dark:bg-gray-700 rounded-full blur-2xl opacity-50 transform translate-x-10 -translate-y-10 group-hover:bg-indigo-100 dark:group-hover:bg-gray-600 transition-colors"></div>
                    <div class="relative z-10 flex items-start">
                         <div class="p-3 bg-indigo-100 rounded-xl mr-4 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Product Sales</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Detailed product performance metrics.</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('reports.packet-count') }}" class="group block p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:border-purple-500 dark:hover:border-purple-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 dark:bg-gray-700 rounded-full blur-2xl opacity-50 transform translate-x-10 -translate-y-10 group-hover:bg-purple-100 dark:group-hover:bg-gray-600 transition-colors"></div>
                    <div class="relative z-10 flex items-start">
                        <div class="p-3 bg-purple-100 rounded-xl mr-4 text-purple-600 dark:bg-purple-900 dark:text-purple-300 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Packet Count (User)</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Packing performance by staff member.</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('reports.user-sales') }}" class="group block p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:border-red-500 dark:hover:border-red-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 dark:bg-gray-700 rounded-full blur-2xl opacity-50 transform translate-x-10 -translate-y-10 group-hover:bg-red-100 dark:group-hover:bg-gray-600 transition-colors"></div>
                    <div class="relative z-10 flex items-start">
                        <div class="p-3 bg-red-100 rounded-xl mr-4 text-red-600 dark:bg-red-900 dark:text-red-300 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">User Wise Sales</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Sales contribution by user or reseller.</p>
                        </div>
                    </div>
                </a>

            </div>
            
            <!-- Simple Chart Placeholder (Enhanced) -->
            <div class="mt-8 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white">Monthly Sales Trend</h3>
                    <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    </div>
                </div>
                
                <div class="space-y-6">
                    @foreach($monthlySales as $data)
                    <div class="group">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $data->month }}</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ number_format($data->sums, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-blue-600 h-3 rounded-full transition-all duration-1000 ease-out group-hover:bg-blue-500 text-xs text-right text-white pr-2 leading-3" style="width: {{ ($data->sums / ($monthlySales->max('sums') ?: 1)) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
