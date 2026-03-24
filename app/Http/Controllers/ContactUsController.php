<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\ContactUsRequest;
use App\Http\Resources\ContactUsResource;
use App\Interfaces\ContactUsRepositoryInterface;
use App\Mail\ContactUsMail;
use App\Mail\ContactUsRequestMail;
use App\Models\ContactUs;
use App\Models\EmailTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(ContactUsRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $message = ContactUsResource::collection($this->crudRepository->all(
                [],
                [],
                ['*']
            ));
            return $message->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function show(ContactUs $contactu): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess('Item fetched successfully', new ContactUsResource($contactu));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(ContactUsRequest $request)
    {
        try {
            $this->crudRepository->create($request->validated());
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }




    public function update(ContactUsRequest $request, ContactUs $contactu): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $contactu->id);
            activity()->performedOn($contactu)->withProperties(['attributes' => $contactu])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('contact_us', $request['items']);
            return  JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(ContactUs::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}
