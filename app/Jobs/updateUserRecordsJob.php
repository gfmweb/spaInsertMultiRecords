<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class updateUserRecordsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $usersInDB;
    protected $updatedUsers;
    public function __construct($users,$update)
    {
        $this->usersInDB = $users;
        $this->updatedUsers = $update;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->usersInDB as $user)
        {
            foreach ($this->updatedUsers as $item)
            {
                if($user->first_name == $item->first_name && $user->last_name == $item->last_name)
                {
                    User::where('id', $user->id)->update(['email' =>$item->email,'age'=>$item->age]);
                }
            }
        }
    }
}
