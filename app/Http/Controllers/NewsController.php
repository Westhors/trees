<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\NewsRequest;
use App\Http\Resources\NewsResource;
use App\Interfaces\NewsRepositoryInterface;
use App\Models\News;
use Exception;
use Illuminate\Http\Request;

class NewsController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(NewsRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $news = NewsResource::collection($this->crudRepository->all(
                [],
                [],
                ['*']
            ));
            return $news->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function show(News $news): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess('Item fetched successfully', new NewsResource($news));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(NewsRequest $request)
    {
        try {
            $news = $this->crudRepository->create($request->validated());
            if (request('gallery') !== null) {
                $this->crudRepository->AddMediaCollectionArray('gallery', $news, 'gallery');
            }
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }




    public function update(NewsRequest $request, News $news): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $news->id);
            if ($request->filled('gallery')) {
                $news = News::find($news->id);
                $this->crudRepository->AddMediaCollectionArray('gallery', $news, 'gallery');
            }
            activity()->performedOn($news)->withProperties(['attributes' => $news])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('news', $request['ids']);
            return  JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(News::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}
