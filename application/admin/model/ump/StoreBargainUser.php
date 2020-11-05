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
 * 参与砍价Model
 * Class StoreBargainUser
 * @package app\admin\model\ump
 */
class StoreBargainUser extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取参与人数
     * @param int $bargainId $bargainId 砍价产品ID
     * @param int $status   $status 状态
     * @return int|string
     */
    public static function getCountPeopleAll($bargainId = 0,$status = 0){
        if(!$bargainId) return 0;
        $model = new self();
        $model = $model->where('bargain_id',$bargainId);
        if($status) $model = $model->where('status',$status);
        return $model->count();
    }

}