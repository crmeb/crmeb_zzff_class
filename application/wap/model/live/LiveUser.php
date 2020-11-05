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
 * 直播间用户表
 */
use basic\ModelBasic;
use traits\ModelTrait;

class LiveUser extends ModelBasic
{
    use ModelTrait;

    public static function setLiveUser($uid,$live_id,$lastCd=3600)
    {
        $liveUser = self::where(['uid'=>$uid,'live_id'=>$live_id])->find();
        $ip = \think\Request::instance()->ip();
        if($liveUser){
            if($liveUser->is_open_ben && $liveUser->open_ben_time > time()) return self::setErrorInfo('您已被禁止访问此直播间！');
            $liveUser->last_ip = $ip;
            $liveUser->visit_num = ($liveUser->last_time + $lastCd) < time() ? $liveUser->visit_num++ : $liveUser->visit_num;
            $liveUser->last_time = time();
            if($liveUser->is_ban && $liveUser->ban_time && $liveUser->ban_time < time()){
                $liveUser->is_ban = 0;
                $liveUser->ban_time = 0;
            }
            $liveUser->save();
            return $liveUser;
        }else{
            $data = [
                'uid'=>$uid,
                'live_id'=>$live_id,
                'add_time'=>time(),
                'last_time'=>time(),
                'visit_num'=>1,
                'last_ip'=>$ip,
                'add_ip'=>$ip,
                'is_ban'=>0,
            ];
            return self::set($data);
        }
    }

    /*
     * 设置用户上下线
     * @param int $live_id 直播间号
     * @param int $uid 用户id
     * @param int $is_online 上下线
     * @return object
     * */
    public static function setLiveUserOnline($live_id,$uid,$is_online =1 )
    {

        if(self::be(['live_id'=>$live_id,'uid'=>$uid,'is_online'=>$is_online])) return true;
        return self::where(['live_id'=>$live_id,'uid'=>$uid])->update(['is_online'=>$is_online]);
    }

}