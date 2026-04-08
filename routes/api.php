<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});





//////////////////////////////////////// user ////////////////////////////////
Route::middleware(['admin'])->group(function () {
    Route::post('/user/index', [UserController::class, 'index']);
    Route::post('user/restore', [UserController::class, 'restore']);
    Route::delete('user/delete', [UserController::class, 'destroy']);
    Route::put('/user/{id}/{column}', [UserController::class, 'toggle']);
    Route::delete('user/force-delete', [UserController::class, 'forceDelete']);
    Route::apiResource('user', UserController::class);
});

//////////////////////////////////////// user ////////////////////////////////



////////////////////////////////////////// Admin ////////////////////////////////
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/admin/index', [AdminController::class, 'index']);
    Route::post('admin/restore', [AdminController::class, 'restore']);
    Route::delete('admin/delete', [AdminController::class, 'destroy']);
    Route::delete('admin/force-delete', [AdminController::class, 'forceDelete']);
    Route::put('/admin/{id}/{column}', [AdminController::class, 'toggle']);
    Route::post('/admin-select', [AdminController::class, 'index']);
    Route::post('/admin-logout', [AdminController::class, 'logout']);
    Route::get('/get-admin', [AdminController::class, 'getCurrentAdmin']);
    Route::apiResource('admin', AdminController::class);
});
Route::post('/admin/login', [AdminController::class, 'login']);
////////////////////////////////////////// Admin ////////////////////////////////




////////////////////////////////////////// media ////////////////////////////////
Route::group(['middleware' => ['api']], static function () {
    Route::get('/media', [MediaController::class, 'index']);
    Route::get('/media/{media}', [MediaController::class, 'show']);
    Route::post('/media', [MediaController::class, 'store']);
    Route::delete('/media/{media}', [MediaController::class, 'destroy']);
    Route::get('/get-unused-media', [MediaController::class, 'getUnUsedImages']);
    Route::delete('/delete-unused-media', [MediaController::class, 'deleteUnUsedImages']);
});
Route::get('/get-media/{media}', [MediaController::class, 'show']);
Route::post('/media-array', [MediaController::class, 'showMedia']);
Route::post('/media-upload-many', [MediaController::class, 'storeMany']);
//////////////////////////////////////// media ////////////////////////////////





//////////////////////////////////////// ContactUs ////////////////////////////////
Route::middleware([])->group(function () {
    Route::post('/contactus/index', [ContactUsController::class, 'index']);
    Route::post('contactus/restore', [ContactUsController::class, 'restore']);
    Route::delete('contactus/delete', [ContactUsController::class, 'destroy']);
    Route::delete('contactus/force-delete', [ContactUsController::class, 'forceDelete']);
    Route::put('/contactus/{id}/{column}', [ContactUsController::class, 'toggle']);
    Route::apiResource('contactus', ContactUsController::class);
});
Route::post('contact-us-public', [ContactUsController::class, 'store'])->middleware('throttle:3,1');
Route::post('publicsss', [ContactUsController::class, 'aaaa']);
//////////////////////////////////////// ContactUs ////////////////////////////////






//////////////////////////////////////// Branch ////////////////////////////////
Route::middleware([])->group(function () {
    Route::post('/branch/index', [BranchController::class, 'index']);
    Route::post('branch/restore', [BranchController::class, 'restore']);
    Route::delete('branch/delete', [BranchController::class, 'destroy']);
    Route::delete('branch/force-delete', [BranchController::class, 'forceDelete']);
    Route::put('/branch/{id}/{column}', [BranchController::class, 'toggle']);
    Route::apiResource('branch', BranchController::class);
});
Route::post('branch-public', [BranchController::class, 'store']);
//////////////////////////////////////// Branch ////////////////////////////////






//////////////////////////////////////// member ////////////////////////////////
Route::post('application-form', [MemberController::class, 'applicationForm']);
Route::post('login', [MemberController::class, 'login']);
Route::post('logout', [MemberController::class, 'logout'])->middleware('auth:sanctum');
Route::get('check-auth', [MemberController::class, 'checkAuth'])->middleware('auth:sanctum');
Route::get('user/delete-account', [MemberController::class, 'deleteAccount'])->middleware('auth:sanctum');


Route::get('members/tree', [MemberController::class,'tree']);
Route::middleware([])->group(function () {
    Route::post('/member/index', [MemberController::class, 'index']);
    Route::post('member/restore', [MemberController::class, 'restore']);
    Route::delete('member/delete', [MemberController::class, 'destroy']);
    Route::delete('member/force-delete', [MemberController::class, 'forceDelete']);
    Route::put('/member/{id}/{column}', [MemberController::class, 'toggle']);
    Route::apiResource('member', MemberController::class);
});
Route::post('member-public', [MemberController::class, 'store']);
Route::post('add-member-public', [MemberController::class, 'addMember']);
//////////////////////////////////////// member ////////////////////////////////





//////////////////////////////////////// event ////////////////////////////////

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/event/index', [EventController::class, 'index']);
    Route::post('event/restore', [EventController::class, 'restore']);
    Route::delete('event/delete', [EventController::class, 'destroy']);
    Route::delete('event/force-delete', [EventController::class, 'forceDelete']);
    Route::put('/event/{id}/{column}', [EventController::class, 'toggle']);
    Route::apiResource('event', EventController::class);

    Route::get('my-events', [EventController::class, 'myEvents']);

    Route::post('event-attendance', [EventController::class, 'changeAttendance']);

    Route::get('events/{event}/attendees', [EventController::class, 'getAttendees']);
});

//////////////////////////////////////// Event ////////////////////////////////







//////////////////////////////////////// news ////////////////////////////////
Route::middleware([])->group(function () {
    Route::post('/news/index', [NewsController::class, 'index']);
    Route::post('news/restore', [NewsController::class, 'restore']);
    Route::delete('news/delete', [NewsController::class, 'destroy']);
    Route::delete('news/force-delete', [NewsController::class, 'forceDelete']);
    Route::put('/news/{id}/{column}', [NewsController::class, 'toggle']);
    Route::apiResource('news', NewsController::class);
});
Route::post('news-public', [NewsController::class, 'store']);
//////////////////////////////////////// news ////////////////////////////////


