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