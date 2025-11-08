<?php

namespace App\Jobs;



use App\Models\QueueFails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class TestJob implements ShouldQueue
{
    use Dispatchable,Queueable;



    /**
     * Execute the job.
     */
    public function handle(): void
    {
        QueueFails::query()->create(['queue'=>'test','data'=>['driver_id'=>0]]);
    }

}
