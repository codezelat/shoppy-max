<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Reseller Targets') }}
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
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Reseller Targets</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-gray-800">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 md:space-x-4 mb-6">
            <!-- Search & Filter -->
            <div class="flex-1 w-full md:max-w-xl">
                <form method="GET" action="{{ route('reseller-targets.index') }}" class="flex gap-2">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                        </div>
                        <input type="search" name="search" value="{{ request('search') }}" class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Search by Reseller or Ref ID..." />
                    </div>
                    <select name="target_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-40 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="daily" {{ request('target_type') == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ request('target_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ request('target_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('reseller-targets.create') }}" class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800 transition-transform transform hover:scale-105 shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Target
                </a>
            </div>
        </div>

        <div class="relative overflow-x-auto sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Reseller</th>
                        <th scope="col" class="px-6 py-3">Type</th>
                        <th scope="col" class="px-6 py-3">Period</th>
                        <th scope="col" class="px-6 py-3 text-center">Target Qty</th>
                        <th scope="col" class="px-6 py-3 text-right">Target Amount</th>
                        <th scope="col" class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($targets as $target)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <td class="px-6 py-4">
                             <div class="flex flex-col">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $target->reseller->name }}</span>
                                <span class="text-xs text-gray-500">{{ $target->reseller->business_name }}</span>
                                @if($target->ref_id)
                                    <span class="text-xs text-gray-400 mt-1">Ref: {{ $target->ref_id }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded border
                                {{ $target->target_type == 'daily' ? 'bg-blue-100 text-blue-800 border-blue-400 dark:bg-gray-700 dark:text-blue-400' : '' }}
                                {{ $target->target_type == 'weekly' ? 'bg-purple-100 text-purple-800 border-purple-400 dark:bg-gray-700 dark:text-purple-400' : '' }}
                                {{ $target->target_type == 'monthly' ? 'bg-green-100 text-green-800 border-green-400 dark:bg-gray-700 dark:text-green-400' : '' }}">
                                {{ ucfirst($target->target_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($target->start_date && $target->end_date)
                                {{ $target->start_date->format('M d') }} - {{ $target->end_date->format('M d, Y') }}
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-gray-700 dark:text-gray-300">
                            {{ $target->target_pcs_qty }}
                        </td>
                        <td class="px-6 py-4 text-right">
                             @if($target->target_completed_price)
                                {{ number_format($target->target_completed_price, 2) }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('reseller-targets.edit', $target) }}" class="p-2 text-yellow-600 hover:bg-yellow-100 rounded-lg dark:text-yellow-400 dark:hover:bg-gray-700 transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('reseller-targets.destroy', $target) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this target?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg dark:text-red-400 dark:hover:bg-gray-700 transition-colors" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-6">
                                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-base">No targets found.</p>
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
</x-app-layout>
