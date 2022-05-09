<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MensajeController extends Controller
{
   	/**
     * Create a new controller instance.
     *
     * @return void
     */
	public function __construct()
	{
		$this->middleware('auth:api', ['except' => ['login']]);
        $this->guard = "api";
	}

    public function get(ChatRoom $chatroom)
    {
        return $chatroom->messages;
    }

    public function index($receiverId)
    {
        $receiver = User::find($receiverId);
        $senderUserId = auth()->user()->id;
        $roomMembers = [$receiverId, $senderUserId];
        sort($roomMembers);
        $roomMembers = implode($roomMembers, ',');
        
        $chatRoom = ChatRoom::where('user_ids', $roomMembers)->first();
        if(is_null($chatRoom)) {
            $chatRoom = new ChatRoom;
            $chatRoom->room_type = 'private';
            $chatRoom->user_ids = $roomMembers;
            $chatRoom->save();
        }

        return $chatRoom;
    }

    public function store(ChatRoom $chatroom)
    {
        $senderId = auth()->user()->id;
        $roomMembers = collect(explode(',', $chatroom->user_ids));
        $roomMembers->forget($roomMembers->search($senderId));
        $receiverId = $roomMembers->first();

        $message = new Message;
        $message->chat_room_id = $chatroom->id;
        $message->sender_id = $senderId;
        $message->message = request('message');
        $message->save();

        $receiver = new Receiver;
        $receiver->message_id = $message->id;
        $receiver->receiver_id = $receiverId;

        if($receiver->save()) {
            $message = Message::with('sender')->find($message->id);
            broadcast(new PrivateMessageEvent($message))->toOthers();
            return $message;
        } else {
            return 'Something went wrong!!';
        }
    }
// public function chat_with(User $user)
// {
	
// 	// Primero recuperamos al usuario que realiza la solicitud
// 	$user_a = auth()->user();
 
// 	// Usuario con el que deseamos chatear
// 	$user_b = $user
 
// 	// Vamos a recuperar la sala de chat del usuario a que tambien tenga al usuario b
// 	$chat =  $user_a->chats()->whereHas('users', function ($q) use ($user_b) {
 
// 		// Aquí buscamos la relación con el usuario b
// 		$q->where('chat_user.user_id', $user_b->id);
 
// 	})->first();
 
// 	// Si la sala no existe debemos crearla
// 	if(!$chat)
// 	{
 
// 		// La sala no tiene ningún parámetro
// 		$chat = \App\Models\Chat::create([]);
 
// 		// Después adjuntamos a ambos usuarios
// 		$chat->users()->sync([$user_a->id, $user_b->id]);
 
// 	}
 
// 	// Redireccionamos al usuario a la ruta chat.show
// 	return redirect()->route('chat.show', $chat);
 
// }
}
