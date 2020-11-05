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
namespace app\wap\model\special;

use basic\ModelBasic;
use traits\ModelTrait;

class SpecialRelation extends ModelBasic
{
    use ModelTrait;

    /**
     * 收藏和取消收藏
     * @param $uid int 用户uid
     * @param $id int 专题id
     * @return bool|Object
     */
    public static function SetCollect($uid,$id){
        if(self::be(['uid'=>$uid,'link_id'=>$id,'category'=>1])){
            return self::where(['uid'=>$uid,'link_id'=>$id,'category'=>1])->delete();
        }else{
            return self::set(['uid'=>$uid,'link_id'=>$id,'category'=>1,'add_time'=>time()]);
        }
    }

}