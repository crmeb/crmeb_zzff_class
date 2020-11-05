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