<?php

namespace app\wap\model\store;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 积分产品管理 model
 * Class IntegralProduct
 * @package app\routine\model\store
 */
class IntegralProduct extends ModelBasic
{
    use ModelTrait;

    public static function get_integral_list($page,$limit,$is_hot=false){
        $model=self::setWhere();
        if($is_hot) $model->where('is_hot',1);
        $list=$model->order('sort DESC, id DESC')->field(['image','integral','integral_name','id','sort','add_time'])->page($page,$limit)->select();
        foreach ($list as &$item){
            $item['add_time']=date('Y-M-d H:i:s',$item['add_time']);
        }
        $page++;
        return compact('list','page');
    }

    public static function setWhere($alias=''){
        $alias && $alias.='.';
        return self::where($alias.'is_del',0)->where($alias.'is_show',1)->where($alias.'mer_id',0);
    }
    public static function isValidProduct($id){
        return self::setWhere()->where('id',$id)->count();
    }
    public static function getProductStock($id){
        return self::setWhere()->where('id',$id)->value('stock');
    }
    public static function decIntegralStock($num,$id)
    {
        $res = false !== self::where('id',$id)->dec('stock',$num)->inc('sales',$num)->update();
        return $res;
    }
    public static function get_buy_list($uid,$page,$limit){
        $list=StoreOrder::where(['a.uid'=>$uid])->where('a.integral_id','<>',0)
            ->alias('a')->join('integral_product i','i.id=a.integral_id')->field(['i.image','i.integral_name','i.integral','i.id'])
            ->page($page,$limit)->select();
        $page++;
        return compact('list','page');
    }

}
