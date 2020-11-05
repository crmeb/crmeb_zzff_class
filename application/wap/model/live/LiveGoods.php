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
namespace app\wap\model\live;

/**
 * 直播带货
 */

use app\admin\model\order\StoreOrder;
use basic\ModelBasic;
use service\SystemConfigService;
use traits\ModelTrait;
use app\wap\model\user\User;

class LiveGoods extends ModelBasic
{

    use ModelTrait;


    public static function getLiveGoodsList($where,$page = 0,$limit = 10)
    {
        $model = self::alias('g');
        $list = $model->where('g.is_delete',0);
        if ($where['is_show'] != "" && isset($where['is_show'])){
            $list = $model->where('g.is_show',$where['is_show']);
        }
        $list = $model->join('special s','g.special_id=s.id')->field('g.id as live_goods_id, g.sort as gsort, g.fake_sales as gfake_sales, g.is_show as gis_show, g.sales as gsales, s.*');
        $list = $model->order('g.sort desc')->page((int)$page,(int)$limit)->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item){
            $item['label'] = json_decode($item['label'],true);
            $item['_add_time']  = date('Y-m-d H:i:s',$item['add_time']);
            $item['pink_end_time'] = $item['pink_end_time'] ? strtotime($item['pink_end_time']) : 0;
            $item['sales'] = StoreOrder::where(['paid' => 1, 'cart_id' => $item['id'], 'refund_status' => 0])->count();
            //查看拼团状态,如果已结束关闭拼团
            if ($item['is_pink'] && $item['pink_end_time'] < time()) {
                self::update(['is_pink' => 0], ['id' => $item['id']]);
                $item['is_pink'] = 0;
            }
        }
        $page--;
        return ['list'=>$list,'page'=> $page];
    }


}