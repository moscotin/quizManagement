@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#333398] text-start text-base font-medium text-[#333398] bg-indigo-50 dark:bg-[#2a2a2a] dark:text-[#5555CC] focus:outline-none focus:text-[#333398] dark:focus:text-[#5555CC] focus:bg-indigo-100 dark:focus:bg-[#2a2a2a] focus:border-[#333398] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-800 dark:focus:text-gray-300 focus:bg-gray-50 dark:focus:bg-gray-900 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
