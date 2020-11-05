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


namespace app\wap\controller;


use app\wap\model\user\SmsCode;
use app\wap\model\user\SystemVip;
use app\wap\model\store\StoreBargain;
use app\wap\model\store\StoreBargainUser;
use app\wap\model\store\StoreBargainUserHelp;
use app\wap\model\store\StoreCouponIssue;
use app\wap\model\store\StoreCouponIssueUser;
use app\wap\model\store\StoreOrderCartInfo;
use app\wap\model\store\StorePink;
use app\wap\model\store\StoreProductReply;
use app\wap\model\store\StoreService;
use app\wap\model\store\StoreServiceLog;
use app\wap\model\store\StoreCart;
use app\wap\model\store\StoreCategory;
use app\wap\model\store\StoreCouponUser;
use app\wap\model\store\StoreOrder;
use app\wap\model\store\StoreProduct;
use app\wap\model\store\StoreProductAttr;
use app\wap\model\store\StoreProductRelation;
use app\wap\model\user\User;
use app\wap\model\user\UserAddress;
use app\wap\model\user\UserBill;
use app\wap\model\user\UserExtract;
use app\wap\model\user\UserRecharge;
use app\wap\model\user\UserNotice;
use app\wap\model\user\UserSign;
use app\wap\model\user\SignPoster;
use app\wap\model\user\WechatUser;
use behavior\wap\StoreProductBehavior;
use service\AliMessageService;
use service\WechatTemplateService;
use service\CacheService;
use service\HookService;
use service\JsonService;
use service\SystemConfigService;
use service\GroupDataService;
use service\UtilService;
use service\WechatService;
use think\Cache;
use think\Request;
use think\Session;
use think\Url;
use app\wap\model\user\MemberShip;
use app\wap\model\user\MemberCard;//会员卡
use app\wap\model\user\MemberCardBatch;//会员卡批次
class AuthApi extends AuthController
{

    public static function WhiteList()
    {
        return [
            'code'
        ];
    }

    public function upload()
    {
        $aliyunOss = \Api\AliyunOss::instance([
            'AccessKey' => SystemConfigService::get('accessKeyId'),
            'AccessKeySecret' => SystemConfigService::get('accessKeySecret'),
            'OssEndpoint' => SystemConfigService::get('end_point'),
            'OssBucket' => SystemConfigService::get('OssBucket'),
            'uploadUrl' => SystemConfigService::get('uploadUrl'),
        ]);
        $res = $aliyunOss->upload('file');
        if ($res) {
            return JsonService::successful('上传成功', ['url' => $res['url']]);
        } else {
            return JsonService::fail('上传失败');
        }
    }

    /**
     * 发送短信验证码
     * @param string $phone
     */
    public function code($phone = '')
    {
        $name = "is_phone_code" . $phone;
        if ($phone == '') return JsonService::fail('请输入手机号码!');
        $time = Session::get($name, 'routine');
        if ($time < time() + 60) Session::delete($name, 'routine');
        if (Session::has($name, 'routine') && $time < time()) return JsonService::fail('您发送验证码的频率过高,请稍后再试!');
        $code = AliMessageService::getVerificationCode();
        SmsCode::set(['tel' => $phone, 'code' => $code, 'last_time' => time() + 300, 'uid' => $this->uid]);
        Session::set($name, time() + 60, 'routine');
        $res = AliMessageService::sendmsg($phone, $code);
        if($res){
            return JsonService::successful('发送成功',$res);
        } else {
            return JsonService::fail('发送失败!');
        }
    }

    /**
     * 用户签到信息
     */
    public function getUserList(){
        $signList = UserSign::userSignInlist($this->userInfo['uid'],1,3);
        return JsonService::successful($signList);
    }

    /**
     * 签到明细
     */
    public function getUserSignList($page,$limit){
        $signList = UserSign::userSignInlist($this->userInfo['uid'],$page,$limit);
        return JsonService::successful($signList);
    }
    /**
     * 签到
     */
    public function user_sign()
    {
        $gold_name=SystemConfigService::get('gold_name');//虚拟币名称
        $signed = UserSign::checkUserSigned($this->userInfo['uid']);
        if ($signed) return JsonService::fail('已签到');
        if (false !== $gold_coin = UserSign::sign($this->userInfo,$gold_name)){
            $poster=SignPoster::todaySignPoster($this->userInfo['uid']);
            if($poster){
                return JsonService::successful('签到获得' . floatval($gold_coin).$gold_name,$poster);
            }else{
                return JsonService::fail('生成海报失败!');
            }
        }else
            return JsonService::fail('签到失败!');
    }

    /**
     * 用户信息
     */
    public function userInfo(){
        $user=$this->userInfo;
        $surplus=0; //会员剩余天数
        $time=bcsub($user['overdue_time'],time(),0);
        if($user['level']>0 && $time>0) $surplus=bcdiv($time,86400,0);
        $user['surplus']=$surplus;
        return JsonService::successful($user);
    }

    /**
     * 会员页数据
     */
    public function merberDatas(){
        $interests=GroupDataService::getData('membership_interests',3)?:[];
        $description=GroupDataService::getData('member_description')?:[];
        $interests_sort = array_column($interests,'sort');
        array_multisort($interests_sort,SORT_ASC,$interests);
        $description_sort = array_column($description,'sort');
        array_multisort($description_sort,SORT_ASC,$description);
        $data['interests']=$interests;
        $data['description']=$description;
        $data['member']=MemberShip::memberMinOne();
        $data['freeData']=MemberShip::memberFree($this->userInfo['uid']);
        return JsonService::successful($data);
    }

