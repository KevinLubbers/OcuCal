<?php

use Livewire\Volt\Component;
use Carbon\Carbon;
use App\Models\CalendarDate;
use Livewire\Attributes\On;

new class extends Component {
    public int $year;
    public array $months = [];
    public array $days = [];


    public function mount() {
        $this->year = now()->year;
        $this->days = range(1, 31);
        $this->months = collect(range(1, 12))
            ->map(fn ($month) => Carbon::create($this->year, $month, 1))
            ->all();
    }
    public function getAddedProperty() {
        $rows = CalendarDate::where('user_id', auth()->id())
            ->whereYear('date', $this->year)
            ->select('date', 'type')
            ->get();

        $added= [];
        foreach ($rows as $row) {
            $added[$row->date][$row->type] = true;
        }

        return $added;
    }
    #[On('toggle')]
    public function toggle($date, $type) {
        $user = auth()->user();

        $existing = CalendarDate::where('user_id', $user->id)
        ->whereDate('date', $date)
        ->where('type', $type)
        ->first();

        if ($existing) {
            $existing->delete();
        } else {
            CalendarDate::create([
                'user_id' => $user->id,
                'date' => $date,
                'type' => $type,
            ]);
        }
    }

    public function nextYear()
    {
        $this->year++;
        $this->generateMonths();

    }

    public function prevYear()
    {
        $this->year--;
        $this->generateMonths();

    }

    private function generateMonths()
    {
        $this->months = collect(range(1, 12))
            ->map(fn ($month) => Carbon::create($this->year, $month, 1))
            ->all();

    }
}; ?>


