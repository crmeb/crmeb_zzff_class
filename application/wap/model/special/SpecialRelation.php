<?php
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