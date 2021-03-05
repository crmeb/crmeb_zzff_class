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

namespace behavior\wechat;

use app\wap\model\user\User;
use app\wap\model\user\WechatUser;
use think\Cookie;
use think\Request;

class UserBehavior
{
    /**
     * 微信授权成功后
     * @param $userInfo
     */
    public static function wechatOauthAfter($openid,$wechatInfo)
    {
        Cookie::set('is_login',1);
        $spread_uid=$wechatInfo['spread_uid'];
        $wechatInfo['nickname']=filter_emoji($wechatInfo['nickname']);
        if(isset($wechatInfo['unionid']) && $wechatInfo['unionid'] != '' && WechatUser::be(['unionid'=>$wechatInfo['unionid']])){
            WechatUser::edit($wechatInfo,$wechatInfo['unionid'],'unionid');
            $uid = WechatUser::where('unionid',$wechatInfo['unionid'])->value('uid');
            User::updateWechatUser($wechatInfo,$uid);
        }else if(WechatUser::be(['openid'=>$wechatInfo['openid']])){
            WechatUser::edit($wechatInfo,$wechatInfo['openid'],'openid');
            User::updateWechatUser($wechatInfo,WechatUser::openidToUid($wechatInfo['openid']));
        }else{
            unset($wechatInfo['spread_uid']);
            if(isset($wechatInfo['subscribe_scene'])) unset($wechatInfo['subscribe_scene']);
            if(isset($wechatInfo['qr_scene'])) unset($wechatInfo['qr_scene']);
            if(isset($wechatInfo['qr_scene_str'])) unset($wechatInfo['qr_scene_str']);
            try{
                $userInfo=User::setWechatUser($wechatInfo,$spread_uid);
                WechatUser::setNewUserInfo($userInfo);
            }catch (\Exception $e){
            }
        }
        User::where('uid',WechatUser::openidToUid($openid))->limit(1)->update(['last_time'=>time(),'last_ip'=>Request::instance()->ip()]);
    }

}
