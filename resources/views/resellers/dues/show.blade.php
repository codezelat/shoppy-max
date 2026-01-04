<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Statement of Accounts') }}
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
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="{{ route('reseller-dues.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">User Due Payments</a>
                        </div>
                    </li>
                    <li aria-current="page">
                         <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">{{ $reseller->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6 m-6">
        <!-- Header & Filters -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <!-- Reseller Info Card -->
            <div class="flex-1 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:justify-between md:items-start">
                    <div>
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $reseller->name }}</h5>
                        <p class="font-normal text-gray-700 dark:text-gray-400">{{ $reseller->business_name }}</p>
                        <p class="font-normal text-sm text-gray-500 mt-1">{{ $reseller->address }}, {{ $reseller->city }}</p>
                        <p class="font-normal text-sm text-gray-500">{{ $reseller->mobile }} | {{ $reseller->email }}</p>
                    </div>
                    <div class="mt-4 md:mt-0 text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Outstanding Due</p>
                        <p class="text-3xl font-bold {{ $reseller->due_amount > 0 ? 'text-red-600 dark:text-red-500' : 'text-green-600 dark:text-green-500' }}">
                            Rs. {{ number_format($reseller->due_amount, 2) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Date Filter -->
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <form method="GET" action="{{ route('reseller-dues.show', $reseller->id) }}" class="flex flex-col gap-2">
                    <div class="flex gap-2">
                        <div>
                            <label for="start_date" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">From</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        <div>
                            <label for="end_date" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">To</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Filter</button>
                        <a href="{{ route('reseller-dues.show', $reseller->id) }}" class="px-4 py-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statement Table -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Transaction History</h3>
            
            <div class="relative overflow-x-auto rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Date</th>
                            <th scope="col" class="px-6 py-3">Description</th>
                            <th scope="col" class="px-6 py-3">Reference</th>
                            <th scope="col" class="px-6 py-3 text-right text-red-600 dark:text-red-400">Debit (Order)</th>
                            <th scope="col" class="px-6 py-3 text-right text-green-600 dark:text-green-400">Credit (Payment)</th>
                            <th scope="col" class="px-6 py-3 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($statement as $transaction)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium {{ $transaction->type == 'Order' ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}">
                                    {{ $transaction->type }}
                                </span>
                                <span class="text-xs text-gray-500 block">{{ $transaction->description }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if(isset($transaction->url))
                                    <a href="{{ $transaction->url }}" class="hover:underline text-blue-500">
                                        {{ $transaction->reference }}
                                    </a>
                                @else
                                    {{ $transaction->reference }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-red-600 dark:text-red-400">
                                {{ $transaction->debit > 0 ? number_format($transaction->debit, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-green-600 dark:text-green-400">
                                {{ $transaction->credit > 0 ? number_format($transaction->credit, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                {{ number_format($transaction->balance, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No transactions found in this period.
                            </td>
                        </tr>
                        @endforelse

                        <!-- Balance Forward / Opening Balance (Show at bottom of list) -->
                        @if(abs($balanceForward) > 0.001 && $transactions->onLastPage())
                        <tr class="bg-gray-50 dark:bg-gray-700 font-semibold border-t dark:border-gray-600">
                            <td class="px-6 py-4" colspan="3">
                                {{ $startDate ? 'Balance Brought Forward (Pre-' . $startDate . ')' : 'Opening Balance / Adjustments' }}
                            </td>
                            <td class="px-6 py-4 text-right"></td>
                            <td class="px-6 py-4 text-right"></td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                {{ number_format($balanceForward, 2) }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
