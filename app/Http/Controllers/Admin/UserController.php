<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $users = User::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q')->toString();

                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('role')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.users.form', ['adminUser' => new User(['role' => User::ROLE_ADMIN, 'active' => true])]);
    }

    public function store(AdminUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('status', 'Administrador creado.');
    }

    public function edit(Request $request, User $user)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.users.form', ['adminUser' => $user]);
    }

    public function update(AdminUserRequest $request, User $user)
    {
        $data = $request->validated();

        $this->ensureSuperAdminRemains($user, $data);

        if (! filled($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', 'Administrador actualizado.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        if ($request->user()->is($user)) {
            throw ValidationException::withMessages([
                'user' => 'No puedes eliminar tu propio usuario.',
            ]);
        }

        $this->ensureSuperAdminRemains($user, ['role' => User::ROLE_ADMIN, 'active' => false]);

        $user->delete();

        return back()->with('status', 'Administrador eliminado.');
    }

    private function ensureSuperAdminRemains(User $user, array $newData): void
    {
        if ($user->role !== User::ROLE_SUPER_ADMIN || ! $user->active) {
            return;
        }

        $willRemainSuperAdmin = ($newData['role'] ?? $user->role) === User::ROLE_SUPER_ADMIN
            && (bool) ($newData['active'] ?? $user->active);

        if ($willRemainSuperAdmin) {
            return;
        }

        $otherSuperAdmins = User::query()
            ->whereKeyNot($user->getKey())
            ->where('role', User::ROLE_SUPER_ADMIN)
            ->where('active', true)
            ->exists();

        if (! $otherSuperAdmins) {
            throw ValidationException::withMessages([
                'role' => 'Debe existir al menos un superadmin activo.',
            ]);
        }
    }
}
