<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class HmoNotif extends Notification
{
    use Queueable;

    private $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['mail']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->details['subject'])
            ->greeting($this->details['greeting'])
            ->line(new \Illuminate\Support\HtmlString($this->details['body']))
            ->line(new \Illuminate\Support\HtmlString($this->details['thanks']));
            // ->action($this->details['actionText'], $this->details['actionURL']);
    }
}
