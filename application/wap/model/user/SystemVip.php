<?php

namespace app\wap\model\user;

use service\SystemConfigService;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 会员礼品设置 model
 * Class User
 * @package app\admin\model\user
 */
class SystemVip extends ModelBasic
{
    use ModelTrait;

    public static function getVipInfo($vip_id)
    {
        $vipinfo = self::where(['is_del' => 0, 'is_show' => 1])->where('id', $vip_id)->find();
        if (!$vipinfo) return [];
        $vipinfo['content'] = explode("\n", $vipinfo['content']);
        return $vipinfo;
    }

    public static function getdiscount($vip_id)
    {
        return self::where(['is_del' => 0, 'is_show' => 1])->where('id', $vip_id)->value('discount');
    }

}