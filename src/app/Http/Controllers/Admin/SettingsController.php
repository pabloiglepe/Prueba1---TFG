<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = ClubSetting::all()->pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'opening_time'         => 'required|date_format:H:i',
            'closing_time'         => 'required|date_format:H:i|after:opening_time',
            'slot_interval'        => 'required|integer|in:15,30,60',
            'reservation_duration' => 'required|integer|in:60,90,120',
            'price_day'            => 'required|numeric|min:0',
            'price_night'          => 'required|numeric|min:0',
        ], [
            'closing_time.after' => 'La hora de cierre debe ser posterior a la de apertura.',
        ]);

        foreach ($validated as $key => $value) {
            ClubSetting::set($key, $value);
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}
