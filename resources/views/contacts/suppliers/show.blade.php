<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Supplier Details') }}
            </h2>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                            <a href="{{ route('suppliers.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Suppliers</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Details</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Profile Header Card -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <div>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $supplier->name }}</h3>
                    @if($supplier->business_name)
                        <p class="text-xl text-gray-600 dark:text-gray-400 font-medium mt-1">{{ $supplier->business_name }}</p>
                    @endif
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-purple-900 dark:text-purple-300">
                            Supplier
                        </span>
                        <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-gray-700 dark:text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $supplier->city ?? 'Unknown City' }}
                        </span>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800 flex items-center shadow-md transition-transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Supplier
                    </a>
                    
                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline-block" data-confirm-message="Are you sure you want to delete this supplier? This action cannot be undone.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 flex items-center shadow-md transition-transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Contact Details -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 h-full hover:shadow-md transition-shadow">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center border-b border-gray-200 dark:border-gray-700 pb-2">
                     <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Contact Info
                </h4>
                <ul class="space-y-4 text-gray-600 dark:text-gray-400">
                    <li class="flex items-center">
                        <span class="font-semibold w-24 text-gray-900 dark:text-gray-300">Mobile:</span>
                        <span class="text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ $supplier->mobile }}</span>
                    </li>
                     <li class="flex items-center">
                        <span class="font-semibold w-24 text-gray-900 dark:text-gray-300">Landline:</span>
                        <span class="text-gray-900 dark:text-white">{{ $supplier->landline ?? '-' }}</span>
                    </li>
                     <li class="flex items-center">
                        <span class="font-semibold w-24 text-gray-900 dark:text-gray-300">Email:</span>
                        <span class="text-gray-900 dark:text-white">{{ $supplier->email ?? '-' }}</span>
                    </li>
                </ul>
            </div>

            <!-- Address & Financial Details -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 h-full hover:shadow-md transition-shadow">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center border-b border-gray-200 dark:border-gray-700 pb-2">
                     <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Details
                </h4>
                 <ul class="space-y-4 text-gray-600 dark:text-gray-400">
                    <li class="flex items-start">
                        <span class="font-semibold w-24 text-gray-900 dark:text-gray-300">Address:</span>
                        <span class="text-gray-900 dark:text-white">{{ $supplier->address }}</span>
                    </li>
                     <li class="flex items-start">
                        <span class="font-semibold w-24 text-gray-900 dark:text-gray-300">City:</span>
                        <span class="text-gray-900 dark:text-white">{{ $supplier->city }}</span>
                    </li>
                     <li class="flex items-start">
                        <span class="font-semibold w-24 text-gray-900 dark:text-gray-300">Country:</span>
                        <span class="text-gray-900 dark:text-white">{{ $supplier->country }}</span>
                    </li>

                    @if($supplier->country === 'Sri Lanka' || $supplier->province)
                    <li class="flex items-start">
                        <span class="font-semibold w-24 text-gray-900 dark:text-gray-300">Province:</span>
                         <span class="text-gray-900 dark:text-white">{{ $supplier->province ?? '-' }}</span>
                    </li>
                    @endif

                    @if($supplier->country === 'Sri Lanka' || $supplier->district)
                    <li class="flex items-start">
                         <span class="font-semibold w-24 text-gray-900 dark:text-gray-300">District:</span>
                         <span class="text-gray-900 dark:text-white">{{ $supplier->district ?? '-' }}</span>
                    </li>
                    @endif
                 </ul>
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-end">
             <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-colors">
                <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>
</x-app-layout>
