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


namespace app\admin\model\ump;


use basic\ModelBasic;
use traits\ModelTrait;

class StoreCouponIssue extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    public static function stsypage($where){
        $model = self::alias('A')->field('A.*,B.title')->join('__STORE_COUPON__ B','A.cid = B.id')->where('A.is_del',0)->order('A.add_time DESC');
        if(isset($where['status']) && $where['status']!=''){
            $model=$model->where('A.status',$where['status']);
        }
        if(isset($where['coupon_title']) && $where['coupon_title']!=''){
            $model=$model->where('B.title','LIKE',"%$where[coupon_title]%");
        }
        return self::page($model);
    }

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function setIssue($cid,$total_count = 0,$start_time = 0,$end_time = 0,$remain_count = 0,$status = 0)
    {
        return self::set(compact('cid','start_time','end_time','total_count','remain_count','status'));
    }
}