<?php
/**
 * Created by PhpStorm.
 * User: tanakas
 * Date: 2018/04/09
 * Time: 13:37
 */

namespace App\Exceptions;


class InvalidMessages extends \Exception
{
    private $message = '無効なメッセージです';

    // 例外を再定義し、メッセージをオプションではなくする
    public function __construct() {
        parent::__construct($this->message, $code = 0, $previous = null);
    }

    // オブジェクトの文字列表現を独自に定義する
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
