<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $role   = $request->query('role', 'player');
        $search = $request->query('search');

        $query = User::with('role');

        if ($role !== 'all') {
            $query->whereHas('role', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();
        $roles = Role::all();

        $totalPlayers = User::whereHas('role', fn($q) => $q->where('name', 'player'))->count();
        $totalCoaches = User::whereHas('role', fn($q) => $q->where('name', 'coach'))->count();

        return view('admin.users.index', compact('users', 'roles', 'role', 'search', 'totalPlayers', 'totalCoaches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:50',
            'email'        => 'required|email|unique:users',
            'phone_number' => 'required|string|size:9|unique:users',
            'role_id'      => 'required|exists:roles,id',
            'password'     => 'required|min:8|confirmed',
        ]);

        User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role_id'      => $validated['role_id'],
            'password'     => Hash::make($validated['password']),
            'rgpd_consent' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load(['role', 'reservations', 'classesByCoach']);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:50',
            'phone_number' => 'required|string|size:9|unique:users,phone_number,' . $user->id,
            'role_id'      => 'required|exists:roles,id',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // EVITAR QUE EL ADMIN SE BORRE A SÍ MISMO
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
