<?php

namespace App\Http\Controllers\Chats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IChat;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\IMessage;

class ChatController extends Controller
{
    protected $chats;
    protected $messages;

    public function __construct(IChat $chats,IMessage $messages)
    {
        $this->chats = $chats;
        $this->messages = $messages;
    }

    public function sendMessage(Request $request)
    {
        $this->validate($request,[
            'recipient' => ['required'],
            'body' => ['required']
        ]);

        $recipient = $request->recipient;
        $user = auth()->user();
        $body = $request->body;

        $chat = $user->getChatWithUser($recipient);
        if(!$chat){
            $chat = $this->chats->create([]);
            $this->chats->createParticipants($chat->id, [$user->id, $recipient]);
        }

        $message = $this->messages->create([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'body' => $body,
            'last_read' => null
        ]);

        return new MessageResource($message);
    }

    public function getUserChats()
    {

    }

    public function getChatMessages($id)
    {

    }

    public function markAsRead($id)
    {

    }

    public function destroyMessage($id)
    {
        
    }
}
