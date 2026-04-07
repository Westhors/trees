<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\EventAttendanceRequest;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\MemberResource;
use App\Interfaces\EventRepositoryInterface;
use App\Models\EventAttendance;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Event;

class EventController extends BaseController
{
    protected mixed $crudRepository;

    public function __construct(EventRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $event = EventResource::collection($this->crudRepository->all(
                ['attendances', 'attendances.member'],
                [],
                ['*']
            ));
            return $event->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function show(Event $event): ?\Illuminate\Http\JsonResponse
    {
        try {
            $event->load(['attendances.member','attendees',
                'attendances' => function($query) {
                    $query->orderBy('created_at', 'desc');
            }]);
            return JsonResponse::respondSuccess(
                'Event details fetched successfully',
                new EventResource($event)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(EventRequest $request)
    {
        try {
           $event = $this->crudRepository->create($request->validated());
            if (request('image') !== null) {
                $this->crudRepository->AddMediaCollection('image', $event);
            }
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function update(EventRequest $request, Event $event)
    {
        try {
           $event = $this->crudRepository->update($request->validated(), $event->id);
            if ($request->filled('image')) {
                $event = Event::find($event->id);
                $this->crudRepository->AddMediaCollection('image', $event);
            }
            return JsonResponse::respondSuccess(
                trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY),
                new EventResource($event->fresh(['attendances.member']))
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('events', $request['ids']);
            return  JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Event::class, $request['ids']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function changeAttendance(EventAttendanceRequest $request)
    {
        try {
            DB::beginTransaction();

            $member = auth()->user();
            $eventId = $request->event_id;
            $newStatus = $request->status;
            // طريقة بديلة للبحث عن المناسبة
            $event = Event::where('id', $eventId)->first();

            // البحث عن تسجيل سابق
            $attendance = EventAttendance::where('event_id', $eventId)
                ->where('member_id', $member->id)
                ->first();

            if ($attendance) {
                // تحديث الحالة
                $attendance->update([
                    'status' => $newStatus,
                    'note' => $request->note,
                ]);
                $message = 'Attendance status updated successfully';
            } else {
                // إنشاء تسجيل جديد
                $attendance = EventAttendance::create([
                    'event_id' => $eventId,
                    'member_id' => $member->id,
                    'status' => $newStatus,
                    'note' => $request->note,
                ]);
                $message = 'Attendance recorded successfully';
            }

            DB::commit();

            // إرجاع تفاصيل المناسبة مع الحالة الجديدة
            $event->load(['attendances.member']);

            return JsonResponse::respondSuccess($message, [
                'event' => new EventResource($event),
                'my_attendance' => $newStatus,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return JsonResponse::respondError($e->getMessage());
        }
    }

    /**
     * عرض المناسبات التي اشترك فيها العضو
     */
    public function myEvents()
    {
        try {
            $member = auth()->user();

            $events = Event::whereHas('attendances', function($query) use ($member) {
                $query->where('member_id', $member->id);
            })
            ->with(['attendances' => function($query) use ($member) {
                $query->where('member_id', $member->id);
            }, 'attendances.member'])
            ->orderBy('date', 'desc')
            ->get();

            return JsonResponse::respondSuccess(
                'My events fetched successfully',
                EventResource::collection($events)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    /**
     * عرض الحضور في مناسبة معينة (للأدمن)
     */
    public function getAttendees(Event $event)
    {
        try {
            $event->load(['attendances.member']);

            $attendees = [
                'attending' => $event->attendances
                    ->where('status', 'attending')
                    ->map(function($attendance) {
                        return [
                            'member' => new MemberResource($attendance->member),
                            'note' => $attendance->note,
                            'registered_at' => $attendance->created_at,
                        ];
                    })
                    ->values(),

                'not_attending' => $event->attendances
                    ->where('status', 'not_attending')
                    ->map(function($attendance) {
                        return [
                            'member' => new MemberResource($attendance->member),
                            'note' => $attendance->note,
                            'registered_at' => $attendance->created_at,
                        ];
                    })
                    ->values(),

                'pending' => $event->attendances
                    ->where('status', 'pending')
                    ->map(function($attendance) {
                        return [
                            'member' => new MemberResource($attendance->member),
                            'note' => $attendance->note,
                            'registered_at' => $attendance->created_at,
                        ];
                    })
                    ->values(),
            ];

            return JsonResponse::respondSuccess('Event attendees fetched successfully', [
                'event' => new EventResource($event),
                'attendees' => $attendees,
                'statistics' => [
                    'total' => $event->attendances->count(),
                    'attending' => $attendees['attending']->count(),
                    'not_attending' => $attendees['not_attending']->count(),
                    'pending' => $attendees['pending']->count(),
                ]
            ]);
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



}
