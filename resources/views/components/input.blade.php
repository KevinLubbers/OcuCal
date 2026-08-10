@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#97CA3D] dark:focus:border-[#97CA3D] focus:ring-[#97CA3D] dark:focus:ring-[#97CA3D] rounded-md shadow-sm']) !!}>