    /**
     * 会员设置列表
     */
    public function membershipLists(){
        $meList=MemberShip::membershipList();
        return JsonService::successful($meList);
    }

    /**
     * 会员卡激活
     */
    public function confirm_activation(){
        $request = Request::instance();
        if (!$request->isPost()) return JsonService::fail('参数错误!');
        $data = UtilService::postMore([
            ['member_code', ''],
            ['member_pwd', ''],
        ], $request);
        $res=MemberCard::confirmActivation($data,$this->userInfo);
        if($res)
            return JsonService::successful('激活成功');
        else
            return JsonService::fail(MemberCard::getErrorInfo('激活失败!'));
    }
    /**
     * 用户购买会员
     */
    public function memberPurchase($id=0){
        if(!$id) return JsonService::fail('参数错误!');
        $order = StoreOrder::cacheMemberCreateOrder($this->userInfo['uid'],$id,'weixin');
        $orderId = $order['order_id'];
        $info = compact('orderId');
        if ($orderId) {
                $orderInfo = StoreOrder::where('order_id', $orderId)->find();
                if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
                if ($orderInfo['paid']) exception('支付已支付!');
                if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                    if (StoreOrder::jsPayMePrice($orderId, $this->userInfo['uid']))
                        return JsonService::status('success', '领取成功', $info);
                    else
                        return JsonService::status('pay_error', StoreOrder::getErrorInfo());
                } else {
                    try {
                        $jsConfig = StoreOrder::jsPayMember($orderId);
                    } catch (\Exception $e) {
                        return JsonService::status('pay_error', $e->getMessage(), $info);
                    }
                    $info['jsConfig'] = $jsConfig;
                    return JsonService::status('wechat_pay', '领取成功', $info);
                }
        } else {
            return JsonService::fail(StoreOrder::getErrorInfo('领取失败!'));
        }
    }
    /**
     * 购买会员
     * @param string $vip_id
     * @throws \think\exception\DbException
     */
    public function become_vip($vip_id = '')
    {
        if (!$vip_id) return JsonService::fail('参数错误!');
        $systemvip = SystemVip::get($vip_id);
        if (!$systemvip) return JsonService::fail('您购买的会员不存在');
        $ordervip['order_id'] = UserRecharge::getNewOrderId($this->uid);
        $ordervip['uid'] = $this->uid;
        $ordervip['price'] = $systemvip->money;
        //记录会员购买
        $recharge['order_id'] = $ordervip['order_id'];
        $recharge['uid'] = $ordervip['uid'];
        $recharge['price'] = $ordervip['price'];
        $recharge['recharge_type'] = 'buy_vip';
        $recharge['paid'] = 0;
        $recharge['vip_id'] = $vip_id;
        $recharge['add_time'] = time();
        $recharge['refund_price'] = 0;
        UserRecharge::set($recharge);
        if ($jspay = UserRecharge::jsPay($ordervip, 'buy_vip', $systemvip->title))
            return JsonService::successful($jspay);
        else
            return JsonService::fail('订单生成失败');
    }

    public function set_cart($productId = '', $cartNum = 1, $uniqueId = '')
    {
        if (!$productId || !is_numeric($productId)) return $this->failed('参数错误!');
        $res = StoreCart::setCart($this->userInfo['uid'], $productId, $cartNum, $uniqueId, 'product');
        if (!$res)
            return $this->failed(StoreCart::getErrorInfo('加入购物车失败!'));
        else {
            HookService::afterListen('store_product_set_cart_after', $res, $this->userInfo, false, StoreProductBehavior::class);
            return $this->successful('ok', ['cartId' => $res->id]);
        }
    }

    public function now_buy($productId = '', $cartNum = 1, $uniqueId = '', $combinationId = 0, $secKillId = 0, $bargainId = 0)
    {
        if ($productId == '') return $this->failed('参数错误!');
        if ($bargainId && StoreBargainUserHelp::getSurplusPrice($bargainId, $this->userInfo['uid'])) return JsonService::fail('请先砍价');
        $res = StoreCart::setCart($this->userInfo['uid'], $productId, $cartNum, $uniqueId, 'product', 1, $combinationId, $secKillId, $bargainId);
        if (!$res)
            return $this->failed(StoreCart::getErrorInfo('订单生成失败!'));
        else {
            return $this->successful('ok', ['cartId' => $res->id]);
        }
    }

    public function like_product($productId = '', $category = 'product')
    {
        if (!$productId || !is_numeric($productId)) return $this->failed('参数错误!');
        $res = StoreProductRelation::productRelation($productId, $this->userInfo['uid'], 'like', $category);
        if (!$res)
            return $this->failed(StoreProductRelation::getErrorInfo('点赞失败!'));
        else
            return $this->successful();
    }

    public function unlike_product($productId = '', $category = 'product')
    {

        if (!$productId || !is_numeric($productId)) return $this->failed('参数错误!');
        $res = StoreProductRelation::unProductRelation($productId, $this->userInfo['uid'], 'like', $category);
        if (!$res)
            return $this->failed(StoreProductRelation::getErrorInfo('取消点赞失败!'));
        else
            return $this->successful();
    }

    public function collect_product($productId, $category = 'product')
    {
        if (!$productId || !is_numeric($productId)) return $this->failed('参数错误!');
        $res = StoreProductRelation::productRelation($productId, $this->userInfo['uid'], 'collect', $category);
        if (!$res)
            return $this->failed(StoreProductRelation::getErrorInfo('收藏失败!'));
        else
            return $this->successful();
    }

    public function uncollect_product($productId, $category = 'product')
    {
        if (!$productId || !is_numeric($productId)) return $this->failed('参数错误!');
        $res = StoreProductRelation::unProductRelation($productId, $this->userInfo['uid'], 'collect', $category);
        if (!$res)
            return $this->failed(StoreProductRelation::getErrorInfo('取消收藏失败!'));
        else
            return $this->successful();
    }

    public function get_cart_num()
    {
        return JsonService::successful('ok', StoreCart::getUserCartNum($this->userInfo['uid'], 'product'));
    }

    public function get_cart_list()
    {
        return JsonService::successful('ok', StoreCart::getUserProductCartList($this->userInfo['uid']));
    }

    public function change_cart_num($cartId = '', $cartNum = '')
    {
        if (!$cartId || !$cartNum || !is_numeric($cartId) || !is_numeric($cartNum)) return JsonService::fail('参数错误!');
        StoreCart::changeUserCartNum($cartId, $cartNum, $this->userInfo['uid']);
        return JsonService::successful();
    }

    public function remove_cart($ids = '')
    {
        if (!$ids) return JsonService::fail('参数错误!');
        StoreCart::removeUserCart($this->userInfo['uid'], $ids);
        return JsonService::successful();
    }


    public function get_use_coupon()
    {
        return JsonService::successful('', StoreCouponUser::getUserValidCoupon($this->userInfo['uid']));
    }

    public function get_user_collect_product($first = 0, $limit = 8)
    {
        $list = StoreProductRelation::where('A.uid', $this->userInfo['uid'])
            ->field('B.id pid,B.store_name,B.price,B.ot_price,B.sales,B.image,B.is_del,B.is_show')->alias('A')
            ->where('A.type', 'collect')->where('A.category', 'product')
            ->order('A.add_time DESC')->join('__STORE_PRODUCT__ B', 'A.product_id = B.id')
            ->limit($first, $limit)->select()->toArray();
        foreach ($list as $k => $product) {
            if ($product['pid']) {
                $list[$k]['is_fail'] = $product['is_del'] && $product['is_show'];
            } else {
                unset($list[$k]);
            }
        }
        return JsonService::successful($list);
    }

    public function remove_user_collect_product($productId = '')
    {
        if (!$productId || !is_numeric($productId)) return JsonService::fail('参数错误!');
        StoreProductRelation::unProductRelation($productId, $this->userInfo['uid'], 'collect', 'product');
        return JsonService::successful();
    }

    public function set_user_default_address($addressId = '')
    {
        if (!$addressId || !is_numeric($addressId)) return JsonService::fail('参数错误!');
        if (!UserAddress::be(['is_del' => 0, 'id' => $addressId, 'uid' => $this->userInfo['uid']]))
            return JsonService::fail('地址不存在!');
        $res = UserAddress::setDefaultAddress($addressId, $this->userInfo['uid']);
        if (!$res)
            return JsonService::fail('地址不存在!');
        else
            return JsonService::successful();
    }

    public function edit_user_address()
    {
        $request = Request::instance();
        if (!$request->isPost()) return JsonService::fail('参数错误!');
        $addressInfo = UtilService::postMore([
            ['address', []],
            ['is_default', false],
            ['real_name', ''],
            ['post_code', ''],
            ['phone', ''],
            ['detail', ''],
            ['id', 0]
        ], $request);
        $addressInfo['province'] = $addressInfo['address']['province'];
        $addressInfo['city'] = $addressInfo['address']['city'];
        $addressInfo['district'] = $addressInfo['address']['district'];
        $addressInfo['is_default'] = $addressInfo['is_default'] == true ? 1 : 0;
        $addressInfo['uid'] = $this->userInfo['uid'];
        unset($addressInfo['address']);

        if ($addressInfo['id'] && UserAddress::be(['id' => $addressInfo['id'], 'uid' => $this->userInfo['uid'], 'is_del' => 0])) {
            $id = $addressInfo['id'];
            unset($addressInfo['id']);
            if (UserAddress::edit($addressInfo, $id, 'id')) {
                if ($addressInfo['is_default'])
                    UserAddress::setDefaultAddress($id, $this->userInfo['uid']);
                return JsonService::successful();
            } else
                return JsonService::fail('编辑收货地址失败!');
        } else {
            if ($address = UserAddress::set($addressInfo)) {
                if ($addressInfo['is_default'])
                    UserAddress::setDefaultAddress($address->id, $this->userInfo['uid']);
                return JsonService::successful();
            } else
                return JsonService::fail('添加收货地址失败!');
        }


    }

    public function user_default_address()
    {
        $defaultAddress = UserAddress::getUserDefaultAddress($this->userInfo['uid'], 'id,real_name,phone,province,city,district,detail,is_default');
        if ($defaultAddress)
            return JsonService::successful('ok', $defaultAddress);
        else
            return JsonService::successful('empty', []);
    }

    public function remove_user_address($addressId = '')
    {
        if (!$addressId || !is_numeric($addressId)) return JsonService::fail('参数错误!');
        if (!UserAddress::be(['is_del' => 0, 'id' => $addressId, 'uid' => $this->userInfo['uid']]))
            return JsonService::fail('地址不存在!');
        if (UserAddress::edit(['is_del' => '1'], $addressId, 'id'))
            return JsonService::successful();
        else
            return JsonService::fail('删除地址失败!');
    }

    /**
     * 创建订单
     * @param string $key
     * @return \think\response\Json
     */
    public function create_order($key = '')
    {
        if (!$key) return JsonService::fail('参数错误!');
        if (StoreOrder::be(['order_id|unique' => $key, 'uid' => $this->userInfo['uid'], 'is_del' => 0]))
            return JsonService::status('extend_order', '订单已生成', ['orderId' => $key, 'key' => $key]);
        list($addressId, $couponId, $payType, $useIntegral, $mark, $combinationId, $pinkId, $seckill_id, $bargainId, $integral_id) = UtilService::postMore([
            'addressId', 'couponId', 'payType', 'useIntegral', 'mark', ['combinationId', 0], ['pinkId', 0], ['seckill_id', 0], ['bargainId', 0], ['integral_id', 0]
        ], Request::instance(), true);
        $payType = strtolower($payType);
        if ($bargainId) StoreBargainUser::setBargainUserStatus($bargainId, $this->userInfo['uid']);//修改砍价状态
        if ($pinkId) if (StorePink::getIsPinkUid($pinkId)) return JsonService::status('ORDER_EXIST', '订单生成失败，你已经在该团内不能再参加了', ['orderId' => StoreOrder::getStoreIdPink($pinkId)]);
        if ($pinkId) if (StoreOrder::getIsOrderPink($pinkId)) return JsonService::status('ORDER_EXIST', '订单生成失败，你已经参加该团了，请先支付订单', ['orderId' => StoreOrder::getStoreIdPink($pinkId)]);
        $order = StoreOrder::cacheKeyCreateOrder($this->userInfo['uid'], $key, $addressId, $payType, $useIntegral, $couponId, $mark, $combinationId, $pinkId, $seckill_id, $bargainId, $integral_id);
        $orderId = $order['order_id'];
        $info = compact('orderId', 'key');
        if ($orderId) {
            if ($payType == 'weixin') {
                $orderInfo = StoreOrder::where('order_id', $orderId)->find();
                if (!$orderInfo || !isset($orderInfo['paid'])) exception('支付订单不存在!');
                if ($orderInfo['paid']) exception('支付已支付!');
                if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                    if (StoreOrder::jsPayPrice($orderId, $this->userInfo['uid']))
                        return JsonService::status('success', '微信支付成功', $info);
                    else
                        return JsonService::status('pay_error', StoreOrder::getErrorInfo());
                } else {
                    try {
                        $jsConfig = StoreOrder::jsPay($orderId);
                    } catch (\Exception $e) {
                        return JsonService::status('pay_error', $e->getMessage(), $info);
                    }
                    $info['jsConfig'] = $jsConfig;
                    return JsonService::status('wechat_pay', '订单创建成功', $info);
                }
            } else if ($payType == 'yue') {
                if (StoreOrder::yuePay($orderId, $this->userInfo['uid']))
                    return JsonService::status('success', '余额支付成功', $info);
                else
                    return JsonService::status('pay_error', StoreOrder::getErrorInfo());
            } else if ($payType == 'offline') {
                StoreOrder::createOrderTemplate($order);
                return JsonService::status('success', '订单创建成功', $info);
            }
        } else {
            return JsonService::fail(StoreOrder::getErrorInfo('订单生成失败!'));
        }
    }

    public function get_user_order_list($type = '', $first = 0, $limit = 8, $search = '')
    {
//        StoreOrder::delCombination();//删除拼团未支付订单
        $type == 'null' && $type = '';
        if ($search) {
            $order = StoreOrder::searchUserOrder($this->userInfo['uid'], $search) ?: [];
            $list = $order == false ? [] : [$order];
        } else {
            if ($type == 'first') $type = '';
            $list = StoreOrder::getUserOrderList($this->userInfo['uid'], $type, $first, $limit);
        }
        foreach ($list as $k => $order) {
            $list[$k] = StoreOrder::tidyOrder($order, true);
            if ($list[$k]['_status']['_type'] == 3) {
                foreach ($order['cartInfo'] ?: [] as $key => $product) {
                    $list[$k]['cartInfo'][$key]['is_reply'] = StoreProductReply::isReply($product['unique'], 'product');
                }
            }
        }
        return JsonService::successful($list);
    }

    public function user_remove_order($uni = '')
    {
        if (!$uni) return JsonService::fail('参数错误!');
        $res = StoreOrder::removeOrder($uni, $this->userInfo['uid']);
        if ($res)
            return JsonService::successful();
        else
            return JsonService::fail(StoreOrder::getErrorInfo());
    }

    /**
     * 支付订单
     * @param string $uni
     * @return \think\response\Json
     */
    public function pay_order($uni = '')
    {
        if (!$uni) return JsonService::fail('参数错误!');
        $order = StoreOrder::getUserOrderDetail($this->userInfo['uid'], $uni);
        if (!$order) return JsonService::fail('订单不存在!');
        if ($order['paid']) return JsonService::fail('该订单已支付!');
        if ($order['pink_id']) if (StorePink::isPinkStatus($order['pink_id'])) return JsonService::fail('该订单已失效!');
        if ($order['pay_type'] == 'weixin') {
            try {
                $jsConfig = StoreOrder::jsPay($order);
            } catch (\Exception $e) {
                return JsonService::fail($e->getMessage());
            }
            return JsonService::status('wechat_pay', ['jsConfig' => $jsConfig, 'order_id' => $order['order_id']]);
        } else if ($order['pay_type'] == 'yue') {
            if ($res = StoreOrder::yuePay($order['order_id'], $this->userInfo['uid']))
                return JsonService::successful('余额支付成功');
            else
                return JsonService::fail(StoreOrder::getErrorInfo());
        } else if ($order['pay_type'] == 'offline') {
            StoreOrder::createOrderTemplate($order);
            return JsonService::successful('订单创建成功');
        }
    }

    public function apply_order_refund($uni = '', $text = '')
    {
        if (!$uni || $text == '') return JsonService::fail('参数错误!');
        $res = StoreOrder::orderApplyRefund($uni, $this->userInfo['uid'], $text);
        if ($res)
            return JsonService::successful();
        else
            return JsonService::fail(StoreOrder::getErrorInfo());
    }

    public function user_take_order($uni = '')
    {
        if (!$uni) return JsonService::fail('参数错误!');

        $res = StoreOrder::takeOrder($uni, $this->userInfo['uid']);
        if ($res)
            return JsonService::successful();
        else
            return JsonService::fail(StoreOrder::getErrorInfo());
    }

    public function user_wechat_recharge($price = 0,$payType = 0)
    {
        if (!$price || $price <= 0 || !is_numeric($price)) return JsonService::fail('参数错误');
        if (!isset($this->userInfo['uid']) || !$this->userInfo['uid']) return JsonService::fail('用户不存在');
        //$storeMinRecharge = SystemConfigService::get('store_user_min_recharge');
        //if ($price < $storeMinRecharge) return JsonService::fail('充值金额不能低于' . $storeMinRecharge);
        try {
        //充值记录
        $rechargeOrder = UserRecharge::addRecharge($this->userInfo['uid'], $price, $payType);
        if (!$rechargeOrder) return JsonService::fail('充值订单生成失败!');
        $orderId = $rechargeOrder['order_id'];
       /* $a = UserRecharge::rechargeSuccess($orderId);
        print_r($a);return false;*/
        //资金监管记录
        //$goldNum = money_rate_num((int)$price, 'gold');
        $goldName = SystemConfigService::get("gold_name");
       // UserBill::income('用户充值'.$goldName,$this->userInfo['uid'],'gold_num','recharge',$goldNum,0,$this->userInfo['gold_num'],'用户充值'.$price.'元人民币获得'.$goldNum.'个'.$goldName);
       // User::bcInc($this->userInfo['uid'],'gold_num',$goldNum,'uid');
        switch ($payType) {
                case 'weixin':
                    try {
                        $jsConfig = UserRecharge::jsRechargePay($orderId);
                    } catch (\Exception $e) {
                        return JsonService::status('pay_error', $e->getMessage(), $rechargeOrder);
                    }
                    $rechargeOrder['jsConfig'] = $jsConfig;
                    return JsonService::status('wechat_pay', '订单创建成功', $rechargeOrder);
                    break;
                case 'yue':
                    if (UserRecharge::yuePay($orderId, $this->userInfo))
                        return JsonService::status('success', '余额支付成功', $rechargeOrder);
                    else
                        return JsonService::status('pay_error', StoreOrder::getErrorInfo());
                    break;
                case 'zhifubao':
                    $rechargeOrder['orderName'] = $goldName."充值";
                    $rechargeOrder['orderId'] = $orderId;
                    $rechargeOrder['pay_price'] = $price;
                    return JsonService::status('zhifubao_pay','订单创建成功', base64_encode(json_encode($rechargeOrder)));
                    break;
            }
        } catch (\Exception $e) {
            return JsonService::fail($e->getMessage());
        }
    }

    /**余额明细
     * @param int $index
     * @param int $first
     * @param int $limit
     */
    public function user_balance_list($index = 0,$first = 0, $limit = 8)
    {
        $model = UserBill::where('uid', $this->userInfo['uid'])->where('category', 'now_money')
            ->field('title,mark,pm,number,add_time')
            ->where('status', 1)->where('number','>',0);
        switch ($index){
            case 1:
                $model=$model->where('pm',0);
            break;
            case 2:
                $model=$model->where('pm',1);
            break;
        }
        $list=$model->order('add_time DESC')->page((int)$first, (int)$limit)->select();
        $list=count($list) >0 ? $list->toArray() : [];
        foreach ($list as &$v) {
            $v['add_time'] = date('Y/m/d H:i', $v['add_time']);
        }
        return JsonService::successful($list);
    }
    /**金币明细
     * @param int $index
     * @param int $first
     * @param int $limit
     */
    public function user_gold_num_list($index = 0,$first = 0, $limit = 8)
    {
        $model = UserBill::where('uid', $this->userInfo['uid'])->where('category', 'gold_num')
            ->field('title,mark,pm,number,add_time')
            ->where('status', 1)->where('number','>',0);
        switch ($index){
            case 1:
                $model=$model->where('pm',0);
            break;
            case 2:
                $model=$model->where('pm',1);
            break;
        }
        $list=$model->order('add_time DESC')->page((int)$first, (int)$limit)->select();
        $list=count($list) >0 ? $list->toArray() : [];
        foreach ($list as &$v) {
            $v['add_time'] = date('Y/m/d H:i', $v['add_time']);
        }
        return JsonService::successful($list);
    }

    public function user_integral_list($first = 0, $limit = 8)
    {
        $list = UserBill::where('uid', $this->userInfo['uid'])->where('category', 'integral')
            ->field('mark,pm,number,add_time')
            ->where('status', 1)->order('add_time DESC')->limit($first, $limit)->select()->toArray();
        foreach ($list as &$v) {
            $v['add_time'] = date('Y/m/d H:i', $v['add_time']);
            $v['number'] = floatval($v['number']);
        }
        return JsonService::successful($list);

    }

    public function user_comment_product($unique = '')
    {
        if (!$unique) return JsonService::fail('参数错误!');
        $cartInfo = StoreOrderCartInfo::where('unique', $unique)->find();
        $uid = $this->userInfo['uid'];
        if (!$cartInfo || $uid != $cartInfo['cart_info']['uid']) return JsonService::fail('评价产品不存在!');
        if (StoreProductReply::be(['oid' => $cartInfo['oid'], 'unique' => $unique]))
            return JsonService::fail('该产品已评价!');
        $group = UtilService::postMore([
            ['comment', ''], ['pics', []], ['product_score', 5], ['service_score', 5]
        ], Request::instance());
        $group['comment'] = htmlspecialchars(trim($group['comment']));
        if (sensitive_words_filter($group['comment'])) return JsonService::fail('请注意您的用词，谢谢！！');
        if ($group['product_score'] < 1) return JsonService::fail('请为产品评分');
        else if ($group['service_score'] < 1) return JsonService::fail('请为商家服务评分');
        $group = array_merge($group, [
            'uid' => $uid,
            'oid' => $cartInfo['oid'],
            'unique' => $unique,
            'product_id' => $cartInfo['product_id'],
            'reply_type' => 'product'
        ]);
        StoreProductReply::beginTrans();
        $res = StoreProductReply::reply($group, 'product');
        if (!$res) {
            StoreProductReply::rollbackTrans();
            return JsonService::fail('评价失败!');
        }
        try {
            HookService::listen('store_product_order_reply', $group, $cartInfo, false, StoreProductBehavior::class);
        } catch (\Exception $e) {
            StoreProductReply::rollbackTrans();
            return JsonService::fail($e->getMessage());
        }
        StoreProductReply::commitTrans();
        return JsonService::successful();
    }

    public function get_product_category()
    {
        $parentCategory = StoreCategory::pidByCategory(0, 'id,cate_name')->toArray();
        foreach ($parentCategory as $k => $category) {
            $category['child'] = StoreCategory::pidByCategory($category['id'], 'id,cate_name')->toArray();
            $parentCategory[$k] = $category;
        }
        return JsonService::successful($parentCategory);
    }

    public function get_spread_list($first = 0, $limit = 20)
    {
        $list = User::where('spread_uid', $this->userInfo['uid'])->field('uid,nickname,avatar,add_time')->limit($first, $limit)->order('add_time DESC')->select()->toArray();
        foreach ($list as $k => $user) {
            $list[$k]['add_time'] = date('Y/m/d', $user['add_time']);
        }
        return JsonService::successful($list);
    }

    public function get_product_list($keyword = '', $cId = 0, $sId = 0, $priceOrder = '', $salesOrder = '', $news = 0, $first = 0, $limit = 8)
    {
        if (!empty($keyword)) $keyword = base64_decode(htmlspecialchars($keyword));
        $model = StoreProduct::validWhere();
        if ($cId && $sId) {
            $model->where('cate_id', $sId);
        } elseif ($cId) {
            $sids = StoreCategory::pidBySidList($cId) ?: [];
            $sids[] = $cId;
            $model->where('cate_id', 'IN', $sids);
        }
        if (!empty($keyword)) $model->where('keyword|store_name', 'LIKE', "%$keyword%");
        if ($news) $model->where('is_new', 1);
        $baseOrder = '';
        if ($priceOrder) $baseOrder = $priceOrder == 'desc' ? 'price DESC' : 'price ASC';
        if ($salesOrder) $baseOrder = $salesOrder == 'desc' ? 'sales DESC' : 'sales ASC';
        if ($baseOrder) $baseOrder .= ', ';
        $model->order($baseOrder . 'sort DESC, add_time DESC');
        $list = $model->limit($first, $limit)->field('id,store_name,image,sales,price,stock,ficti,keyword')->select()->toArray();
        if ($list) setView($this->uid, 0, $sId, 'search', 'product', $keyword);
        return JsonService::successful($list);
    }

    public function user_get_coupon($couponId = '')
    {
        if (!$couponId || !is_numeric($couponId)) return JsonService::fail('参数错误!');
        if (StoreCouponIssue::issueUserCoupon($couponId, $this->userInfo['uid'])) {
            return JsonService::successful('领取成功');
        } else {
            return JsonService::fail(StoreCouponIssue::getErrorInfo('领取失败!'));
        }
    }

    public function product_reply_list($productId = '', $first = 0, $limit = 8, $filter = 'all')
    {
        if (!$productId || !is_numeric($productId)) return JsonService::fail('参数错误!');
        $list = StoreProductReply::getProductReplyList($productId, $filter, $first, $limit);
        return JsonService::successful($list);
    }

    public function product_attr_detail($productId = '')
    {
        if (!$productId || !is_numeric($productId)) return JsonService::fail('参数错误!');
        list($productAttr, $productValue) = StoreProductAttr::getProductAttrDetail($productId);
        return JsonService::successful(compact('productAttr', 'productValue'));

    }

    public function user_address_list()
    {
        $list = UserAddress::getUserValidAddressList($this->userInfo['uid'], 'id,real_name,phone,province,city,district,detail,is_default');
        return JsonService::successful($list);
    }

    public function get_notice_list($page = 0, $limit = 8)
    {
        $list = UserNotice::getNoticeList($this->userInfo['uid'], $page, $limit);
        return JsonService::successful($list);
    }

    public function see_notice($nid)
    {
        UserNotice::seeNotice($this->userInfo['uid'], $nid);
        return JsonService::successful();
    }

    public function refresh_msn(Request $request)
    {
        $params = $request->post();
        $remind_where = "mer_id = " . $params["mer_id"] . " AND uid = " . $params["uid"] . " AND to_uid = " . $params["to_uid"] . " AND type = 0 AND remind = 0";
        $remind_list = StoreServiceLog::where($remind_where)->order("add_time asc")->select();
        foreach ($remind_list as $key => $value) {
            if (time() - $value["add_time"] > 3) {
                StoreServiceLog::edit(array("remind" => 1), $value["id"]);
                $now_user = StoreService::field("uid,nickname")->where(array("uid" => $params["uid"]))->find();
                if (!$now_user) $now_user = User::field("uid,nickname")->where(array("uid" => $params["uid"]))->find();
                if ($params["to_uid"]) {
                    $userInfo = WechatUser::where('uid', $params["to_uid"])->field(['openid', 'subscribe'])->find();
                    if($userInfo && $userInfo['openid'] && $userInfo['subscribe']) {
                        $head = '客服提醒';
                        $description = '您有新的消息，请注意查收！';
                        $url = SystemConfigService::get('site_url').'/wap/service/service_ing/to_uid/'.$this->uid.'/mer_id/0';
                        $message = WechatService::newsMessage($head, $description, $url, $this->userInfo['avatar']);
                        try {
                            WechatService::staffService()->message($message)->to($userInfo['openid'])->send();
                        } catch (\Exception $e) {
                            \think\Log::error($userInfo['nickname'] . '发送失败' . $e->getMessage());
                        }
                    }
                }
            }
        }
        $where = "mer_id = " . $params["mer_id"] . " AND uid = " . $params["to_uid"] . " AND to_uid = " . $params["uid"] . " AND type = 0";
        $list = StoreServiceLog::where($where)->order("add_time asc")->select()->toArray();
        $ids = [];
        foreach ($list as $key => $value) {
            //设置发送人与接收人区别
            if ($value["uid"] == $params["uid"])
                $list[$key]['my'] = "my";
            else
                $list[$key]['my'] = "to";

            array_push($ids, $value["id"]);
        }

        //设置这些消息为已读
        StoreServiceLog::where(array("id" => array("in", $ids)))->update(array("type" => 1, "remind" => 1));
        return JsonService::successful($list);
    }

    public function add_msn(Request $request)
    {
        $params = $request->post();
        if ($params["type"] == "html")
            $data["msn"] = htmlspecialchars_decode($params["msn"]);
        else
            $data["msn"] = $params["msn"];
        $data["uid"] = $params["uid"];
        $data["to_uid"] = $params["to_uid"];
        $data["mer_id"] = $params["mer_id"] > 0 ? $params["mer_id"] : 0;
        $data["add_time"] = time();
        StoreServiceLog::set($data);
        return JsonService::successful();
    }

    public function get_msn(Request $request)
    {
        $params = $request->post();
        $size = 10;
        $page = $params["page"] >= 0 ? $params["page"] : 1;
        $where = "(mer_id = " . $params["mer_id"] . " AND uid = " . $params["uid"] . " AND to_uid = " . $params["to_uid"] . ") OR (mer_id = " . $params["mer_id"] . " AND uid = " . $params["to_uid"] . " AND to_uid = " . $params["uid"] . ")";
        $list = StoreServiceLog::where($where)->limit(($page - 1) * $size, $size)->order("add_time desc")->select()->toArray();
        foreach ($list as $key => $value) {
            //设置发送人与接收人区别
            if ($value["uid"] == $params["uid"])
                $list[$key]['my'] = "my";
            else
                $list[$key]['my'] = "to";

            //设置这些消息为已读
            if ($value["uid"] == $params["to_uid"] && $value["to_uid"] == $params["uid"]) StoreServiceLog::edit(array("type" => 1, "remind" => 1), $value["id"]);
        }
        $list = array_reverse($list);
        return JsonService::successful($list);
    }

    public function refresh_msn_new(Request $request)
    {
        $params = $request->post();
        $now_user = User::getUserInfo($this->userInfo['uid']);
        if ($params["last_time"] > 0)
            $where = "(uid = " . $now_user["uid"] . " OR to_uid = " . $now_user["uid"] . ") AND add_time>" . $params["last_time"];
        else
            $where = "uid = " . $now_user["uid"] . " OR to_uid = " . $now_user["uid"];


        $msn_list = StoreServiceLog::where($where)->order("add_time desc")->select()->toArray();
        $info_array = $list = [];
        foreach ($msn_list as $key => $value) {
            $to_uid = $value["uid"] == $now_user["uid"] ? $value["to_uid"] : $value["uid"];
            if (!in_array(["to_uid" => $to_uid, "mer_id" => $value["mer_id"]], $info_array)) {
                $info_array[count($info_array)] = ["to_uid" => $to_uid, "mer_id" => $value["mer_id"]];

                $to_user = StoreService::field("uid,nickname,avatar")->where(array("uid" => $to_uid))->find();
                if (!$to_user) $to_user = User::field("uid,nickname,avatar")->where(array("uid" => $to_uid))->find();
                $to_user["mer_id"] = $value["mer_id"];
                $to_user["mer_name"] = '';
                $value["to_info"] = $to_user;
                $value["count"] = StoreServiceLog::where(array("mer_id" => $value["mer_id"], "uid" => $to_uid, "to_uid" => $now_user["uid"], "type" => 0))->count();
                $list[count($list)] = $value;
            }
        }
        return JsonService::successful($list);
    }

    public function get_user_brokerage_list($uid, $first = 0, $limit = 8)
    {
        if (!$uid)
            return $this->failed('用户不存在');
        $list = UserBill::field('A.mark,A.add_time,A.number,A.pm')->alias('A')->limit($first, $limit)
            ->where('A.category', 'now_money')->where('A.type', 'brokerage')
            ->where('A.uid', $this->userInfo['uid'])
            ->join('__STORE_ORDER__ B', 'A.link_id = B.id AND B.uid = ' . $uid)->select()->toArray();
        return JsonService::successful($list);
    }

    public function user_extract()
    {
        if (UserExtract::userExtract($this->userInfo, UtilService::postMore([
            ['type', '', '', 'extract_type'], 'real_name', 'alipay_code', 'bank_code', 'bank_address', ['price', '', '', 'extract_price']
        ])))
            return JsonService::successful('申请提现成功!');
        else
            return JsonService::fail(Extract::getErrorInfo());
    }

    public function get_issue_coupon_list($limit = 2)
    {
        $list = StoreCouponIssue::validWhere('A')->join('__STORE_COUPON__ B', 'A.cid = B.id')
            ->field('A.*,B.coupon_price,B.use_min_price')->order('B.sort DESC,A.id DESC')->limit($limit)->select()->toArray() ?: [];
        $list_coupon = [];
        foreach ($list as $k => &$v) {
            if (!($v['is_use'] = StoreCouponIssueUser::be(['uid' => $this->userInfo['uid'], 'issue_coupon_id' => $v['id']])) && $v['total_count'] > 0 && $v['remain_count'] > 0) {
                array_push($list_coupon, $v);
            }
        }
        return JsonService::successful($list_coupon);
    }

    public function clear_cache($uni = '')
    {
        if ($uni) CacheService::clear();
    }

    /**
     * 获取今天正在拼团的人的头像和名称
     * @return \think\response\Json
     */
    public function get_pink_second_one()
    {
        $addTime = mt_rand(time() - 30000, time());
        $storePink = StorePink::where('p.add_time', 'GT', $addTime)->alias('p')->where('p.status', 1)->join('User u', 'u.uid=p.uid')->field('u.nickname,u.avatar as src')->find();
        return JsonService::successful($storePink);
    }

    public function order_details($uni = '')
    {

        if (!$uni) return JsonService::fail('参数错误!');
        $order = StoreOrder::getUserOrderDetail($this->userInfo['uid'], $uni);
        if (!$order) return JsonService::fail('订单不存在!');
        $order = StoreOrder::tidyOrder($order, true);
        $res = array();
        foreach ($order['cartInfo'] as $v) {
            if ($v['combination_id']) return JsonService::fail('拼团产品不能再来一单，请在拼团产品内自行下单!');
            else  $res[] = StoreCart::setCart($this->userInfo['uid'], $v['product_id'], $v['cart_num'], isset($v['productInfo']['attrInfo']['unique']) ? $v['productInfo']['attrInfo']['unique'] : '', 'product', 0, 0);
        }
        $cateId = [];
        foreach ($res as $v) {
            if (!$v) return JsonService::fail('再来一单失败，请重新下单!');
            $cateId[] = $v['id'];
        }
        return JsonService::successful('ok', implode(',', $cateId));

    }


    /**
     * 帮好友砍价
     * @param int $bargainId
     * @param int $bargainUserId
     * @return \think\response\Json
     */
    public function set_bargain_help()
    {
        list($bargainId, $bargainUserId) = UtilService::postMore([
            'bargainId', 'bargainUserId'], Request::instance(), true);
        if (!$bargainId || !$bargainUserId) return JsonService::fail('参数错误');
        $res = StoreBargainUserHelp::setBargainUserHelp($bargainId, $bargainUserId, $this->userInfo['uid']);
        if ($res) {
            if (!StoreBargainUserHelp::getSurplusPrice($bargainId, $bargainUserId)) {//砍价成功，发模板消息
                $bargainUserTableId = StoreBargainUser::getBargainUserTableId($bargainId, $bargainUserId);
                $bargain = StoreBargain::where('id', $bargainId)->find()->toArray();
                $bargainUser = StoreBargainUser::where('id', $bargainUserTableId)->find()->toArray();
            }
            return JsonService::status('SUCCESS', '砍价成功');
        } else return JsonService::status('ERROR', '砍价失败，请稍后再帮助朋友砍价');
    }

    /**
     * 砍价分享添加次数
     * @param int $bargainId
     */
    public function add_bargain_share($bargainId = 0)
    {
        if (!$bargainId) return JsonService::successful();
        StoreBargain::addBargainShare($bargainId);
        return JsonService::successful();
    }

}