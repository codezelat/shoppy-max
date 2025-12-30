<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Seller Targets') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <a href="{{ route('sellers.targets.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 shadow-md transition-transform hover:scale-105 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add New Target
                        </a>
                        
                         <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                             <form method="GET" action="{{ route('sellers.targets.index') }}" class="flex w-full md:w-auto">
                                <div class="relative w-full">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}" class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-l-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search..." required />
                                </div>
                                <button type="submit" class="p-2.5 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                    <span class="sr-only">Search</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    @if(session('success'))
                         <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-300 dark:border-green-800" role="alert">
                            <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                            </svg>
                            <span class="sr-only">Success</span>
                            <div>
                                <span class="font-medium">Success!</span> {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    <div class="relative overflow-x-auto rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Start/End Date</th>
                                    <th scope="col" class="px-6 py-3">Target User</th>
                                    <th scope="col" class="px-6 py-3">Type</th>
                                    <th scope="col" class="px-6 py-3">Target Qty</th>
                                    <th scope="col" class="px-6 py-3">Target Done Price</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($targets as $target)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-gray-900 dark:text-white">
                                            {{ $target->start_date->format('Y-m-d') }} <br> <span class="text-gray-500">to</span> {{ $target->end_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $target->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                             <span class="px-3 py-1 text-xs font-medium leading-tight text-purple-800 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-300">
                                                {{ $target->target_type }}
                                             </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white font-semibold">{{ $target->target_pieces_qty }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-green-600 dark:text-green-400 font-bold">{{ number_format($target->target_completed_price, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="flex w-3 h-3 bg-green-500 rounded-full"></span> <!-- Placeholder status -->
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                   <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="7" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                                <svg class="w-16 h-16 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p class="text-lg font-medium">No targets found</p>
                                                <p class="text-sm">Create a new target to see it listed here.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $targets->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
