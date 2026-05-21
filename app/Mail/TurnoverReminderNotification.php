<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TurnoverReminderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($data)
    {
        $this->request_data = $data;
    }

    public function build()
    {
        return $this->subject('HRIS - Vacation Leave Turnover List Reminder')
            ->view('email.turnover_reminder_notification')
            ->with([
                'details' => $this->request_data,
            ]);
    }
}
