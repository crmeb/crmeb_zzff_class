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

namespace app\admin\model\live;

/**
 * 直播间礼物
 */
use basic\ModelBasic;
use traits\ModelTrait;
use app\admin\model\system\SystemGroupData;

class LiveGift extends ModelBasic
{
    use ModelTrait;

    const LIVE_GIFT_NUM='live_gift_num';
    /**礼物列表
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function liveGiftList()
    {
        $data=self::order('sort DESC,id DESC')->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
            $item[self::LIVE_GIFT_NUM] = implode(',',json_decode($item[self::LIVE_GIFT_NUM]));
        }
        $count = self::count();
        return compact('data','count');
    }

    /**
     * 单个礼物信息
     */
    public static function liveGiftOne($id)
    {
        $gift=self::where('id',$id)->find();
        if($gift) return $gift;
        else return [];
    }
}
