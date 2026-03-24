<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\AdminRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;
use App\Interfaces\AdminRepositoryInterface;
use App\Models\Admin;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(AdminRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $admin = AdminResource::collection($this->crudRepository->all(
                [],
                [],
                ['*']
            ));
            return $admin->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(AdminRequest $request)
    {
        try {
            $admin = $this->crudRepository->create($request->validated());
            return new AdminResource($admin);
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function show(Admin $admin): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess('Item Fetched Successfully', new AdminResource($admin));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(AdminRequest $request, Admin $admin)
    {
        try {
            $this->crudRepository->update($request->validated(), $admin->id);
            activity()->performedOn($admin)->withProperties(['attributes' => $admin])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('admins', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Admin::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }




    public function forceDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecordsFinial(Admin::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

   public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);
        $loginKey = $request->email;
        $admin = Admin::where('email', $loginKey)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            if (Hash::needsRehash($admin->password)) {
                $admin->update([
                    'password' => Hash::make($request->password)
                ]);
            }
            activity()
                ->performedOn($admin)
                ->withProperties(['attributes' => $admin])
                ->log('login');
            $token = $admin->createToken('admin-token')->plainTextToken;
            return response()->json([
                'message' => 'Login success',
                'role'    => 'admin',
                'token'   => $token,
                'data'    => new AdminResource($admin),
            ]);
        }
        $user = User::where('phone', $loginKey)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('user-token')->plainTextToken;
            if ($user->role === 'company') {
                return response()->json([
                    'message' => 'Login success',
                    'role'    => 'company',
                    'token'   => $token,
                    'data'    => new CompanyResource($user),
                ]);
            }
            return response()->json([
                'message' => 'Login success',
                'role'    => 'user',
                'token'   => $token,
                'data'    => new UserResource($user),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ❌ Invalid credentials
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'result'  => 'Error',
            'message' => 'Invalid credentials',
        ], 401);
    }


    public function logout()
    {
        try {
            auth('admins')->user()->tokens()->delete();
            return response()->json(['message' => 'Successfully logged out']);
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage(), 401);
        }
    }

    public function getCurrentAdmin()
    {
        try {

            if (auth('admins')->check()) {
                $admin = auth('admins')->user();

                return response()->json([
                    'role' => 'admin',
                    'data' => new AdminResource($admin),
                ]);
            }


            if (auth()->check()) {
                $user = auth()->user();

                $user->load(['maintenances', 'products']);

                if ($user->role === 'company') {
                    return response()->json([
                        'role' => 'company',
                        'data' => new CompanyResource($user),
                    ]);
                }

                return response()->json([
                    'role' => 'user',
                    'data' => new UserResource($user),
                ]);
            }


            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);

        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage(), 401);
        }
    }

    /////////////////////////////// testing activity log ///////////////////////////////
}
