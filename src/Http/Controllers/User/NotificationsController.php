<?php

namespace Transmissor\Http\Controllers\User;

use Transmissor\Http\Controllers\User\Controller;
use Auth;

class NotificationsController extends Controller
{
    public function unread()
    {
        if (Auth::user()->notification_count > 0 && Auth::user()->message_count == 0) {
            return redirect()->route('profile.transmissor.notifications.index');
        }
        return redirect()->route('profile.transmissor.messages.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $notifications = Auth::user()->notifications();
        Auth::user()->notification_count = 0;
        Auth::user()->save();

        return view('siravel::components.modules.notifications.index', compact('notifications'));
    }

    public function count()
    {
        return Auth::user()->notification_count + Auth::user()->message_count;
    }
}
