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

namespace app\admin\model\order;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 订单操作纪律model
 * Class StoreOrderStatus
 * @package app\admin\model\store
 */
class StoreOrderStatus extends ModelBasic
{
    use ModelTrait;

    /**
     * @param $oid
     * @param $type
     * @param $message
     */
   public static function setStatus($oid,$type,$message,$order_type=0){
       $data['oid'] = (int)$oid;
       $data['type'] = $order_type;
       $data['change_type'] = $type;
       $data['change_message'] = $message;
       $data['change_time'] = time();
       self::set($data);
   }

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($oid){
        $model = new self;
        $model = $model->where('oid',$oid);
        $model = $model->order('change_time asc');
        return self::page($model);
    }
    /**
     * @param $where
     * @return array
     */
    public static function systemPageMer($oid){
        $model = new self;
        $model = $model->where('oid',$oid);
        $model = $model->order('change_time asc');
        return self::page($model);
    }
}