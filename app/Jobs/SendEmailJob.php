<?php

namespace App\Jobs;

use Mail;
use App\Models\Schedule;
use App\Mail\ScheduleCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $schedule;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Todo $todo)
    {
        $this->schedule = $schedule;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to('your@email.com')->send(new ScheduleCreated($this->schedule));
    }
}
