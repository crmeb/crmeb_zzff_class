<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
//
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