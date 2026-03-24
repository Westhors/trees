<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\BranchRequest;
use App\Http\Resources\BranchResource;
use App\Interfaces\BranchRepositoryInterface;
use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;

class BranchController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(BranchRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $branch = BranchResource::collection($this->crudRepository->all(
                [],
                [],
                ['*']
            ));
            return $branch->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function show(Branch $branch): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess('Item fetched successfully', new BranchResource($branch));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(BranchRequest $request)
    {
        try {
            $this->crudRepository->create($request->validated());
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }




    public function update(BranchRequest $request, Branch $branch): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $branch->id);
            activity()->performedOn($branch)->withProperties(['attributes' => $branch])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('branches', $request['ids']);
            return  JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Branch::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}
