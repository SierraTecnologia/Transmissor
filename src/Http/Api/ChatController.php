<?php

namespace Transmissor\Http\Api;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $conversations = Chat::conversation(Chat::conversations()->conversation)
          ->for(auth()->user())
          ->get()
          ->toArray()['data'];
        $conversations = array_pluck($conversations, 'id');
        return view('home', compact('conversations'));
    }

    public function history($toId): void
    {
        $user = Auth::getToken();
        $conversation = Chat::conversations()->between($user, User::find($toId));
    }

    public function send($toId): void
    {
        $user = Auth::getToken();
        $message = Chat::message('Hello')
            ->from($user)
            ->to($conversation)
            ->send();
    }
}