<div class="bg-white dark:text-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/100 dark:via-transparent border-b border-gray-200 dark:border-gray-700"
    x-data="{ period: false, fertility: false, sex: false, orgasms: false, medication: false, pregnancy: false, clearAll: false, showAll: false , unlocked: false, locked_url: 'imgs/locked.png', unlocked_url: 'imgs/unlocked.png', dragging: false}"
    @pointerdown="dragging = true"
    @pointerup.window="dragging = false"
    @pointercancel.window="dragging = false" > 

    <h1 x-text="!unlocked ? 'Viewing Calendar' : 'Editing Calendar'" class="text-2xl font-medium text-gray-900 dark:text-white">
    </h1>

    <div class="overflow-auto">
        <div class="flex items-center gap-2 mt-2 mb-2 pl-2">
            <button @click="$wire.prevYear()">←</button>
            <span class="font-bold text-lg">{{ $year }}</span>
            <button @click="$wire.nextYear()">→</button>
        </div>
        <div class="flex flex-row flex-wrap gap-x-6 gap-y-2 pl-2">
            <div class="flex items-center gap-2">
                <x-checkbox x-model="period" />
                <x-label for="period" x-text="!unlocked ? 'Show Period' : 'Add Period'" />
                <div class="mx-auto w-4 h-4 rounded-full bg-red-800"></div>
            </div>
            <div class="flex items-center gap-2">
                <x-checkbox x-model="fertility" />
                <x-label for="fertility" x-text="!unlocked ? 'Show Fertility' : 'Add Fertility'" />
                <div class="mx-auto w-4 h-4 rounded-full bg-orange-600"></div>
            </div>
            <div class="flex items-center gap-2">
                <x-checkbox x-model="sex"  />
                <x-label for="sex" x-text="!unlocked ? 'Show Sexual Activity' : 'Add Sexual Activity'" />
                <div class="mx-auto w-4 h-4 rounded-full bg-purple-800"></div>
            </div>
            <div class="flex items-center gap-2">
                <x-checkbox x-model="orgasms" />
                <x-label for="orgasms" x-text="!unlocked ? 'Show Orgasms' : 'Add Orgasms'" />
                <div class="mx-auto w-4 h-4 rounded-full bg-indigo-500"></div>
            </div>
            <div class="flex items-center gap-2">
                <x-checkbox x-model="medication" />
                <x-label for="medication" x-text="!unlocked ? 'Show Medication' : 'Add Medication'" />
                <div class="mx-auto w-4 h-4 rounded-full bg-green-600"></div>
            </div>
            <div class="flex items-center gap-2">
                <x-checkbox x-model="pregnancy" />
                <x-label for="pregnancy" x-text="!unlocked ? 'Show Pregnancy' :'Add Pregnancy'" />
                <div class="mx-auto w-4 h-4 rounded-full bg-blue-500"></div>
            </div>
        </div>
        <div class="flex flex-row flex-wrap gap-x-6 gap-y-2 mt-2 pl-2">
            <div class="flex items-center gap-2">
                <x-checkbox x-model="clearAll" @click="!clearAll ? (period = false, fertility = false, sex = false, orgasms = false, medication = false, pregnancy = false, showAll = false, clearAll = false) : null" />
                <x-label for="clearAll" value="Clear All" />
            </div>
            <div class="flex items-center gap-2">
                <x-checkbox x-model="showAll" @click="!showAll ? (period = true, fertility = true, sex = true, orgasms = true, medication = true, pregnancy = true, clearAll = false) : null" />
                <x-label for="showAll" value="Show All" />
            </div>
        </div>
        <div class="flex flex-row flex-wrap gap-x-6 gap-y-2 mt-2 mb-2 pl-2 items-center">
            <img x-bind:src="unlocked ? unlocked_url : locked_url" alt="Locked / Unlocked Icon" class="h-6 w-6">
            <x-button class="text-center" x-text="unlocked ? 'Click to Lock Calendar' : 'Click to Unlock Calendar'" @click="unlocked = !unlocked"></x-button>
        </div>
    </div>


    <div class="overflow-auto" style="user-select:none; -webkit-user-select:none;" wire:key="year-{{ $year }}" x-data="{ added: @js($this->added) }">
        <table class="table-auto w-full text-sm">
            <thead>
                <tr>
                    <th class="p-2 sticky left-0 bg-white dark:bg-gray-800 z-10">Day</th>

                    @foreach ($months as $month)
                        <th class="p-2 text-center">
                            {{ $month->format('M') }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach ($days as $day)
                    <tr>
                        <td class="font-bold text-center sticky left-0 bg-white dark:bg-gray-800 z-10">{{ $day }}</td>

                        @foreach ($months as $month)
                            <td class="text-center align-middle border-l border-r border-gray-700 dark:border-gray-200">
                                @if ($day <= $month->daysInMonth)
                                @php
                                    $date = Carbon::create($this->year, $month->month, $day)->format('Y-m-d');
                                @endphp
                                <div x-data="{ rowdate: '{{$date}}' }">
                                <div class="flex flex-row" wire:ignore>
                                    <div x-show="period" @pointerenter="if (!unlocked || !dragging) return; added[rowdate] ??= {}; added[rowdate]['period'] = !added[rowdate]['period']; toggleCalendar(rowdate, 'period');" @pointerdown="if (!unlocked) return; added[rowdate] ??= {}; added[rowdate]['period'] = !added[rowdate]['period']; toggleCalendar(rowdate, 'period');" x-bind:style="$data.unlocked ? 'cursor: pointer' : 'cursor: default'" x-bind:class="(added[rowdate]?.period ?? false) ? 'bg-red-800' : 'bg-gray-200 dark:bg-gray-700'" class="mx-auto w-4 h-4 rounded-full"></div>
                                    <div x-show="fertility" @pointerenter="if (!unlocked || !dragging) return; added[rowdate] ??= {}; added[rowdate]['fertility'] = !added[rowdate]['fertility']; toggleCalendar(rowdate, 'fertility');" @pointerdown="if (!unlocked) return; added[rowdate] ??= {}; added[rowdate]['fertility'] = !added[rowdate]['fertility']; toggleCalendar(rowdate, 'fertility');" x-bind:style="$data.unlocked ? 'cursor: pointer' : 'cursor: default'" x-bind:class="(added[rowdate]?.fertility ?? false) ? 'bg-orange-600' : 'bg-gray-200 dark:bg-gray-700'" class="mx-auto w-4 h-4 rounded-full"></div>
                                    <div x-show="sex" @pointerenter="if (!unlocked || !dragging) return; added[rowdate] ??= {}; added[rowdate]['sex'] = !added[rowdate]['sex']; toggleCalendar(rowdate, 'sex');" @pointerdown="if (!unlocked) return; added[rowdate] ??= {}; added[rowdate]['sex'] = !added[rowdate]['sex']; toggleCalendar(rowdate, 'sex');" x-bind:style="$data.unlocked ? 'cursor: pointer' : 'cursor: default'" x-bind:class="(added[rowdate]?.sex ?? false) ? 'bg-purple-800' : 'bg-gray-200 dark:bg-gray-700'" class="mx-auto w-4 h-4 rounded-full"></div>
                                    <div x-show="orgasms" @pointerenter="if (!unlocked || !dragging) return; added[rowdate] ??= {}; added[rowdate]['orgasms'] = !added[rowdate]['orgasms']; toggleCalendar(rowdate, 'orgasms');" @pointerdown="if (!unlocked) return; added[rowdate] ??= {}; added[rowdate]['orgasms'] = !added[rowdate]['orgasms']; toggleCalendar(rowdate, 'orgasms');" x-bind:style="$data.unlocked ? 'cursor: pointer' : 'cursor: default'" x-bind:class="(added[rowdate]?.orgasms ?? false) ? 'bg-indigo-500' : 'bg-gray-200 dark:bg-gray-700'" class="mx-auto w-4 h-4 rounded-full"></div>
                                    <div x-show="medication" @pointerenter="if (!unlocked || !dragging) return; added[rowdate] ??= {}; added[rowdate]['medication'] = !added[rowdate]['medication']; toggleCalendar(rowdate, 'medication');" @pointerdown="if (!unlocked) return; added[rowdate] ??= {}; added[rowdate]['medication'] = !added[rowdate]['medication']; toggleCalendar(rowdate, 'medication');" x-bind:style="$data.unlocked ? 'cursor: pointer' : 'cursor: default'" x-bind:class="(added[rowdate]?.medication ?? false) ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-700'" class="mx-auto w-4 h-4 rounded-full"></div>
                                    <div x-show="pregnancy" @pointerenter="if (!unlocked || !dragging) return; added[rowdate] ??= {}; added[rowdate]['pregnancy'] = !added[rowdate]['pregnancy']; toggleCalendar(rowdate, 'pregnancy');" @pointerdown="if (!unlocked) return; added[rowdate] ??= {}; added[rowdate]['pregnancy'] = !added[rowdate]['pregnancy']; toggleCalendar(rowdate, 'pregnancy');" x-bind:style="$data.unlocked ? 'cursor: pointer' : 'cursor: default'" x-bind:class="(added[rowdate]?.pregnancy ?? false) ? 'bg-blue-500' : 'bg-gray-200 dark:bg-gray-700'" class="mx-auto w-4 h-4 rounded-full"></div>
                                </div>
                                </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
