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
 * 直播间礼物
 */
use basic\ModelBasic;
use service\SystemConfigService;
use traits\ModelTrait;
use app\wap\model\user\User;

class LiveReward extends ModelBasic
{

    use ModelTrait;


    public static function getLiveRewardList($where,$page = 0,$limit = 10)
    {
        $model = self::where('live_id',$where['live_id'])->where('is_show',1);
        $list = $model->field('sum(total_price) as total_price,uid,gift_id,id,gift_price,gift_num')->group('uid')->order('total_price desc')->page($page,$limit)->select();
        $list = count($list) ? $list->toArray() : [];
        $gold_info = SystemConfigService::more(['gold_name','gold_image']);
        foreach ($list as &$item) {
            $userinfo = User::where('uid', $item['uid'])->field(['nickname', 'avatar'])->find();
            if ($userinfo) {
                $item['nickname'] = $userinfo['nickname'];
                $item['avatar'] = $userinfo['avatar'];
            } else {
                $item['nickname'] = '';
                $item['avatar'] = '';
            }
        }
        $page--;
        return ['list'=>$list,'page'=> $page,'gold_info' => $gold_info];
    }

    /**插入打赏数据
     * @param $data
     * @return bool|int|string
     */
    public static function insertLiveRewardData($data)
    {
        if (!$data || !is_array($data)) {
            return false;
        }
        return self::insertGetId($data);
    }

    /**获取当前用户打赏
     * @param array $where
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLiveRewardOne(array $where)
    {
        if (!$where) return false;
        $data = self::where($where)->field('sum(total_price) as total_price,uid,gift_id,id')->group('uid')->find();
        $data = $data ? $data->toArray() : [];
        if (!$data) return $data;
        $userinfo = User::where('uid', $data['uid'])->field(['nickname', 'avatar'])->find();
        if ($userinfo) {
            $data['nickname'] = $userinfo['nickname'];
            $data['avatar'] = $userinfo['avatar'];
        } else {
            $data['nickname'] = '';
            $data['avatar'] = '';
        }
        return $data;
    }



}