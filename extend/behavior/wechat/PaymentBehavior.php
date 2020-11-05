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


use app\wap\model\store\StoreOrder as StoreOrderRoutineModel;
use app\wap\model\store\StoreOrder as StoreOrderWapModel;
use app\wap\model\user\UserRecharge;
use service\HookService;
use service\RoutineRefund;
use service\WechatService;
use app\wap\model\activity\EventSignUp;
class PaymentBehavior
{

    /**
     * 下单成功之后
     * @param $order
     * @param $prepay_id
     */
    public static function wechatPaymentPrepare($order, $prepay_id)
    {

    }

    /**
     * 支付成功后
     * @param $notify
     * @return bool|mixed
     */
    public static function wechatPaySuccess($notify)
    {
        if(isset($notify->attach) && $notify->attach){
            return HookService::listen('wechat_pay_success_'.strtolower($notify->attach),$notify->out_trade_no,$notify,true,self::class);
        }
        return false;
    }

    /**
     * 商品订单支付成功后  微信公众号
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessProduct($orderId, $notify)
    {
        try{
            if(StoreOrderWapModel::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return StoreOrderWapModel::paySuccess($orderId);
        }catch (\Exception $e){
            return false;
        }
    }
    /**
     * 专题订单支付成功后  微信公众号 支付宝
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessSpecial($orderId, $notify)
    {
        try{
            if(StoreOrderWapModel::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return StoreOrderWapModel::paySuccess($orderId);
        }catch (\Exception $e){
            return false;
        }
    }
    /**
     * 会员订单支付成功后  微信公众号 支付宝
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessMember($orderId, $notify)
    {
        try{
            if(StoreOrderWapModel::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return StoreOrderWapModel::payMeSuccess($orderId);
        }catch (\Exception $e){
            return false;
        }
    }
    /**
     * 活动报名订单支付成功后  微信公众号 支付宝
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessSignup($orderId, $notify)
    {
        try{
            if(EventSignUp::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return EventSignUp::paySuccess($orderId);
        }catch (\Exception $e){
            return false;
        }
    }


    /**
     * 商品订单支付成功后  小程序
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessProductr($orderId, $notify)
    {
        try{
            if(StoreOrderRoutineModel::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return StoreOrderRoutineModel::paySuccess($orderId);
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * 用户充值成功后
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessRecharge($orderId, $notify)
    {
        try{
            if(UserRecharge::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return UserRecharge::rechargeSuccess($orderId);
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * 用户成为合伙人
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessUserPartner($orderId, $notify)
    {
        try{
            if(UserRecharge::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return UserRecharge::UserPartnerSuccess($orderId);
        }catch (\Exception $e){
            return false;
        }
    }
    public static function wechatPaySuccessBuyVip($orderId, $notify){
        try{
            if(UserRecharge::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return UserRecharge::paySuccessBuyVip($orderId);
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * 使用余额支付订单时
     * @param $userInfo
     * @param $orderInfo
     */
    public static function yuePayProduct($userInfo, $orderInfo)
    {


    }


    /**
     * 微信支付订单退款
     * @param $orderNo
     * @param array $opt
     */
    public static function wechatPayOrderRefund($orderNo, array $opt)
    {
        WechatService::payOrderRefund($orderNo,$opt);
    }

    public static function routinePayOrderRefund($orderNo, array $opt)
    {
        $refundDesc = isset($opt['desc']) ? $opt['desc'] : '';
        $res = RoutineRefund::doRefund($opt['pay_price'],$opt['refund_price'],$orderNo,'',$orderNo,$refundDesc);
    }

    /**
     * 微信支付充值退款
     * @param $orderNo
     * @param array $opt
     */

    public static function userRechargeRefund($orderNo, array $opt)
    {
        WechatService::payOrderRefund($orderNo,$opt);
    }
}