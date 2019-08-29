<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Overtrue\EasySms\EasySms;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $template;
    protected $content;
    protected $data;

    public function __construct($phone, $template, $content, array $data)
    {
        $this->phone = $phone;
        $this->template = $template;
        $this->content = $content;
        $this->data = $data;
    }

    public function handle(EasySms $easySms)
    {
        try
        {
            $array = [
                'template' => $this->template,
                'content' => $this->content,
                'data' => $this->data,
            ];

            $result = $easySms->send($this->phone, $array);
        } catch (\Exception $exception)
        {
            Log::error($exception);
            Log::error($exception->getException('aliyun'));
        }
    }
}
