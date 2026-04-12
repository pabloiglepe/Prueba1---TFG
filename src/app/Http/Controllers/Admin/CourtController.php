<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    /**
     * LISTAS TODAS LAS PISTAS, ORDENADAS POR NOMBRE
     */
    public function index()
    {
        $courts = Court::orderBy('name')->get();
        return view('admin.courts.index', compact('courts'));
    }

    /**
     * FORMULARIO DE CREACIÓN
     */
    public function create()
    {
        return view('admin.courts.create');
    }

    /**
     * GUARDAR UNA NUEVA PISTA
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:30|unique:courts',
            'type'    => 'required|in:cristal,muro',
            'surface' => 'required|in:cesped,cemento',
        ]);

        Court::create($validated);

        return redirect()->route('admin.courts.index')
            ->with('success', 'Pista creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * FORMULARIO DE EDICIÓN
     */
    public function edit(Court $court)
    {
        $court->load('reservations');
        
        $futureReservations = $court->reservations()
        ->where('status', '!=', 'cancelled')
        ->where('reservation_date', '>=', today())
        ->exists();
        
        return view('admin.courts.edit', compact('court', 'futureReservations'));
    }

    /**
     * ACTUALIZAR PISTA
     */
    public function update(Request $request, Court $court)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:30|unique:courts,name,' . $court->id,
            'type'      => 'required|in:cristal,muro',
            'surface'   => 'required|in:cesped,cemento',
            'is_active' => 'boolean',
        ]);

        $court->update($validated);

        return redirect()->route('admin.courts.index')
            ->with('success', 'Pista actualizada correctamente.');
    }

    /**
     * ELIMINAR PISTA
     */
    public function destroy(Court $court)
    {
        $court->delete();

        return redirect()->route('admin.courts.index')
            ->with('success', 'Pista eliminada correctamente.');
    }
}
