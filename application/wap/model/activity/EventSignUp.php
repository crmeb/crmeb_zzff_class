<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/11
 */

namespace app\wap\model\activity;


use app\wap\model\activity\EventRegistration;
use app\wap\model\user\User;
use app\wap\model\user\UserBill;
use service\HookService;
use traits\ModelTrait;
use basic\ModelBasic;
use think\Db;
use service\SystemConfigService;
use service\WechatService;
use service\WechatTemplateService;
use app\wap\model\user\WechatUser;
use behavior\wechat\PaymentBehavior;
use service\AlipayTradeWapService;
use think\Url;

class EventSignUp extends ModelBasic
{
    use ModelTrait;
    protected $insert = ['add_time'];

    protected static $payType = ['weixin' => '微信支付', 'yue' => '余额支付', 'offline' => '线下支付', 'zhifubao' => '支付宝'];

    protected function setAddTimeAttr()
    {
        return time();
    }
    public static function getNewOrderId()
    {
        $count = (int)self::where('add_time', ['>=', strtotime(date("Y-m-d"))], ['<', strtotime(date("Y-m-d", strtotime('+1 day')))])->count();
        return 'su' . date('YmdHis', time()) . (10000 + $count + 1);
    }
    /**用户提交报名
     * @param $id
     * @param $userName
     * @param $userPhone
     */
    public static function userEventSignUp($id,$signUp,$payType,$uid){
        if (!array_key_exists($payType, self::$payType)) return self::setErrorInfo('选择支付方式有误!');
        $userInfo = User::getUserInfo($uid);
        if (!$userInfo) return self::setErrorInfo('用户不存在!');
        $activity = EventRegistration::oneActivitys($id);
        if (!$activity) return false;
        $count=self::where('paid',1)->where('activity_id',$id)->count();//活动报名总数
        if(bcsub($activity['number'],$count,0)<=0) return self::setErrorInfo('活动报名结束!');
        $userCount=self::where('paid',1)->where('uid',$uid)->where('activity_id',$id)->count();//用户该活动报名次数
        if($activity['restrictions'] && bcsub($activity['restrictions'],$userCount,0)<=0) return self::setErrorInfo('您没有报名次数了!');
        $payPrice = 0;
        if(isset($userInfo['level']) && $userInfo['level'] > 0 && $activity['member_pay_type'] == 1 && $activity['member_price'] > 0){
            $payPrice = $activity['member_price'];
        }elseif ($userInfo['level']==0 && $activity['pay_type'] == 1 && $activity['price'] > 0){
            $payPrice = $activity['price'];
        }
        $data=[
            'order_id'=>self::getNewOrderId(),
            'uid'=>$uid,
            'user_info'=>$signUp,
            'activity_id'=>$id,
            'pay_price'=>$payPrice,
            'pay_type'=>$payType,
        ];
        $order = self::set($data);
        if (!$order) return self::setErrorInfo('报名订单生成失败!');
        return $order;
    }
    /**
     * 微信支付 为 0元时
     * @param $order_id
     * @param $uid
     * @return bool
     */
    public static function jsPayPrice($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        $userInfo = User::getUserInfo($uid);
        self::beginTrans();
        $res1 = UserBill::expend('活动报名成功', $uid, 'now_money', 'pay_sign_up', $orderInfo['pay_price'], $orderInfo['id'], $userInfo['now_money'], '微信支付' . floatval($orderInfo['pay_price']) . '元活动报名');
        $res2 = self::paySuccess($order_id);
        $res = $res1 && $res2;
        self::checkTrans($res);
        return $res;
    }
    public static function jsPay($orderId, $field = 'order_id')
    {
        if (is_string($orderId))
            $orderInfo = self::where($field, $orderId)->find();
        else
            $orderInfo = $orderId;
        if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
        if ($orderInfo['paid']) exception('支付已支付!');
        if ($orderInfo['pay_price'] <= 0) exception('该支付无需支付!');
        $openid = WechatUser::uidToOpenid($orderInfo['uid']);
        return WechatService::jsPay($openid, $orderInfo['order_id'], $orderInfo['pay_price'], 'signup', SystemConfigService::get('site_name'));
    }

