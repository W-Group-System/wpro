<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmployeeReviewedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $reviewer;
    public $remarks;
    public $actionType;

    public function __construct($employee, $reviewer, $remarks, $actionType)
    {
        $this->employee = $employee;
        $this->reviewer = $reviewer;
        $this->remarks = $remarks;
        $this->actionType = $actionType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Employee Record Has Been Reviewed')
                    ->view('email.employee_reviewed');
    }
}
