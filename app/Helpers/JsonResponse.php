<?php


namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use Illuminate\Support\Facades\Response;

class JsonResponse
{
    const MSG_ADDED_SUCCESSFULLY = 'Item has been added successfully';
    const MSG_ADDED_SUCCESSFULLY_APPLICATION = 'Application has been submitted successfully';
    const MSG_UPDATED_SUCCESSFULLY = "Item has been updated successfully";
    const MSG_DELETED_SUCCESSFULLY = "Item has been deleted successfully";
    const MSG_COUPON_REMOVED_SUCCESSFULLY = "Coupon Code has been removed successfully";
    const MSG_FORCE_DELETED_SUCCESSFULLY = "Item has been deleted successfully from database";
    const MSG_NOT_ALLOWED = "responses.msg_not_allowed";
    const MSG_NOT_AUTHORIZED = "responses.msg_not_authorized";
    const MSG_NOT_AUTHENTICATED = "responses.msg_not_authenticated";
    const MSG_USER_NOT_ENABLED = "responses.msg_user_not_enabled";
    const MSG_NOT_FOUND = "Item can not be found";
    const MSG_USER_NOT_FOUND = "responses.msg_user_not_found";
    const MSG_EMAIL_NOT_VERIFIED = "your email not verified";
    const MSG_EMAIL_SENDING = "Email has Been Sent Successfully";
    const MSG_EMAIL_SMTP_ERROR = "Item was updated but Email has not been sent due to SMTP Errors";
    const MSG_WRONG_PASSWORD = "responses.msg_wrong_password";
    const MSG_SUCCESS = "Success";
    const MSG_FAILED = "Error";
    const MSG_LOGIN_SUCCESSFULLY = "responses.msg_login_successfully";
    const MSG_LOGIN_FAILED = "Invalid Credentials";
    const MSG_LOGOUT_SUCCESSFULLY = "responses.msg_logout_successfully";
    const MSG_CANNOT_DELETED = "This item can not be deleted due to relationship with other resources";
    const MSG_CANNOT_RESTORED = "This item can not be restored";
    const MSG_RESTORED_SUCCESSFULLY = "Item has been successfully restored";
    const MSG_CANNOT_DELETED_MULTI_RESOURCE = "Can not be deleted due to relationship with other resources";
    const MSG_BAD_REQUEST = "responses.msg_bad_request";
    const MSG_PROPER_HEADER = "responses.msg_add_proper_header";

    /**
     * @param $message
     * @param null $content
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respondSuccess($message, $content = null, $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'result' => trans(self::MSG_SUCCESS),
            'data' => $content,
            'message' => $message,
            'status' => $status
        ]);
    }

    /**
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respondError($message, int $status = 500): \Illuminate\Http\JsonResponse
    {
        try {
            throw new Exception($message, $status);
        } catch (Throwable $e) {
            $errorLog = collect([
                'code' => $e->getCode(),
            ])->concat((array)collect($e->getTrace())->take(1))->toArray()[0];
            Log::channel('slack')->error($e->getMessage(), $errorLog);
        }

        return response()->json([
            'data' => null,
            'result' => trans(self::MSG_FAILED),
            'message' => $message,
            'status' => $status,
        ]);
    }


    public static function downloadFile($url): BinaryFileResponse
    {
        return response()->download(public_path('storage/' . $url));
    }

    public static function uploadFile($url)
    {
    }

    public static function success(): array
    {
        return ['result' => trans(self::MSG_SUCCESS), 'message' => trans(self::MSG_SUCCESS), 'status' => 200];
    }

    public static function savedSuccessfully(): array
    {
        return ['result' => trans(self::MSG_SUCCESS), 'message' => trans(self::MSG_ADDED_SUCCESSFULLY), 'status' => 200];
    }

    public static function updatedSuccessfully(): array
    {
        return ['result' => trans(self::MSG_SUCCESS), 'message' => trans(self::MSG_UPDATED_SUCCESSFULLY), 'status' => 200];
    }

    public static function respondUniqueError()
    {
        return Response::json([
            'result' => 'Error',
            'data' => null,
            'message' => 'The slug must be unique',
            'status' => 500
        ], 422);
    }
}
