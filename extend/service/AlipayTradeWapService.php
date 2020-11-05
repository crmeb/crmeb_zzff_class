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
namespace service;

use think\Log;
use think\Request;
use think\Url;
use behavior\wechat\PaymentBehavior;
use service\HookService;
use service\SystemConfigService;


class AlipayTradeWapService
{

    /**
     * 异步通知地址
     * @var string
     */
    protected static $notifyUrl;

    /**
     * 同步跳转地址
     * @var mixed
     */
    protected static $returnUrl;

    /**
     * 支付宝公钥
     * @var mixed
     */
    protected static $alipayPublicKey;

    /**
     * 应用appid
     * @var mixed
     */
    protected static $alipayAppId;

    /**
     * 应用私钥
     * @var mixed
     */
    protected static $alipayPrivateKey;

    /**
     * 编码格式
     * @var mixed|string
     */
    protected static $charset = 'UTF-8';

    /**
     * 请求网管
     * @var string
     */
    protected static $gatewayUrl = 'https://openapi.alipay.com/gateway.do';

    /**
     * 加密方式
     * @var mixed|string
     */
    protected static $signType = 'RSA2';

    /**
     * 是否开启调试模式
     * @var bool
     */
    public static $isDeBug = true;

    /**
     * 获取不到配置信息错误次数
     * @var int
     */
    private static $ErrorCount = 0;

    /**
     * 获取不到配置信息错误最大次数
     * @var int
     */
    private static $ErrorSum = 3;

    /**
     * AlipayTradeWapService constructor.
     * @param array $confing
     * @throws \Exception
     */
    public function __construct($confing = [])
    {
        self::$ErrorCount++;
        if (self::$ErrorCount >= self::$ErrorSum) return exception('请配置支付宝公钥私钥APPID');
        if ((!self::$alipayAppId || !self::$alipayPublicKey || !self::$alipayPrivateKey) && !$confing) self::confing(true);
        if (isset($confing['returnUrl'])) self::$returnUrl = $confing['returnUrl'];
        if (isset($confing['notifyUrl'])) self::$returnUrl = $confing['notifyUrl'];
        if (isset($confing['signType'])) self::$signType = $confing['signType'];
        if (isset($confing['charset'])) self::$charset = $confing['charset'];
        if (isset($confing['alipay_public_key'])) self::$alipayAppId = $confing['alipay_public_key'];
        if (isset($confing['alipay_public_key'])) self::$alipayPublicKey = $confing['alipay_public_key'];
        if (isset($confing['alipay_private_key'])) self::$alipayPrivateKey = $confing['alipay_private_key'];
        if (!self::$alipayAppId || !self::$alipayPublicKey || !self::$alipayPrivateKey) exception('请配置支付宝公钥私钥APPID');
        self::$ErrorCount = 0;
    }

    /**
     * 设置加密方式
     * @param $signType
     * @return $this
     */
    public function setSignType($signType)
    {
        self::$signType = $signType;
        return $this;
    }

    /**
     * 设置同步回调地址
     * @param $returnUrl
     * @return $this
     */
    public function setReturnUrl($returnUrl)
    {
        self::$returnUrl = $returnUrl;
        return $this;
    }

    /**
     * 设置异步回调地址
     * @param $notifyUrl
     */
    public function setNotifyUrl($notifyUrl)
    {
        self::$notifyUrl = $notifyUrl;
        return $this;
    }

    /**
     * 设置业务参数
     * @param array $biz_content
     * @return string
     */
    protected static function setBizContent(array $biz_content = [])
    {
        if (isset($biz_content['passback_params'])) $biz_content['passback_params'] = urlencode($biz_content['passback_params']);
        if (isset($biz_content['trade_no']) && empty($biz_content['trade_no'])) unset($biz_content['trade_no']);
        $bizContent = json_encode($biz_content);
        //打印业务参数
        self::$isDeBug && self::WriteLog($bizContent);
        return $bizContent;
    }

    /**
     * 获取同步回调地址
     * @return mixed
     */
    public function getReturnUrl()
    {
        return self::$returnUrl;
    }

