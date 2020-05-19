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
use service\WechatService;
use traits\ModelTrait;
use app\routine\model\user\User;

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
        return self::set(compact('order_id','uid','price','recharge_type','paid'));
    }

    public static function getNewOrderId()
    {
        $count = (int) self::where('add_time',['>=',strtotime(date("Y-m-d"))],['<',strtotime(date("Y-m-d",strtotime('+1 day')))])->count();
        return 'wx1'.date('YmdHis',time()).(10000+$count+1);
    }

    public static function jsPay($orderInfo,$file='user_recharge',$title='用户充值')
    {
        return WechatService::jsPay(WechatUser::uidToOpenid($orderInfo['uid']),$orderInfo['order_id'],$orderInfo['price'],$file,$title);
    }

    /**
     * //TODO用户充值成功后
     * @param $orderId
     */
    public static function rechargeSuccess($orderId)
    {
        $order = self::where('order_id',$orderId)->where('paid',0)->find();
        if(!$order) return false;
        $user = User::getUserInfo($order['uid']);
        self::beginTrans();
        $res1 = self::where('order_id',$order['order_id'])->update(['paid'=>1,'pay_time'=>time()]);
        $res2 = UserBill::income('用户余额充值',$order['uid'],'now_money','recharge',$order['price'],$order['id'],$user['now_money'],'成功充值余额'.floatval($order['price']).'元');
        $res3 = User::edit(['now_money'=>bcadd($user['now_money'],$order['price'],2)],$order['uid'],'uid');
        $res = $res1 && $res2 && $res3;
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