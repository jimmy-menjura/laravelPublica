<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Notifications
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    
     /**
     * @var array
     */
    public $response;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->response = [
            'image'   => $data['image'],
            'of'   => $data['of'],
            'to'        => $data['to'],
            'idOriginador' =>  auth()->user()->id      
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        dd($this->response['to']);
        return new PrivateChannel("NotificaEvent." . $this->response['to']);
    }
}
