<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendEmailSubscriber;
use App\Models\User;
use Mail;

class SendEmailSubscribeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($website_subscribers,$website_name,$email_data)
    {
        $this->subsucribers_list = $website_subscribers;
        $this->website_name = $website_name;
        $this->email_data = $email_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->subsucribers_list as $row)
        {
            $user_info = User::find($row->user_id)->get();
              
            Mail::to($user_info->email)->send(new SendEmailSubscriber($this->email_data,$user_info->name));
        }
   
    }
}
