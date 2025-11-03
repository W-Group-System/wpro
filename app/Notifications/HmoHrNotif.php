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
            ->line($this->detailsHr['body'])
            ->line($this->detailsHr['thanks'])
            ->action($this->detailsHr['actionText'], $this->detailsHr['actionURL']);

        // Attach uploaded files if any
        foreach ($this->attachments as $filePath) {
            $fullPath = storage_path('app/public/' . $filePath);
            if (file_exists($fullPath)) {
                $mail->attach($fullPath);
            }
        }

        return $mail;
    }
}
