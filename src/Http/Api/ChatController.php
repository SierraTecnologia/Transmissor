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
     * @return \Illuminate\Http\Response
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

    public function history($toId)
    {
        $user = Auth::getToken();
        $conversation = Chat::conversations()->between($user, User::find($toId));
    }

    public function send($toId)
    {
        $user = Auth::getToken();
        $message = Chat::message('Hello')
            ->from($user)
            ->to($conversation)
            ->send();
    }
}
