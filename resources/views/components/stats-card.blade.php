@props(['title', 'value', 'icon', 'color' => 'primary'])

<div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 flex items-center justify-between hover:shadow-md transition-shadow">
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $title }}</p>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $value }}</h3>
    </div>
    <div class="p-3 bg-{{ $color }}-100 rounded-full dark:bg-{{ $color }}-900 text-{{ $color }}-600 dark:text-{{ $color }}-300">
        {{ $slot }}
    </div>
</div>