<?php
namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;
/**
 * 会员设置 model
 * Class User
 * @package app\admin\model\user
 */

class MemberShip extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where){
        $model=self::where('is_del',0);
        if($where['is_publish']!='') $model->where('is_publish',$where['is_publish']);
        if($where['title']!='') $model->where('title','like',"%$where[title]%");
        return $model;
    }
    public static function getSytemVipList($where){
        $model=self::setWhere($where)->order('sort DESC,add_time DESC');
        $data=($list=$model->page((int)$where['page'],(int)$where['limit'])
            ->select()) && count($list) ?  $list->toArray() : [];
        $count=self::setWhere($where)->count();
        return compact('data','count');
    }
    public static function getSytemVipSelect(){
        return self::where('mer_id',0)->where('is_del',0)->where('is_show',1)->column('title','id');
    }
}