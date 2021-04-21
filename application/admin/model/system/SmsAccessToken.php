<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------


namespace app\admin\model\system;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 短信 model
 * Class SmsAccessToken
 * @package app\admin\model\system
 */
class SmsAccessToken extends ModelBasic
{
    use ModelTrait;

    const times=300;

    /**添加短信token
     * @param $getToken
     * @return object
     */
    public static function smsTokenAdd($getToken)
    {
        $data=[
            'access_token'=>$getToken['access_token'],
            'stop_time'=>bcsub($getToken['expires_in'],self::times,0),
        ];
        return self::set($data);
    }

    public static function delToken($access_token)
    {
        return self::where('access_token','<>','')->delete();
    }

}
