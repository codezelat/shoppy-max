<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reseller Targets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <a href="{{ route('resellers.targets.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New Target
                        </a>
                        <div class="flex space-x-2">
                             <form method="GET" action="{{ route('resellers.targets.index') }}" class="flex">
                                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" class="border rounded-l px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-r">Filter</button>
                            </form>
                        </div>
                    </div>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start/End Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Qty</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Done Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($targets as $target)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                                            {{ $target->start_date->format('Y-m-d') }} <br> to {{ $target->end_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $target->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $target->target_type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $target->target_pieces_qty }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($target->target_completed_price, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Active</td> <!-- Placeholder status -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No targets found.</td>
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
