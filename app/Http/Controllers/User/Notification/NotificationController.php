<?php

namespace App\Http\Controllers\User\Notification;

use Carbon\Carbon;
use App\Enum\NotificationTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification as DBN;
use App\Http\Requests\User\Notification\SearchNotificationRequest;

class NotificationController extends Controller
{
    public function index(SearchNotificationRequest $request)
    {
        $types = NotificationTypeEnum::cases();

        $user      = auth()->user();
        $morphType = $user->getMorphClass();
        $morphId   = $user->getKey();

        $q = DBN::query()
            ->where(function ($group) use ($morphType, $morphId) {
                $group->where(function ($q) use ($morphType, $morphId) {
                    $q->where('notifiable_type', $morphType)
                        ->where('notifiable_id', $morphId);
                })
                    ->orWhere(function ($q) use ($morphType, $morphId) {
                        $q->where('reciverable_type', $morphType)
                            ->where('reciverable_id', $morphId);
                    });
            });

        if ($request->filled('type')) {
            $q->where('type', $request->input('type'));
        }

        $fromUtc = $request->filled('date_from')
            ? Carbon::parse($request->input('date_from'), 'Asia/Riyadh')->startOfDay()->utc()
            : null;

        $toUtc = $request->filled('date_to')
            ? Carbon::parse($request->input('date_to'), 'Asia/Riyadh')->endOfDay()->utc()
            : null;

            if ($fromUtc && $toUtc) {
                $q->whereBetween('created_at', [$fromUtc, $toUtc]);
            } elseif ($fromUtc) {
                $q->where('created_at', '>=', $fromUtc);
            } elseif ($toUtc) {
                $start = Carbon::parse($request->date_to, 'Asia/Riyadh')->startOfDay()->utc();
                $end   = Carbon::parse($request->date_to, 'Asia/Riyadh')->endOfDay()->utc();
                $q->whereBetween('created_at', [$start, $end]);
            }
            
        $notifications = $q->latest('created_at')->paginate(10)->withQueryString();

        return view('user.pages.notification.index', compact('notifications', 'types'));
    }
}
