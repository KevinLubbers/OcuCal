<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarDate;

class CalendarToggleController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'type' => ['required', 'in:period,fertility,sex,orgasms,medication,pregnancy'],
        ]);

        $user = $request->user();

        $existing = CalendarDate::where('user_id', $user->id)
            ->whereDate('date', $data['date'])
            ->where('type', $data['type'])
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            CalendarDate::create([
                'user_id' => $user->id,
                'date' => $data['date'],
                'type' => $data['type'],
            ]);
        }

        return response()->json(['ok' => true]);
    }
}