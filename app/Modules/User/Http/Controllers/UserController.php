<?php

namespace App\Modules\User\Http\Controllers;

use App\Modules\User\Http\Requests\CreateUserRequest;
use App\Modules\User\Http\Requests\UpdateUserRequest;
use App\Modules\User\Http\Resources\UserResource;
use App\Modules\User\Models\User;
use App\Modules\User\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController {
    public function __construct(
        private readonly UserService $service
    ) {}

    public function index(): AnonymousResourceCollection {
        return UserResource::collection(User::all());
    }

    public function create(CreateUserRequest $request): JsonResponse {
        $user = $this->service->create($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $user): UserResource {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource {
        $user = $this->service->update($user, $request->validated());

        return new UserResource($user);
    }

    public function destroy(User $user): JsonResponse {
        $this->service->delete($user);

        return response()->json(null, 204);
    }
}
