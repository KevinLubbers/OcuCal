<?php

use Livewire\Volt\Component;

new class extends Component {
    
}; ?>

<div x-data="{showExport: false}">
    <x-button class="ml-4 mt-4" @click="showExport = true">Export User Data</x-button>
    <div x-show="showExport">
    <x-dialog-modal>
        <x-slot name="title">
            <div >{{ __('Exporting Data') }}</div>
            <x-label class="text-sm wrap" value="Hint: You can select multiple years and data types with Alt + Click (or Command + Click on Mac)" />
        </x-slot>

        <x-slot name="content">
            <div class="mt-4">
                <x-label for="range" value="{{ __('Select a Year') }}" />    
                <select wire:model.defer="range" multiple class="mt-1 block mb-2 rounded-md text-gray-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-[#97CA3D] dark:focus:border-[#97CA3D] focus:ring-[#97CA3D] dark:focus:ring-[#97CA3D] shadow-sm">
                    <option value="all">All Time</option>
                    @foreach(range(now()->year - 5, now()->year + 5) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
                <x-label for="type" value="{{ __('Select a Data Type') }}" />    
                <select wire:model.defer="type" multiple class="mt-1 block mb-2 rounded-md text-gray-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-[#97CA3D] dark:focus:border-[#97CA3D] focus:ring-[#97CA3D] dark:focus:ring-[#97CA3D] shadow-sm">
                    <option value="all">All Types</option>
                    <option value="period">Period</option>
                    <option value="fertility">Fertility</option>
                    <option value="sex">Sexual Activity</option>
                    <option value="pregnancy">Pregnancy</option>
                    <option value="medication">Medication</option>
                    <option value="orgasms">Orgasms</option>
                </select>
                <x-label for="format" value="{{ __('Select a Format') }}" />    
                <select wire:model.defer="format" class="mt-1 block mb-2 rounded-md text-gray-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-[#97CA3D] dark:focus:border-[#97CA3D] focus:ring-[#97CA3D] dark:focus:ring-[#97CA3D] shadow-sm">
                    <option value="json">JSON</option>
                    <option value="sqlite">SQLite Database File</option>
                    <option value="csv">CSV</option>
                    <option value="html">HTML</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button @click="showExport = false" >
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ml-3" @click="showExport = false"  >
                {{ __('Export') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
    </div>
</div>
