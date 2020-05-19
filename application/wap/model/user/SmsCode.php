<?php

namespace app\wap\model\user;


use basic\ModelBasic;
use traits\ModelTrait;

class SmsCode extends ModelBasic
{
    use ModelTrait;

    public static function CheckCode($tel, $code)
    {
        $res = self::where('tel', $tel)->where('last_time', 'GT', time())->where('is_use', 0)
            ->where('code', $code)->count();
        self::where('last_time', 'LT', time())->delete();
        return $res;
    }

    public static function setCodeInvalid($phone, $code)
    {
        self::where('tel', $phone)->where('last_time', 'GT', time())->where('is_use', 0)
            ->where('code', $code)->update(['is_use' => 1]);
    }
}