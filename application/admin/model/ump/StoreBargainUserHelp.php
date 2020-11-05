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

namespace app\admin\model\ump;

use basic\ModelBasic;
use traits\ModelTrait;

/**
 * 砍价帮砍Model
 * Class StoreBargainUserHelp
 * @package app\admin\model\ump
 */
class StoreBargainUserHelp extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取砍价昌平帮忙砍价人数
     * @param int $bargainId
     * @return int|string
     */
    public static function getCountPeopleHelp($bargainId = 0){
        if(!$bargainId) return 0;
        return self::where('bargain_id',$bargainId)->count();
    }

}

