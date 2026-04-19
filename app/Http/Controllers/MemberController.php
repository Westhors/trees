<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\AddMemberRequest;
use App\Http\Requests\MemberRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\MemberResource;
use App\Http\Resources\MemberTreeResource;
use App\Interfaces\MemberRepositoryInterface;
use App\Models\Admin;
use App\Models\Member;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(MemberRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $message = MemberResource::collection($this->crudRepository->all(
                ['children', 'branch', 'father'],
                [],
                ['*']
            ));
            return $message->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function show(Member $member): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess('Item fetched successfully', new MemberResource($member));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(MemberRequest $request)
    {
        try {
           $member = $this->crudRepository->create($request->validated());
            if (request('image') !== null) {
                $this->crudRepository->AddMediaCollection('image', $member);
            }
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


   public function applicationForm(MemberRequest $request)
    {
        try {
            $data = $request->validated();

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $member = $this->crudRepository->create($data);

            $member->update([
                'application_number' => '#' . str_pad($member->id, 6, '0', STR_PAD_LEFT)
            ]);

            if ($request->hasFile('image')) {
                $this->crudRepository->AddMediaCollection('image', $member);
            }

            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
    

    public function update(MemberRequest $request, Member $member): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validated();

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']); // يمنع تحديثها بقيمة فارغة
            }

            $this->crudRepository->update($data, $member->id);

            if ($request->hasFile('image')) {
                $member = Member::find($member->id);
                $this->crudRepository->AddMediaCollection('image', $member);
            }
            activity()
                ->performedOn($member)
                ->withProperties(['attributes' => $member])
                ->log('update');

            return JsonResponse::respondSuccess(
                trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY)
            );

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('members', $request['ids']);
            return  JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Member::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function tree()
    {
        $roots = Member::whereNull('father_id')->where('active', true)
                        ->with('children.branch','children.father','branch') // eager loading
                        ->get();
        return MemberTreeResource::collection($roots);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        // 🔹 أول حاجة: دور على Admin
        $admin = Admin::where('phone', $request->phone)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {

            $token = $admin->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login success',
                'type'    => 'admin',
                'token'   => $token,
                'data'    => new AdminResource($admin),
            ]);
        }

        // 🔹 لو مش Admin، دور على Member
        $member = Member::where('phone', $request->phone)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json(['message' => 'Wrong credentials'], 401);
        }

        if ($member->active != 1) {
            return response()->json(['message' => 'Your account is not active yet'], 403);
        }

        $token = $member->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'type'    => 'member',
            'token'   => $token,
            'data'    => new MemberResource($member),
        ]);
    }


    public function checkAuth(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // لو Admin
            if ($user instanceof \App\Models\Admin) {
                return response()->json([
                    'type' => 'admin',
                    'data' => new AdminResource($user)
                ]);
            }

            // لو Member
            if ($user instanceof \App\Models\Member) {
                $user->load([
                    'father',
                    'children',
                    'branch',
                    'events',
                    'eventAttendances',
                    'attendingEvents'
                ]);

                return response()->json([
                    'type' => 'member',
                    'data' => new MemberResource($user)
                ]);
            }

            return response()->json([
                'message' => 'Unknown user type'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
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


    public function addMember(AddMemberRequest $request)
    {
        try {
            $data = $request->validated();

            $user = auth()->user();

            // مهم جداً: هل اليوزر نفسه member؟
            // لو عندك relation
            $currentMember = $user->member ?? $user;

            // دايماً branch من اليوزر
            $data['branch_id'] = $currentMember->branch_id;

            // تشفير الباسورد
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // =========================
            // حالة إضافة ابن
            // =========================
            if ($data['type'] === 'son') {

                $data['father_id'] = $currentMember->id;

                $member = $this->crudRepository->create($data);
                if (request('image') !== null) {
                    $this->crudRepository->AddMediaCollection('image', $member);
                }
            }

            // =========================
            // حالة إضافة أب
            // =========================
            elseif ($data['type'] === 'father') {

                // إنشاء الأب
                $member = $this->crudRepository->create($data);
                if (request('image') !== null) {
                    $this->crudRepository->AddMediaCollection('image', $member);
                }
                // ربط المستخدم الحالي بالأب الجديد
                $currentMember->update([
                    'father_id' => $member->id
                ]);
            }

            // application number
            $member->update([
                'application_number' => '#' . str_pad($member->id, 6, '0', STR_PAD_LEFT)
            ]);

            // رفع صورة
            if ($request->hasFile('image')) {
                $this->crudRepository->AddMediaCollection('image', $member);
            }

            return JsonResponse::respondSuccess('تم إضافة العضو بنجاح');

        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}
