<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ $storeLabel }} Racks</h2>
                <nav class="mt-1 flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                        <li><a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">Dashboard</a></li>
                        <li class="text-gray-400">/</li>
                        <li><a href="{{ route('purchases.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">Purchases</a></li>
                        <li class="text-gray-400">/</li>
                        <li><span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $storeLabel }} Racks</span></li>
                    </ol>
                </nav>
            </div>
        </div>
    </x-slot>

    @include('purchases.partials.store-tabs', ['store' => $store])

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-6 py-3">Row</th>
                        <th class="px-6 py-3">Store</th>
                        <th class="px-6 py-3">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($racks as $rack)
                        <tr class="border-b bg-white dark:border-gray-700 dark:bg-gray-800">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $rack->row_name }}</td>
                            <td class="px-6 py-4">{{ $storeLabel }}</td>
                            <td class="px-6 py-4">{{ optional($rack->created_at)->format('d M Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No rack rows created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-6 py-4">
                {{ $racks->links() }}
            </div>
        </div>

        <form method="POST" action="{{ route('purchases.store-racks.store', $store) }}" class="h-fit rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            @csrf
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Create Rack Row</h3>
            <div class="mt-4">
                <label for="row_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Row</label>
                <input id="row_name" name="row_name" value="{{ old('row_name') }}" required maxlength="100" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                @error('row_name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="mt-5 inline-flex items-center rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700">
                Create Row
            </button>
        </form>
    </div>
</x-app-layout>
