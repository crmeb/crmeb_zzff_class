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

namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;
/**
 * 会员礼品设置 model
 * Class User
 * @package app\admin\model\user
 */

class SystemVipGift extends ModelBasic
{
    use ModelTrait;

    public static function getAll($where){
        $models=self::where(['is_del'=>0])->order('sort desc,add_time dec')->where('vip_id',$where['vip_id']);
        if($where['gift_name']) $models->where('gift_name','like',"%$where[gift_name]%");
        return self::page($models,$where);
    }

}