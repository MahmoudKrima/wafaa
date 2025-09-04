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
        $notifications = $q->latest('created_at')->paginate(10)->withQueryString();

        return view('user.pages.notification.index', compact('notifications', 'types'));
    }
}
