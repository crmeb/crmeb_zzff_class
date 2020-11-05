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


use app\admin\model\wechat\WechatQrcode;
use app\wap\model\store\StoreOrder;
use basic\ModelBasic;
use service\SystemConfigService;
use think\Cookie;
use think\Request;
use think\response\Redirect;
use think\Session;
use think\Url;
use traits\ModelTrait;

class User extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time', 'add_ip', 'last_time', 'last_ip'];

    protected function setAddTimeAttr($value)
    {
        return time();
    }

    protected function setAddIpAttr($value)
    {
        return Request::instance()->ip();
    }

    protected function setLastTimeAttr($value)
    {
        return time();
    }

    protected function setLastIpAttr($value)
    {
        return Request::instance()->ip();
    }

    public static function ResetSpread($openid)
    {
        $uid = WechatUser::openidToUid($openid);
        if (self::be(['uid' => $uid, 'is_promoter' => 0, 'is_senior' => 0])) self::where('uid', $uid)->update(['spread_uid' => 0]);
    }

    /**
     * 绑定用户手机号码修改手机号码用户购买的专题和其他数据
     * @param $bindingPhone 绑定手机号码
     * @param $uid 当前用户id
     * @param $newUid 切换用户id
     * @param bool $isDel 是否删除
     * @param int $qcodeId 扫码id
     * @return bool
     * @throws \think\exception\PDOException
     */
    public static function setUserRelationInfos($bindingPhone, $uid, $newUid, $isDel = true, $qcodeId = 0)
    {
        self::startTrans();
        try {
            //修改下级推广人关系
            self::where('spread_uid', $uid)->update(['spread_uid' => $newUid]);
            //修改用户金额变动记录表
            self::getDb('user_bill')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改提现记录用户
            self::getDb('user_extract')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改专题购买记录表
            self::getDb('special_buy')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改购物车记录表
            self::getDb('store_cart')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改用户订单记录
            self::getDb('store_order')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改拼团用户记录
            self::getDb('store_pink')->where('uid', $uid)->update(['uid' => $newUid]);
            //修改手机用户表记录
            self::getDb('phone_user')->where('uid', $uid)->update(['uid' => $newUid]);
            //删除用户表H5用户记录
            $user = self::where('uid', $uid)->find();
            if ($isDel) self::where('uid', $uid)->delete();
            //修改上级推广关系和绑定手机号码
            self::where('uid', $newUid)->update(['phone' => $bindingPhone, 'spread_uid' => $user['spread_uid'], 'valid_time' => $user['valid_time']]);
            if ($qcodeId) WechatQrcode::where('id', $qcodeId)->update(['scan_id' => $newUid]);
            self::commit();
            \think\Session::clear('wap');
            \think\Session::set('loginUid', $newUid, 'wap');
            return true;
        } catch (\Exception $e) {
            self::rollback();
            return self::setErrorInfo($e->getMessage());
        }
    }

    /**
     * 保存微信用户信息
     * @param $wechatUser 用户信息
     * @param int $spread_uid 上级用户uid
     * @return mixed
     */
    public static function setWechatUser($wechatUser, $spread_uid = 0)
    {
        $where = ['nickname' => $wechatUser['nickname'], 'avatar' => $wechatUser['headimgurl'], 'user_type' => 'wechat'];
        if (self::be($where)) {
            $wechatUser['uid'] = (int)self::where($where)->value('uid');
            return $wechatUser;
        }
        if (isset($wechatUser['uid']) && $wechatUser['uid'] == $spread_uid) $spread_uid = 0;
        $data = [
            'account' => 'wx' . date('YmdHis'),
            'pwd' => md5(123456),
            'nickname' => $wechatUser['nickname'] ?: '',
            'avatar' => $wechatUser['headimgurl'] ?: '',
            'user_type' => 'wechat'
        ];
        //处理推广关系
        if ($spread_uid) $data = self::manageSpread($spread_uid, $data);
        $res = self::set($data);
        if ($res) $wechatUser['uid'] = (int)$res['uid'];
        return $wechatUser;
    }

    /**
     * 设置上下级推广人关系
     * 普通推广人星级关系由字段 is_promoter 区分， is_promoter = 1 为 0 星， is_promoter = 2 为 1 星，依次类推
     * @param $spread_uid 上级推广人
     * @param array $data 更新字段
     * @param bool $isForever
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function manageSpread($spread_uid, $data = [], $isForever = false)
    {
        $data['spread_uid'] = $spread_uid;
        $data['spread_time'] = time();
        return $data;
    }

    /**
     * 更新用户数据并绑定上下级关系
     * @param $wechatUser
     * @param $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function updateWechatUser($wechatUser, $uid)
    {
        $name = '__login_phone_num' . $uid;
        $userinfo = self::where('uid', $uid)->find();
        //检查是否有此字段
        $spread_uid = isset($wechatUser['spread_uid']) ? $wechatUser['spread_uid'] : 0;
        //自己不能成为自己的下线
        $spread_uid = $spread_uid == $userinfo->uid ? 0 : $spread_uid;
        //手机号码存在直接登陆
        if ($userinfo['phone']) {
            Cookie::set('__login_phone', 1);
            Session::set($name, $userinfo['phone'], 'wap');
            Session::set('__login_phone_number', $userinfo['phone'], 'wap');
        }
        //有推广人直接更新
        $editData = [
            'nickname' => $wechatUser['nickname'] ?: '',
            'avatar' => $wechatUser['headimgurl'] ?: '',
        ];
        //不是推广人，并且有上级id绑定关系
        if (!$userinfo->is_promoter && $spread_uid && !$userinfo->spread_uid && $spread_uid != $uid) $editData = self::manageSpread($spread_uid, $editData);
        return self::edit($editData, $uid, 'uid');
    }

    public static function setSpreadUid($uid, $spreadUid)
    {
        return self::where('uid', $uid)->update(['spread_uid' => $spreadUid]);
    }


    public static function getUserInfo($uid)
    {
        $userInfo = self::where('uid', $uid)->find();
        if (!Session::has('__login_phone_num' . $uid) && $userInfo['phone']) {
            Cookie::set('__login_phone', 1);
            Session::set('__login_phone_num' . $uid, $userInfo['phone'], 'wap');
        }
        if (!$userInfo) {
            Session::delete(['loginUid', 'loginOpenid']);
            throw new \Exception('未查询到此用户');
        }
        return $userInfo->toArray();
    }

    /**
     * 获得当前登陆用户UID
     * @return int $uid
     */
    public static function getActiveUid()
    {
        $uid = null;
        if (Session::has('loginUid', 'wap')) $uid = Session::get('loginUid', 'wap');
        if (!$uid && Session::has('loginOpenid', 'wap') && ($openid = Session::get('loginOpenid', 'wap')))
            $uid = WechatUser::openidToUid($openid);
        if (!$uid) exit(exception('请登陆!'));
        return $uid;
    }

    public static function backOrderBrokerage($orderInfo)
    {
        $userInfo = User::getUserInfo($orderInfo['uid']);
        if (!$userInfo || !$userInfo['spread_uid']) return true;
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ?: 1;//获取后台分销类型
        if ($storeBrokerageStatu == 1) {
            if (!User::be(['uid' => $userInfo['spread_uid'], 'is_promoter' => 1])) return true;
        }
        $brokerageRatio = (SystemConfigService::get('store_brokerage_ratio') ?: 0) / 100;
        if ($brokerageRatio <= 0) return true;
        $brokeragePrice = bcmul($orderInfo['pay_price'], $brokerageRatio, 2);
        if ($brokeragePrice <= 0) return true;
        $mark = $userInfo['nickname'] . '成功消费' . floatval($orderInfo['pay_price']) . '元,奖励推广佣金' . floatval($brokeragePrice);
        self::beginTrans();
        $res1 = UserBill::income('获得推广佣金', $userInfo['spread_uid'], 'now_money', 'brokerage', $brokeragePrice, $orderInfo['id'], 0, $mark);
        $res2 = self::bcInc($userInfo['spread_uid'], 'brokerage_price', $brokeragePrice, 'uid');
        $res = $res1 && $res2;
        self::checkTrans($res);
        if ($res) self::backOrderBrokerageTwo($orderInfo);
        return $res;
    }

    /**
     * 二级推广
     * @param $orderInfo
     * @return bool
     */
    public static function backOrderBrokerageTwo($orderInfo)
    {
        $userInfo = User::getUserInfo($orderInfo['uid']);
        $userInfoTwo = User::getUserInfo($userInfo['spread_uid']);
        if (!$userInfoTwo || !$userInfoTwo['spread_uid']) return true;
        $storeBrokerageStatu = SystemConfigService::get('store_brokerage_statu') ?: 1;//获取后台分销类型
        if ($storeBrokerageStatu == 1) {
            if (!User::be(['uid' => $userInfoTwo['spread_uid'], 'is_promoter' => 1])) return true;
        }
        $brokerageRatio = bcdiv(SystemConfigService::get('store_brokerage_two'),100,2);
        if ($brokerageRatio <= 0) return true;
        $brokeragePrice = bcmul($orderInfo['pay_price'], $brokerageRatio, 2);
        if ($brokeragePrice <= 0) return true;
        $mark = '二级推广人' . $userInfo['nickname'] . '成功消费' . floatval($orderInfo['pay_price']) . '元,奖励推广佣金' . floatval($brokeragePrice);
        self::beginTrans();
        $res1 = UserBill::income('获得推广佣金', $userInfoTwo['spread_uid'], 'now_money', 'brokerage', $brokeragePrice, $orderInfo['id'], 0, $mark);
        $res2 = self::bcInc($userInfoTwo['spread_uid'], 'brokerage_price', $brokeragePrice, 'uid');
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }

    /**
     * 获取登陆的手机号码
     * @param int $uid 用户id
     * @param string $phone 用户号码
     * @return string
     * */
    public static function getLogPhone($uid, $phone = null)
    {
        $name = '__login_phone_num' . $uid;
        if (!Cookie::get('__login_phone')) return null;
        if (Session::has($name, 'wap')) $phone = Session::get($name, 'wap');
        if (is_null($phone)) {
            if (Session::has('__login_phone_number', 'wap')) $phone = Session::get('__login_phone_number', 'wap');
        }
        return $phone;
    }

    /**
     * 获取推广人列表
     * @param $where array 查询条件
     * @param $uid int 用户uid
     * @return array
     * */
    public static function GetSpreadList($where, $uid)
    {
        $uids = self::getSpeadUids($uid, true);
        if (!count($uids)) return ['list' => [], 'page' => 2];
        $model = self::where('uid', 'in', $uids)->field(['nickname', 'phone', 'uid']);
        if ($where['search']) $model = $model->where('nickname|uid|phone', 'like', "%$where[search]%");
        $list = $model->page((int)$where['page'], (int)$where['limit'])->select();
        $list = count($list) ? $list->toArray() : [];
        $page = $where['page'] + 1;
        foreach ($list as $key => &$item) {
            $item['sellout_count'] = UserBill::where(['a.paid' => 1, 'a.is_del' => 0])->where('u.category', 'now_money')->where('u.type', 'brokerage')->alias('u')->join('__STORE_ORDER__ a', 'a.id=u.link_id')
                ->where('u.uid', $item['uid'])->count();
            $item['sellout_money'] = UserBill::where(['a.paid' => 1, 'a.is_del' => 0])->where('u.category', 'now_money')->where('u.type', 'brokerage')->alias('u')->join('__STORE_ORDER__ a', 'a.id=u.link_id')
                ->where('u.uid', $item['uid'])->sum('a.total_price');
        }
        return compact('list', 'page');
    }

    /**
     * 获取当前用户的下两级
     * @param int $uid 用户uid
     * @return array
     * */
    public static function getSpeadUids($uid, $isOne = false)
    {
        $uids = User::where('spread_uid', $uid)->column('uid');
        if ($isOne) return $uids;
        $two_uids = count($uids) ? User::where('spread_uid', 'in', $uids)->column('uid') : [];
        return array_merge($uids, $two_uids);
    }
}