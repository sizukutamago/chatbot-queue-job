<?php

namespace App\Services;

use App\Exceptions\InvalidMessages;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;

class LineService {

    /**
     * @var
     */
    private $messages;

    private $signature;

    /**
     * 検証後、jsonデコードされたevents
     * @var []
     */
    private $events;

    /**
     * line message channel secret
     *
     * @var string
     */
    private $channel_secret;

    /**
     * line message access token
     *
     * @var string
     */
    private $access_token;

    public function __construct(string $messages, string $signature)
    {
        $this->channel_secret = env('LINE_MESSAGE_SECRET');
        $this->access_token = env('LINE_MESSAGE_ACCESS_TOKEN');
        $this->messages = $messages;
        $this->signature = $signature;
    }

    /**
     * @return array
     * @throws InvalidMessages
     * @throws LINEBot\Exception\InvalidEventRequestException
     */
    public function getEvents()
    {
        if (!$this->verifyMessages()) {
            throw new InvalidMessages();
        }

        return $this->parseMessages();
    }

    /**
     * lineサーバから送信されたものかの検証を行う
     *
     * @param $body
     * @return bool
     */
    public function verifyMessages()
    {
        return hash_equals(base64_encode(hash_hmac('sha256', $this->messages, $this->channel_secret, true)), $this->signature);
    }

    /**
     *
     *
     * @return array
     * @throws LINEBot\Exception\InvalidEventRequestException
     */
    public function parseMessages()
    {
        return EventRequestParser::parseEventRequest($this->messages);
    }
}
