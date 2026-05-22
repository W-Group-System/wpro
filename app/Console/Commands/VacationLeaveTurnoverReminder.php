<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\EmployeeLeave;
use App\Mail\TurnoverReminderNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class VacationLeaveTurnoverReminder extends Command
{
    protected $signature = 'command:vacation_leave_turnover_reminder';

    protected $description = 'Notify approvers of pending Vacation Leave applications with no turnover list uploaded (3, 2, 1 day before leave start)';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $count = $this->sendReminders();
        return $this->info("Sent {$count} turnover reminder email(s).");
    }

    private function sendReminders()
    {
        $today = Carbon::today();
        $count = 0;

        foreach ([3, 2, 1] as $daysAhead) {
            $targetDate = $today->copy()->addDays($daysAhead)->toDateString();
            $notifColumn = "turnover_notif_{$daysAhead}";

            $leaves = EmployeeLeave::with('user', 'leave', 'approver.approver_info')
                ->where('leave_type', 1) // Vacation Leave only
                ->where('status', 'Pending')
                ->whereNull('turnover_list')
                ->whereDate('date_from', $targetDate)
                ->whereNull($notifColumn)
                ->get();

            foreach ($leaves as $leave) {
                $notified = false;

                foreach ($leave->approver as $approver) {
                    $isCurrentLevel = (
                        ($leave->level == 0 && $approver->level == 1) ||
                        ($leave->level == 1 && $approver->level == 2)
                    );

                    if ($isCurrentLevel && $approver->approver_info && $approver->approver_info->email) {
                        $details = [
                            'approver_info' => $approver->approver_info,
                            'user_info'     => $leave->user,
                            'details'       => $leave,
                            'days_before'   => $daysAhead,
                        ];

                        Mail::to($approver->approver_info->email)
                            ->send(new TurnoverReminderNotification($details));

                        $count++;
                        $notified = true;
                    }
                }

                if ($notified) {
                    EmployeeLeave::where('id', $leave->id)->update([$notifColumn => 1]);
                }
            }
        }

        return $count;
    }
}
