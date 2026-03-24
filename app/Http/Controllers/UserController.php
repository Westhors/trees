<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\UserRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\EngineerResource;
use App\Http\Resources\LogResource;
use App\Http\Resources\MaintenanceResource;
use App\Http\Resources\MerchantResource;
use App\Http\Resources\SellerResource;
use App\Http\Resources\UserIndexResource;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Mail\UserMail;
use App\Mail\UserRequestMail;
use App\Models\ContactPeople;
use App\Models\EmailTemplate;
use App\Models\Engineer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class UserController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(UserRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $user = UserResource::collection($this->crudRepository->all(
                [],
                [],
                ['*']
            ));
            return $user->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(User $user): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess('Item fetched successfully', new UserResource($user));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(UserRequest $request, User $user)
    {
        try {
            $this->crudRepository->update($request->validated(), $user->id);
            if (request('image') !== null) {
                $user = User::find($user->id);
                $image = $this->crudRepository->AddMediaCollection('image', $user);
            }
            activity()->performedOn($user)->withProperties(['attributes' => $user])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }




    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('users', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(User::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecordsFinial(User::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

/////////////////////////////////////////////////////////// application  ///////////////////////////////////////


    public function applicationForm(UserRequest $request)
    {
        try {
            $data = $request->validated();

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $data['created_from'] = 'user';
            $user = $this->crudRepository->create($data);
            if (request('image') !== null) {
                $this->crudRepository->AddMediaCollection('image', $user);
            }
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Wrong credentials'], 401);
        }

        // شرط التفعيل
        if (in_array($user->role, ['engineer', 'seller', 'merchant', 'company']) && $user->active != 1) {
            return response()->json(['message' => 'Your account is not active yet'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // if ($user->role === 'engineer') {
        //     return response()->json([
        //         'message' => 'Login success',
        //         'type'    => 'engineer',
        //         'token'   => $token,
        //         'data'    => new EngineerResource($user),
        //     ]);
        // }




        return response()->json([
            'message' => 'Login success',
            'type'    => 'user',
            'token'   => $token,
            'data'    => new UserResource($user),
        ]);
    }



    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }



   public function checkAuth(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $user->load(['maintenances','products']);

            // if ($user->role === 'engineer') {
            //     return response()->json(['data' => new EngineerResource($user)]);
            // }

           
            return response()->json(['data' => new UserResource($user)]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteAccount()
    {
        $user = auth()->user();
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Account deleted'
        ]);
    }


/////////////////////////////////////////////////////////// application  ///////////////////////////////////////
}