    public static function yuePay($order_id, $uid)
    {
        $orderInfo = self::where('uid', $uid)->where('order_id', $order_id)->where('is_del', 0)->find();
        if (!$orderInfo) return self::setErrorInfo('订单不存在!');
        if ($orderInfo['paid']) return self::setErrorInfo('该订单已支付!');
        if ($orderInfo['pay_type'] != 'yue') return self::setErrorInfo('该订单不能使用余额支付!');
        $userInfo = User::getUserInfo($uid);

        if ($userInfo['now_money'] < $orderInfo['pay_price'])
            return self::setErrorInfo('余额不足' . floatval($orderInfo['pay_price']));
        self::beginTrans();
        $res1 = false !== User::bcDec($uid, 'now_money', $orderInfo['pay_price'], 'uid');
        $res2 = UserBill::expend('活动报名', $uid, 'now_money', 'pay_sign_up', $orderInfo['pay_price'], $orderInfo['id'], $userInfo['now_money'], '余额支付' . floatval($orderInfo['pay_price']) . '元活动报名');
        $res3 = self::paySuccess($order_id);
        $res = $res1 && $res2 && $res3;
        self::checkTrans($res);
        return $res;
    }
    /**
     * //TODO 支付成功后
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function paySuccess($orderId)
    {
        $order = self::where('order_id', $orderId)->find();
        $res1 = self::where('order_id', $orderId)->update(['paid' => 1, 'pay_time' => time()]);
        $site_url = SystemConfigService::get('site_url');
        try{
            WechatTemplateService::sendTemplate(WechatUser::where('uid', $order['uid'])->value('openid'), WechatTemplateService::ORDER_PAY_SUCCESS, [
                'first' => '亲，您的活动报名成功',
                'keyword1' => $orderId,
                'keyword2' => $order['pay_price'],
                'remark' => '点击查看报名详情'
            ], $site_url . Url::build('wap/special/grade_list'));
        }catch (\Throwable $e){}
        $res = $res1;
        return false !== $res;
    }

    public static function userSignUpActivityList($uid,$page=1,$limit=20){
        $list=self::alias('s')->join('EventRegistration r','r.id=s.activity_id','left')
            ->where('s.paid',1)->page((int)$page,(int)$limit)->order('s.add_time DESC')->select();
    }
    public static function qrcodes_url($order_id='',$size=5){
        vendor('phpqrcode.phpqrcode');
        $url=SystemConfigService::get('site_url');
        $http=substr($url,0,4);
        $rest = substr($url, -1);
        if($http=='http' && $rest!='/') $urls=$url.'/';
        else if($http!='http') return false;
        else $urls=$url;
        $url=$urls.'wap/my/sign_order/type/2/order_id/'.$order_id;
        $value = $url;			//二维码内容
        $errorCorrectionLevel = 'H';	//容错级别
        $matrixPointSize = $size;			//生成图片大小
        //生成二维码图片
        $filename = 'public/qrcode/'.'su'.rand(10000000,99999999).'.png';
        \QRcode::png($value,$filename , $errorCorrectionLevel, $matrixPointSize, 2);
        return $urls.$filename;
    }

    public static function signUpOrder($id,$uid){
        $order=EventSignUp::where('activity_id',$id)->where('uid',$uid)->where('paid',1)->find();
        if(!$order) return false;
        $activity=EventRegistration::where('id',$order['activity_id'])->field('title,image,phone,province,city,district,detail,write_off_code')->find();
        if(!$activity) return false;
        $activity['order_id']=$order['order_id'];
        if(!$activity['write_off_code']){
            $write_off_code=self::qrcodes_url($order['order_id'],5);
            EventSignUp::where('order_id',$order['order_id'])->update(['write_off_code'=>$write_off_code]);
            $activity['write_off_code']=$write_off_code;
        }
        return $activity;
    }
}