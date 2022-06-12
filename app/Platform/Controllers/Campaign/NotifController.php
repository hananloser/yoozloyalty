<?php

namespace Platform\Controllers\Campaign;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use Carbon\Carbon;

class NotifController extends Controller
{
   
    /**
     * Get admin notif unred.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getNotifUnread()
    {
        $notifs = auth('customer')->user()->unreadNotifications;
        $data = [];

        foreach ($notifs as $notif) {
            array_push($data, [
                'id' => $notif->id,
                'created_at' => Carbon::parse($notif->created_at, config('app.timezone'))->setTimezone(auth('customer')->user()->getTimezone())->diffForHumans(),
                'data' => $notif->data
            ]);
        }

        return response()->json($data, 200);
    }

    /**
     * Post mask as read admin notif.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postMasAsRead(Request $request)
    {
        if ($request->id == 'all') {
            auth('customer')->user()->unreadNotifications()->update(['read_at' => now()]);
        }else{
            auth('customer')->user()->unreadNotifications->where('id', $request->id)->markAsRead();
        }

        return response()->json(['status' => 'success'], 200);
    }
}
