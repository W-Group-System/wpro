<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class HmoHrNotif extends Notification
{
    use Queueable;

    private $detailsHr;
    private $attachments;

    public function __construct($detailsHr, $attachments = [])
    {
        $this->detailsHr = $detailsHr;
        $this->attachments = $attachments;
    }

    public function via($notifiable)
    {
        return ['mail']; 
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            // ->cc('ict.engineer@wgroup.com.ph')
            ->subject($this->detailsHr['subject'])
            ->greeting($this->detailsHr['greeting'])
            ->line(new \Illuminate\Support\HtmlString($this->detailsHr['body']))
            ->line(new \Illuminate\Support\HtmlString($this->detailsHr['thanks']));
            // ->action($this->detailsHr['actionText'], $this->detailsHr['actionURL']);

        // Attach files if available
        foreach ($this->attachments as $file) {
            // Check if file exists in public folder
            $fullPath = public_path($file);
            if(file_exists($fullPath)){
                $mail->attach($fullPath);
            }
        }

        return $mail;
    }
}
