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

class StorePink extends ModelBasic{

    use ModelTrait;
    /**
     * 获取当前产品参与的人数
     * @param int $combinationId
     * @return int|string
     */
    public static function getCountPeopleAll($combinationId = 0){
        if(!$combinationId) return self::count();
        return self::where('cid',$combinationId)->count();
    }

    /**
     * 获取当前产品参与的团数
     * @param int $combinationId
     * @return int|string
     */
    public static function getCountPeoplePink($combinationId = 0){
        if(!$combinationId) return self::where('k_id',0)->count();
        return self::where('cid',$combinationId)->where('k_id',0)->count();
    }
}