<?php

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