    /**
     * 获取异步回调地址
     * @return mixed
     */
    public function getNotifyUrl()
    {
        return self::$notifyUrl;
    }

    /**
     * 读取系统配置赋值给静态变量 并加载支付宝官方支付sdk
     * @param bool $isReturn
     * @return AlipayTradeWapService
     */
    public static function confing($isReturn = false)
    {
        $confing = SystemConfigService::more([
            'alipay_public_key',
            'alipay_app_id',
            'alipay_private_key',
        ]);
        self::$alipayAppId = isset($confing['alipay_app_id']) ? trim($confing['alipay_app_id']) : '';
        self::$alipayPublicKey = isset($confing['alipay_public_key']) ? trim($confing['alipay_public_key']) : '';
        self::$alipayPrivateKey = isset($confing['alipay_private_key']) ? trim($confing['alipay_private_key']) : '';
        self::$returnUrl = SystemConfigService::get('site_url') . Url::build('wap/Alipay/alipay_success_synchro');
        self::$notifyUrl = SystemConfigService::get('site_url') . Url::build('wap/Alipay/alipay_success_notify');
        vendor('alipay.AopSdk');
        if ($isReturn == false) return new self;
    }

    /**
     * 静态调用初始化数据
     * @return AlipayTradeWapService
     */
    public static function init()
    {
        return self::confing();
    }

    /**
     * 支付宝异步回调
     */
    public static function handleNotify()
    {
        self::init()->AliPayNotify(function ($data, $result) {
            HookService::listen('wechat_pay_success', $data, null, true, PaymentBehavior::class);
        });
    }

    /**
     * 支付宝异步回调
     * @param callable $notifyFn 闭包函数 参数1,回调返回的参数,回调结果
     * @return bool
     */
    protected function AliPayNotify(callable $notifyFn)
    {
        $post = Request::instance()->post();
        $result = self::AliPaycheck($post);
        if ($result) {
            //商户订单号
            $post['out_trade_no'] = isset($post['out_trade_no']) ? $post['out_trade_no'] : '';
            //支付宝交易号
            $post['trade_no'] = isset($post['trade_no']) ? $post['trade_no'] : '';
            //交易状态
            $post['trade_status'] = isset($post['trade_status']) ? $post['trade_status'] : '';
            //备注
            $post['attach'] = isset($post['passback_params']) ? urldecode($post['passback_params']) : '';
            //异步回调成功执行
            try {
                if (is_callable($notifyFn)) $notifyFn((object)$post, $result);
            } catch (\Exception $e) {
                self::$isDeBug && self::WriteLog('支付宝支付成功,订单号为:' . $post['out_trade_no'] . '.回调报错:' . $e->getMessage());
            }
            echo 'success';
        } else {
            echo 'fail';
        }
        self::$isDeBug && self::WriteLog($result);
        return true;

    }

    /**
     * 支付宝同步回调
     * @return array
     */
    public function AliPayReturn()
    {
        //获取返回参数
        $get = Request::instance()->get();
        //验签成功与否
        $result = self::AliPaycheck($get);
        //记录日志
        self::$isDeBug && self::WriteLog(compact('result', 'get'));
        return compact('result', 'get');
    }

    /**
     * 验签方法
     * @param $arr 验签支付宝返回的信息，使用支付宝公钥。
     * @return boolean
     */
    protected static function AliPaycheck($post)
    {
        $aop = new \AopClient();
        $aop->alipayrsaPublicKey = self::$alipayPublicKey;
        return $aop->rsaCheckV1($post, self::$alipayPrivateKey, self::$signType);
    }

