<?php

namespace App\Services;

use LINE\LINEBot\Event\UnknownEvent;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\UnknownMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;


class EventRequestParser
{
    private static $eventType2class = [
        'message' => '\LINE\LINEBot\Event\MessageEvent',
        'follow' => '\LINE\LINEBot\Event\FollowEvent',
        'unfollow' => '\LINE\LINEBot\Event\UnfollowEvent',
        'join' => '\LINE\LINEBot\Event\JoinEvent',
        'leave' => '\LINE\LINEBot\Event\LeaveEvent',
        'postback' => '\LINE\LINEBot\Event\PostbackEvent',
        'beacon' => '\LINE\LINEBot\Event\BeaconDetectionEvent',
    ];

    private static $messageType2class = [
        'text' => '\LINE\LINEBot\Event\MessageEvent\TextMessage',
        'image' => '\LINE\LINEBot\Event\MessageEvent\ImageMessage',
        'video' => '\LINE\LINEBot\Event\MessageEvent\VideoMessage',
        'audio' => '\LINE\LINEBot\Event\MessageEvent\AudioMessage',
        'file' => '\LINE\LINEBot\Event\MessageEvent\FileMessage',
        'location' => '\LINE\LINEBot\Event\MessageEvent\LocationMessage',
        'sticker' => '\LINE\LINEBot\Event\MessageEvent\StickerMessage',
    ];

    /**
     * @param $body
     * @return array
     * @throws InvalidEventRequestException
     */
    public static function parseEventRequest($body)
    {
        $events = [];

        $parsedReq = json_decode($body, true);
        if (!array_key_exists('events', $parsedReq)) {
            throw new InvalidEventRequestException();
        }

        foreach ($parsedReq['events'] as $eventData) {
            $eventType = $eventData['type'];

            if (!array_key_exists($eventType, self::$eventType2class)) {
                # Unknown event has come
                $events[] = new UnknownEvent($eventData);
                continue;
            }

            $eventClass = self::$eventType2class[$eventType];

            if ($eventType === 'message') {
                $events[] = self::parseMessageEvent($eventData);
                continue;
            }

            $events[] = new $eventClass($eventData);
        }

        return $events;
    }

    /**
     * @param array $eventData
     * @return MessageEvent
     */
    private static function parseMessageEvent($eventData)
    {
        $messageType = $eventData['message']['type'];
        if (!array_key_exists($messageType, self::$messageType2class)) {
            return new UnknownMessage($eventData);
        }

        $messageClass = self::$messageType2class[$messageType];
        return new $messageClass($eventData);
    }
}