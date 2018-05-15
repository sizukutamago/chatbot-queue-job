<?php

namespace App\Jobs;

use App\Services\LineService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendReplyMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $messages;

    private $signature;

	/**
	 * Create a new job instance.
	 *
	 * @param $messages
	 * @param $signature
	 */
    public function __construct($messages, $signature)
    {
        $this->messages = $messages;
        $this->signature = $signature;
    }

    /**
     * Execute the job.
     *
     * @throws \App\Exceptions\InvalidMessages
     * @throws \LINE\LINEBot\Exception\InvalidEventRequestException
     */
    public function handle()
    {
        $line = new LineService($this->messages, $this->signature);
        $events = $line->getEvents();

        Log::emergency(var_export($events));
    }
}
