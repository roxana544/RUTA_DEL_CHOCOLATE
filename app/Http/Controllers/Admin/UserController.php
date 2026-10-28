<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.users.index', [
            'users' => User::withCount('enrollments')
                ->when($request->query('q'), fn ($q, $t) => $q->where(fn ($w) => $w->where('name', 'like', "%{$t}%")->orWhere('email', 'like', "%{$t}%")))
                ->when($request->query('rol'), fn ($q, $r) => $q->where('role', $r))
                ->orderBy('name')->paginate(25)->withQueryString(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', Rule::in(array_keys(User::ROLES))]]);

        if ($user->is($request->user()) && $data['role'] !== User::ROLE_ADMIN) {
            return back()->with('error', 'No puedes quitarte tu propio rol de administrador.');
        }
        $user->update($data + ['is_admin' => $data['role'] === User::ROLE_ADMIN]);

        return back()->with('status', 'Rol actualizado.');
    }
}
