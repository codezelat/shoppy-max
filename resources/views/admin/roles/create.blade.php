<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New Role') }}
            </h2>
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                &larr; Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    
                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf

                        <!-- Role Info -->
                        <div class="mb-8">
                             <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">1</span>
                                Role Information
                            </h3>
                            <div>
                                <x-input-label for="name" :value="__('Role Name')" />
                                <x-text-input id="name" class="block mt-1 w-full max-w-md" type="text" name="name" :value="old('name')" required autofocus placeholder="e.g., Manager, Editor" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="mb-8 border-t border-gray-100 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">2</span>
                                Assign Permissions
                            </h3>
                            
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Select what users with this role can do.</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($permissions as $permission)
                                    <div class="relative flex items-start py-2 px-3 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer" onclick="document.getElementById('perm_{{ $permission->id }}').click()">
                                        <div class="flex items-center h-5">
                                            <input id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                            {{ (is_array(old('permissions')) && in_array($permission->name, old('permissions'))) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="perm_{{ $permission->id }}" class="font-medium text-gray-700 dark:text-gray-300 select-none cursor-pointer">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                             <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                             <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-sm font-medium">Cancel</a>
                            <x-primary-button class="ml-3">
                                {{ __('Create Role') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
