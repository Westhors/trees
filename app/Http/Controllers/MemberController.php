<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\MemberRequest;
use App\Http\Resources\MemberResource;
use App\Http\Resources\MemberTreeResource;
use App\Interfaces\MemberRepositoryInterface;
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
            $member = $this->crudRepository->create($data);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $data['application_number'] = '#' . str_pad($member->id, 6, '0', STR_PAD_LEFT);
            if (request('image') !== null) {
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
            $this->crudRepository->update($request->validated(), $member->id);
            if (request('image') !== null) {
                $member = Member::find($member->id);
                $image = $this->crudRepository->AddMediaCollection('image', $member);
            }
            activity()->performedOn($member)->withProperties(['attributes' => $member])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
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

        $member = Member::where('phone', $request->phone)->first();

        if (!$member) {
            return response()->json(['message' => 'Wrong credentials'], 401);
        }

        if (Hash::needsRehash($member->password)) {
            $member->password = Hash::make($request->password);
            $member->save();
        }

        if (!Hash::check($request->password, $member->password)) {
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
}
