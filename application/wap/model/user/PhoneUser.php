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
namespace app\wap\model\user;


use basic\ModelBasic;
use service\QrcodeService;
use think\Cookie;
use think\Session;
use traits\ModelTrait;

class PhoneUser extends ModelBasic
{
    use ModelTrait;

    public static function UidToPhone($uid)
    {
        return self::where('uid', $uid)->value('phone');
    }

    public static function UserLogIn($phone, $request)
    {
        $isfollow = false;
        self::startTrans();
        try {
            $name = '__login_phone_number';
            if (self::be(['phone' => $phone])) {
                $user = self::where('phone', $phone)->find();
                if (!$user->status) return self::setErrorInfo('账户已被禁止登录');
                $user->last_ip = $request->ip();
                $user->last_time = time();
                if ($user->uid) {
                    Session::set('loginUid', $user->uid, 'wap');
                }
                $userinfo = User::where('uid', $user->uid)->find();
                $user->save();
            } else {
                $userinfo = User::where(['phone' => $phone, 'is_h5user' => 0])->find();
                if (!$userinfo) $userinfo = User::set([
                    'nickname' => $phone,
                    'pwd' => md5(123456),
                    'avatar' => '/system/images/user_log.jpg',
                    'account' => $phone,
                    'phone' => $phone,
                    'is_h5user' => 1,
                ]);
                if (!$userinfo) return self::setErrorInfo('用户信息写入失败', true);
                Session::set('loginUid', $userinfo['uid'], 'wap');
                $user = self::set([
                    'phone' => $phone,
                    'avatar' => '/system/images/user_log.jpg',
                    'nickname' => $phone,
                    'uid' => $userinfo['uid'],
                    'add_ip' => $request->ip(),
                    'add_time' => time(),
                    'pwd' => md5(123456),
                ]);
                if (!$user) return self::setErrorInfo('手机用户信息写入失败', true);
            }
            $isfollow = $userinfo['is_h5user'] ? false : true;
            Cookie::set('__login_phone', 1);
            Cookie::set('is_login', 1);
            Session::set($name, $phone, 'wap');
            Session::set('__login_phone_num' . $userinfo['uid'], $phone, 'wap');
            try {
                $res = QrcodeService::getTemporaryQrcode('binding', $userinfo['uid']);
            } catch (\Exception $e) {
                $res['url'] = '';
                $res['id'] = '';
            }
            self::commit();
            return ['userinfo' => $user, 'url' => $res['url'], 'qcode_id' => $res['id'], 'isfollow' => $isfollow];
        } catch (\Exception $e) {
            return self::setErrorInfo($e->getMessage());
        }
    }
}