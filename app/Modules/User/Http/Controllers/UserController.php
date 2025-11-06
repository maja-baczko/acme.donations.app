<?php

namespace App\Modules\User\Http\Controllers;

use App\Modules\User\Http\Resources\UserResource;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UserController {
    use AuthorizesRequests;

    public function index() {
        $this->authorize('viewAny', User::class);

        return UserResource::collection(User::all());
    }

    public function create(Request $request) {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'firstname' => ['required'],
            'lastname' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required'],
            'department' => ['required'],
            'function' => ['required'],
            'still_working' => ['required'],
            'role_id' => ['required', 'exists:roles'],
            'profile' => ['required', 'exists:images'],
        ]);

        return new UserResource(User::create($data));
    }

    public function show(User $user) {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    public function update(Request $request, User $user) {
        $this->authorize('update', $user);

        $data = $request->validate([
            'firstname' => ['required'],
            'lastname' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required'],
            'department' => ['required'],
            'function' => ['required'],
            'still_working' => ['required'],
            'role_id' => ['required', 'exists:roles'],
            'profile' => ['required', 'exists:images'],
        ]);

        $user->update($data);

        return new UserResource($user);
    }

    public function destroy(User $user) {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json();
    }
}
