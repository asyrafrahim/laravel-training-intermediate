<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Schedule;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchedulePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function show(User $user, Schedule $schedule)
    {
        return $user->id == $schedule->user_id;
    }

    public function edit(User $user, Schedule $schedule)
    {
        return $user->id == $schedule->user_id;
    }

    public function delete(User $user, Schedule $schedule)
    {
        return $user->id == $schedule->user_id;
    }
}
