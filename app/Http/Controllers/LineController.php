<?php

namespace App\Http\Controllers;

use LINE\LINEBot;
use App\Jobs\SendReplyMessage;

class LineController extends Controller
{
    public function getMessage(){
        //queueにメッセージを追加
        $body = file_get_contents('php://input');

        $signature = $_SERVER['HTTP_' . LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
        SendReplyMessage::dispatch($body, $signature);

        return abort(200);
    }
}