    /**
     * 初始化参数
     * @param $request
     * @param bool $ispage
     * @return mixed|\SimpleXMLElement|string|\提交表单HTML文本
     * @throws \Exception
     */
    protected static function AopclientRequestExecute($request, $ispage = false)
    {
        $aop = new \AopClient ();
        //网管地址
        $aop->gatewayUrl = self::$gatewayUrl;
        //appid
        $aop->appId = self::$alipayAppId;
        //私钥
        $aop->rsaPrivateKey = self::$alipayPrivateKey;
        //公钥
        $aop->alipayrsaPublicKey = self::$alipayPublicKey;
        //版本
        $aop->apiVersion = "1.0";
        //编码格式
        $aop->postCharset = self::$charset;
        //内容格式
        $aop->format = 'JSON';
        //加密方式
        $aop->signType = self::$signType;
        // 开启页面信息输出
        $aop->debugInfo = true;
        if ($ispage) {
            $result = $aop->pageExecute($request, "post");
            echo $result;
        } else
            $result = $aop->Execute($request);
        //打开后，将报文写入log文件
        self::$isDeBug && self::WriteLog($result);
        return $result;
    }

    /**
     * alipay.trade.wap.pay 下单支付手机网站支付版本
     * @param $out_trade_no 下单号
     * @param $total_amount 订单金额 单位元
     * @param $subject 订单标题
     * @param $passback_params 订单备注 会原样返回通常用于回调监听函数
     * @param $product_code 销售产品码，商家和支付宝签约的产品码
     * @param $ispage 是否直接输出
     * @return $response 支付宝返回的信息
     */
    public function AliPayWap($out_trade_no, $total_amount, $subject, $passback_params, $product_code = 'QUICK_MSECURITY_PAY', $ispage = true)
    {
        $request = new \AlipayTradeWapPayRequest();
        $request->setNotifyUrl(self::$notifyUrl);
        $request->setReturnUrl(self::$returnUrl);
        $request->setBizContent(self::setBizContent(compact('out_trade_no', 'total_amount', 'subject', 'passback_params', 'product_code')));
        return self::AopclientRequestExecute($request, $ispage);
    }

    /**
     * alipay.trade.query (统一收单线下交易查询)
     * @param $out_trade_no 下单号
     * @param $trade_no 支付宝订单号
     * @param $passback_params 订单备注 会原样返回通常用于回调监听函数
     * @return $response 支付宝返回的信息
     */
    public function AliPayQuery($out_trade_no, $trade_no, $passback_params)
    {
        $request = new \AlipayTradeQueryRequest();
        $request->setBizContent(self::setBizContent(compact('out_trade_no', 'passback_params', 'trade_no')));
        return self::AopclientRequestExecute($request);
    }

    /**
     * alipay.trade.refund (统一收单交易退款接口)
     * @param $out_trade_no 下单订单号
     * @param $trade_no 支付宝订单号
     * @param $refund_amount 退款金额
     * @param $refund_reason 退款说明
     * @param $passback_params 备注
     * @return $response 支付宝返回的信息
     */
    public function AliPayRefund($out_trade_no, $trade_no, $refund_amount, $refund_reason, $passback_params)
    {
        $request = new \AlipayTradeRefundRequest();
        $request->setBizContent(self::setBizContent(compact('out_trade_no', 'trade_no', 'refund_amount', 'refund_reason', 'passback_params', 'product_code')));
        return self::AopclientRequestExecute($request);
    }

    /**
     * alipay.trade.close (统一收单交易关闭接口)
     * @param $out_trade_no 订单号
     * @param $trade_no 支付宝订单号
     * @return $response 支付宝返回的信息
     */
    public function AliPayClose($out_trade_no, $trade_no)
    {
        $request = new \AlipayTradeCloseRequest();
        $request->setBizContent(self::setBizContent(compact('out_trade_no', 'trade_no')));
        return self::AopclientRequestExecute($request);
    }

    /**
     * 写入日志
     * @param $content string | array | object
     * @return boolen
     * */
    public static function WriteLog($content)
    {
        try {
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH . 'alipay/'
            ]);
            if (is_array($content)) $content = 'response: ' . var_export($content, true);
            if (is_object($content)) $content = 'response: ' . var_export($content, true);
            Log::write(date('Y-m-d H:i:s', time()) . '   ' . $content);
        } catch (\Exception $e) {
        }
    }

}