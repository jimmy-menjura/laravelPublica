<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class notifications extends Notification implements ShouldBroadcast
{
    use Queueable;

    public  string $message;
    public string $nickname;
    public string $fullname;
    public string $image;
    public string $created_at;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $message, string $nickname,string $fullname,string $image,string $created_at)
    {
        $this->message = $message;
        $this->nickname = $nickname;
        $this->fullname = $fullname;
        $this->image = $image;
        $this->created_at = $created_at;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => "$this->message",
            // 'user' => $notifiable->id,
            'nickname' => $this->nickname,
            'image' => $this->image,
            'fullname' => $this->fullname,
            'created_at' => $this->created_at
        ]);
    }
}
