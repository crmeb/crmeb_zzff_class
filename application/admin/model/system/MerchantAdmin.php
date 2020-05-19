<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/11
 */

namespace app\admin\model\system;


use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class SystemAdmin
 * @package app\admin\model\system
 */
class MerchantAdmin extends ModelBasic
{
    use ModelTrait;
    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where){
        $model = new self;
        if($where['real_name'] != ''){
            $model = $model->where('account','LIKE',"%$where[real_name]%");
            $model = $model->where('real_name','LIKE',"%$where[real_name]%");
        }
        if($where['phone'] != '') $model = $model->where('phone','LIKE',"%$where[phone]%");
        if($where['status'] != '') $model = $model->where('status',$where['status']);
//        $model = $model->where('is_del',0);
        $model = $model->order('id desc');
        return self::page($model,$where);
    }
}