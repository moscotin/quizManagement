@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-600 dark:bg-[#2a2a2a] dark:text-gray-200 focus:border-[#333398] focus:ring-[#333398] rounded-md shadow-sm']) }}>
