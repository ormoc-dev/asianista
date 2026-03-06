<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LessonStatusNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $title;

    public function __construct($status, $title)
    {
        $this->status = $status;
        $this->title = $title;
    }

    public function via($notifiable)
    {
        return ['database']; // stored in DB
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Your lesson '{$this->title}' has been {$this->status} by admin."
        ];
    }
}
