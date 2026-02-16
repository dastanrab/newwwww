<?php

namespace App\Jobs;

use App\Models\Firebase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTopicFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $topics;
    protected $token;
    protected $platform;
    public function __construct($topics,$token,$platform = '')
    {
        $this->topics = $topics;
        $this->token = $token;
        $this->platform = $platform;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $firebase = new Firebase($this->platform);
        $firebase->subscribeToTopics($this->topics,$this->token);
    }
}
