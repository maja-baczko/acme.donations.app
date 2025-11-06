<?php /** @noinspection ALL */

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService {
    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     * @throws \Throwable
     * @throws \Throwable
     */
    public function create(array $data): User {
        return DB::transaction(function () use ($data) {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Create user
            $user = User::create($data);

            // Assign default role if not provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $user->syncRoles($data['roles']);
            } elseif (!isset($data['roles'])) {
                // Assign default 'user' role if no roles provided
                $user->assignRole('user');
            }

            return $user->fresh();
        });
    }

    /**
     * Update an existing user
     *
     * @param User $user
     * @param array $data
     * @return User
     * @throws \Throwable
     * @throws \Throwable
     */
    public function update(User $user, array $data): User {
        return DB::transaction(function () use ($user, $data) {
            // Hash password only if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Sync roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $roles = $data['roles'];
                unset($data['roles']);
                $user->syncRoles($roles);
            }

            // Update user
            $user->update($data);

            return $user->fresh();
        });
    }

    /**
     * Delete a user
     *
     * @param User $user
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    public function delete(User $user): bool {
        return DB::transaction(function () use ($user) {
            // Prevent self-deletion
            if (auth()->check() && auth()->id() === $user->id) {
                throw new Exception('You cannot delete your own account.');
            }

            return $user->delete();
        });
    }

    /**
     * Sync user roles
     *
     * @param User $user
     * @param array $roles
     * @return User
     */
    public function syncRoles(User $user, array $roles): User {
        $user->syncRoles($roles);
        return $user->fresh();
    }

    /**
     * Assign a single role to user
     *
     * @param User $user
     * @param string $role
     * @return User
     */
    public function assignRole(User $user, string $role): User {
        $user->assignRole($role);
        return $user->fresh();
    }

    /**
     * Get active users (still_working = true)
     *
     * @return Collection
     */
    public function getActiveUsers(): Collection {
        return User::where('still_working', true)->get();
    }

    /**
     * Get users with their roles eager loaded
     *
     * @return Collection
     */
    public function getUsersWithRoles(): Collection {
        return User::with('roles')->get();
    }
}
