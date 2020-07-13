<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2018/01/05
 */

namespace app\wap\model\user;

use app\routine\model\user\SystemVip;
use app\routine\model\user\UserVip;
use app\wap\model\user\WechatUser;
use basic\ModelBasic;
use service\SystemConfigService;
use service\WechatService;
use think\Log;
use traits\ModelTrait;
//use app\routine\model\user\User;
use app\wap\model\user\User;
use behavior\wechat\PaymentBehavior;
use service\HookService;
class UserRecharge extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function addRecharge($uid,$price,$recharge_type = 'weixin',$paid = 0)
    {
        $order_id = self::getNewOrderId();
        $rate = SystemConfigService::get('gold_rate');
        return self::set(compact('order_id','uid','price','recharge_type','paid','rate'));
    }

    public static function getNewOrderId()
    {
        $count = (int) self::where('add_time',['>=',strtotime(date("Y-m-d"))],['<',strtotime(date("Y-m-d",strtotime('+1 day')))])->count();
        return 'wx1'.date('YmdHis',time()).(10000+$count+1);
    }

   /* public static function jsPay($orderInfo,$file='user_recharge',$title='用户充值')
    {
        return WechatService::jsPay(WechatUser::uidToOpenid($orderInfo['uid']),$orderInfo['order_id'],$orderInfo['price'],$file,$title);
    }*/
    public static function jsRechargePay($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['price'] <= 0) exception('该订单无需支付!');
        $openid = WechatUser::uidToOpenid($orderInfo['uid']);
        return WechatService::jsPay($openid, $orderInfo['order_id'], $orderInfo['price'], 'recharge', SystemConfigService::get('site_name'));
    }

    public static function yuePay($order_id, $user_info)
    {
        $uid = $user_info['uid'];
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        if ($orderInfo['recharge_type'] != 'yue') return self::setErrorInfo('该订单不能使用余额支付!');
        $userInfo = User::getUserInfo($uid);
        if ($userInfo['now_money'] < $orderInfo['price'])
            return self::setErrorInfo('余额不足' . floatval($orderInfo['price']));
        self::beginTrans();
        $res1 = self::where('order_id',$order_id)->update(['paid'=>1,'pay_time'=>time()]);
       // $res1 = false !== User::bcDec($uid, 'now_money', $orderInfo['price'], 'uid');
       // $res2 = UserBill::expend('购买专题', $uid, 'now_money', 'pay_product', $orderInfo['pay_price'], $orderInfo['id'], $userInfo['now_money'], '余额支付' . floatval($orderInfo['pay_price']) . '元购买专题');
        $goldNum = money_rate_num((int)$orderInfo['price'], 'gold');
        $goldName = SystemConfigService::get("gold_name");
        $res2 = false !== UserBill::income('用户充值'.$goldName,$orderInfo['uid'],'gold_num','recharge',$goldNum,0,$user_info['gold_num'],'用户充值'.$orderInfo['price'].'元人民币获得'.$goldNum.'个'.$goldName);
        $res3 = User::bcInc($orderInfo['uid'],'gold_num',$goldNum,'uid');
        $res4 = User::bcDec($orderInfo['uid'],'now_money',$orderInfo['price'],'uid');
        $res5 = UserBill::expend('充值金币', $uid, 'now_money', 'recharge', $orderInfo['price'], $orderInfo['id'], $userInfo['now_money'], '余额支付' . floatval($orderInfo['price']) . '元充值'.$goldName);
        try {
           // HookService::listen('yue_pay_product', $userInfo, $orderInfo, false, PaymentBehavior::class);
            $res = $res1 && $res2 && $res3 && $res4 && ($res5 ? true : false);
            self::checkTrans($res);
            return $res;
        } catch (\Exception $e) {
            self::rollbackTrans();
            return self::setErrorInfo($e->getMessage());
        }

    }
    /**
     * //TODO用户微信充值成功后
     * @param $orderId
     */
    public static function rechargeSuccess($orderId)
    {
        $order = self::where('order_id',$orderId)->where('paid',0)->find();
        if(!$order) return false;
        $user = User::getUserInfo($order['uid']);
        self::beginTrans();
        $res1 = self::where('order_id',$order['order_id'])->update(['paid'=>1,'pay_time'=>time()]);
        $goldNum = money_rate_num((int)$order['price'], 'gold');
        $goldName = SystemConfigService::get("gold_name");
        $res2 = UserBill::income('用户充值'.$goldName,$order['uid'],'gold_num','recharge',$goldNum,0,$user['gold_num'],'用户充值'.$order['price'].'元人民币获得'.$goldNum.'个'.$goldName);
        $res3 = User::bcInc($order['uid'],'gold_num',$goldNum,'uid');
        $res = $res1 && $res2 && $res3 ;
        self::checkTrans($res);
        return $res;
    }
    /*
     * 成为合伙人后
     * */
    public static function UserPartnerSuccess($orderId){
        $order = self::where('order_id',$orderId)->where('paid',0)->find();
        if(!$order) return false;
        self::startTrans();
        try{
            $user = User::getUserInfo($order['uid']);
            $overdue_time=\service\SystemConfigService::get('overdue_time');
            $overdue_time=bcmul($overdue_time,365*24*60*60,0);
            $overdue_time=bcadd($overdue_time,time(),0);
            $res1 = self::where('order_id',$order['order_id'])->update(['paid'=>1,'pay_time'=>time()]);
            $res2 = UserBill::income('用户成为合伙人',$order['uid'],'now_money','become_partner',$order['price'],$order['id'],$user['now_money'],'成功充值金额'.floatval($order['price']).'元,成为合伙人');
            User::where(['uid'=>$order['uid']])->update(['is_partner'=>1,'overdue_time'=>$overdue_time]);
            self::commit();
        }catch (\Exception $e){
            self::rollback();
        }
    }

    public static function paySuccessBuyVip($orderId){
        $order = self::where('order_id',$orderId)->where('paid',0)->find();
        if(!$order) return false;
        $vipinfo=SystemVip::get($order['vip_id']);
        $user = User::getUserInfo($order['uid']);
        self::beginTrans();
        $res = self::where('order_id',$order['order_id'])->update(['paid'=>1,'pay_time'=>time()]);
        $res1=UserVip::setVip($order['uid'],$order['vip_id']);
        $res2 = UserBill::income('用户购买会员功能',$order['uid'],'vip','buy_vip',$order['price'],$order['id'],$user['now_money'],'成功购买会员'.$vipinfo['title']);
        $res =$res && $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }
}