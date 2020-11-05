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


namespace app\admin\model\system;


use traits\ModelTrait;
use basic\ModelBasic;
use think\Db;
/**
 * Class SystemAdmin
 * @package app\admin\model\system
 */
class Merchant extends ModelBasic
{
    use ModelTrait;
    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where){
        $model = new self;
        if($where['mer_name'] != ''){
            $model = $model->where('mer_name|real_name','LIKE',"%$where[mer_name]%");
        }
        if($where['mer_phone'] != '') $model = $model->where('mer_phone','LIKE',"%$where[mer_phone]%");
        if($where['status'] != '') $model = $model->where('status',$where['status']);
        $model = $model->where('is_del',0)->order('id DESC');
        return self::page($model,function ($item){
        },$where);
    }
    public static function sysPage($where){
        $model=self::where('is_del',0)->order('reg_time desc')->field(['mer_name','real_name','mer_email','now_money','id']);
        $model=self::getModelTime($where,$model,'reg_time');
        return self::page($model,function ($item){
            $item['order_count']=Db::name('store_order')->where(['mer_id'=>$item['id']])->count();
            $item['extract_price']=Db::name('user_extract')->where(['mer_id'=>$item['id'],'status'=>1])->sum('extract_price');
        },$where);
    }
}