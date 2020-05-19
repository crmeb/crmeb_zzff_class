<?php
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