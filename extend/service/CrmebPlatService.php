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
namespace service;


class CrmebPlatService
{
    /**
     * 验证码
     */
    const PLAT_CODE = 'user/code';
    /**
     * 平台注册
     */
    const PLAT_OPEN = 'user/register';
    /**
     * 用户信息
     */
    const PLAT_USER_INFO = 'user/info';
    /**
     * 修改密码
     */
    const PLAT_USER_MODIFY = 'user/modify';
    /**
     * 找回秘钥
     */
    const PLAT_USER_FORGET = 'user/forget';
    /**
     * 消费记录
     */
    const PLAT_BILL = 'user/bill';
    /**
     * 用量记录
     */
    const PlAT_RRCORD = 'user/record';
    /**
     * 套餐列表
     */
    const MEAL_LIST = 'meal/list';
    /**
     * 支付二维码
     */
    const MEAL_CODE = 'meal/code';
    /**
     * 账号
     * @var null
     */
    protected $account = NULL;
    /**
     * 秘钥
     * @var null
     */
    protected $sercet = NULL;
    /**
     * access_token
     * @var null
     */
    protected $accessToken = NULL;


    public function __construct($account = '', $sercet = '')
    {
        $this->accessToken = $this->getAccessToken($account, $sercet);
    }

    protected function getAccessToken($account, $sercet)
    {
        $this->account = $account ? $account : SystemConfigService::get('sms_account');
        $this->sercet = $sercet ? $sercet : SystemConfigService::get('sms_token');
        return new AccessTokenServeService($this->account, $this->sercet);
    }

    /**
     * 获取验证码
     * @param $phone
     * @return bool|mixed
     */
    public function code($phone)
    {
        $param = [
            'phone' => $phone
        ];
        return $this->accessToken->httpRequest(self::PLAT_CODE, $param, 'POST', false);
    }

    /**
     * 注册
     * @param $account
     * @param $phone
     * @param $password
     * @param $verify_code
     * @return bool
     */
    public function register($account, $phone, $password, $verify_code)
    {
        $param = [
            'account' => $account,
            'phone' => $phone,
            'password' => md5($password),
            'verify_code' => $verify_code
        ];
        $result = $this->accessToken->httpRequest(self::PLAT_OPEN, $param, 'POST', false);
        return $result;
    }

    /**
     * 登录
     * @param $account
     * @param $secret
     * @return mixed
     */
    public function login($account, $secret)
    {
        $token = $this->getAccessToken($account, $secret)->getToken();
        return $token;
    }

    /**
     * 退出登录
     * @param $account
     * @param $secret
     * @return mixed
     */
    public function loginOut()
    {
        return $this->accessToken->destroyToken();
    }

    /**
     * 用户详情
     * @return mixed
     */
    public function info()
    {
        $result = $this->accessToken->httpRequest(self::PLAT_USER_INFO);
        return $result;
    }

    /**
     * 修改秘钥
     * @param $account
     * @param $phone
     * @param $password
     * @param $verify_code
     * @return bool
     */
    public function modify($account, $phone, $password, $verify_code)
    {
        $param = [
            'account' => $account,
            'phone' => $phone,
            'password' => md5($password),
            'verify_code' => $verify_code
        ];
        return $this->accessToken->httpRequest(self::PLAT_USER_MODIFY, $param, 'POST', false);
    }

    /**
     * 找回秘钥
     * @param $phone
     * @param $verify_code
     * @return mixed
     */
    public function forget($phone, $verify_code)
    {
        $param = [
            'phone' => $phone,
            'verify_code' => $verify_code
        ];
        $result = $this->accessToken->httpRequest(self::PLAT_USER_FORGET, $param, 'POST', false);
        return $result;
    }

    /**
     * 获取消费记录
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function bill($page = 0, $limit = 10)
    {
        $param = [
            'page' => $page,
            'limit' => $limit
        ];
        $result = $this->accessToken->httpRequest(self::PLAT_BILL, $param);
        return $result;
    }

    /**
     * 获取用量记录
     * @param string $type
     * @param int $page
     * @param int $limit
     * @return array|mixed
     */
    public function record($type = 'sms', $page = 0, $limit = 10)
    {
        $param = [
            'type' => $type,
            'page' => $page,
            'limit' => $limit
        ];
        $result = $this->accessToken->httpRequest(self::PlAT_RRCORD, $param);
        return $result;
    }

    /**
     * 套餐列表
     * @param string $type
     * @return array|bool|mixed
     */
    public function meal($type = 'sms')
    {
        $param = [
            'type' => $type
        ];
        return $this->accessToken->httpRequest(self::MEAL_LIST, $param);
    }

    /**
     * 获取支付二维码
     * @param $type
     * @param $meal_id
     * @param $price
     * @param $num
     * @param string $pay_type
     * @return array|mixed
     */
    public function pay($type, $meal_id, $price, $num, $pay_type = 'weixin')
    {
        $param = [
            'type' => $type,
            'meal_id' => $meal_id,
            'price' => $price,
            'num' => $num,
            'pay_type' => $pay_type
        ];
        return $this->accessToken->httpRequest(self::MEAL_CODE, $param);
    }
}
