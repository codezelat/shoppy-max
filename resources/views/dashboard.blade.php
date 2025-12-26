<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-200">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-2">{{ __("Welcome back, ") }}<span class="text-primary-600 dark:text-primary-400">{{ Auth::user()->name }}</span>! 👋</h3>
                    <p class="mb-6 text-gray-600 dark:text-gray-400">{{ __("You're logged in and ready to manage your store.") }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Roles Section -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ __('Your Roles') }}
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @forelse(Auth::user()->roles as $role)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200 rounded-full text-sm font-medium border border-blue-200 dark:border-blue-800">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-gray-500 dark:text-gray-400 text-sm italic">{{ __('No roles assigned') }}</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Permissions Section -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ __('Your Permissions') }}
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @forelse(Auth::user()->getAllPermissions() as $permission)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200 rounded-full text-sm font-medium border border-green-200 dark:border-green-800">
                                        {{ $permission->name }}
                                    </span>
                                @empty
                                    <span class="text-gray-500 dark:text-gray-400 text-sm italic">{{ __('No permissions assigned') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    @can('view users')
                    <div class="mt-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700/50 rounded-lg flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                                {{ __('Admin Access Granted') }}
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                                <p>{{ __('You have elevated privileges to access the admin panel. Use the navigation menu above to manage users, roles, and system settings.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
