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


use think\exception\ValidateException;
use app\admin\model\system\SmsAccessToken;

class AccessTokenServeService extends HttpService
{
    /**
     * 配置
     * @var string
     */
    protected $account;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $cacheTokenPrefix = "_crmeb_plat";

    /**
     * @var string
     */
    protected $apiHost = 'http://sms.crmeb.net/api/';

    const USER_LOGIN = "user/login";

    /**
     * AccessTokenServeService constructor.
     * @param string $account
     * @param string $secret
     */
    public function __construct($account,$secret)
    {
        $this->account = $account;
        $this->secret = $secret;
    }

    /**
     * 获取缓存token
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken()
    {
        $accessTokenKey = md5($this->account . '_' . $this->secret . $this->cacheTokenPrefix);
        $cacheToken = CacheService::get($accessTokenKey,'');
        if (!$cacheToken) {
            $getToken = $this->getTokenFromServer();
            if (!is_array($getToken)) {
                return false;
            }
            CacheService::set($accessTokenKey, $getToken['access_token'], $getToken['expires_in'] - time() - 300);
            $cacheToken = $getToken['access_token'];
        }
        $this->accessToken = $cacheToken;
        return $cacheToken;

    }

    /**
     * 销毁token
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function destroyToken()
    {
        $accessTokenKey = md5($this->account . '_' . $this->secret . $this->cacheTokenPrefix);
        return CacheService::rm($accessTokenKey);
    }

    /**
     * 从服务器获取token
     * @return mixed
     */
    public function getTokenFromServer()
    {
        $params = [
            'account' => $this->account,
            'secret' => md5($this->account . md5($this->secret)),
        ];
        $response = $this->postRequest($this->get(self::USER_LOGIN), $params);
        $response = json_decode($response, true);
        if (!$response) {
            throw new ValidateException('获取token失败');
        }
        if ($response['status'] === 200) {
            return $response['data'];
        } else {
            return $response['msg'];
        }
    }

    /**
     * 请求
     * @param string $url
     * @param array $data
     * @param string $method
     * @param bool $isHeader
     * @return array|mixed
     */
    public function httpRequest($url,$data = [], $method = 'POST',$isHeader = true)
    {
        $header = [];
        if ($isHeader) {
            $this->getToken();
            if (!$this->accessToken) {
                throw new ValidateException('配置已更改或token已失效');
            }
            $header = ['Authorization:Bearer-' . $this->accessToken];
        }
        try {
            $res = $this->request($this->get($url), $method, $data, $header);
            if (!$res) {
                exception('发生异常，请稍后重试');
            }
            $result = json_decode($res, true) ?: false;
            return $result;
//            if (!isset($result['status']) || $result['status'] != 200) {
//                return  $result['msg'] ? $result['msg'] : '发生异常，请稍后重试';
//            }
//            return $result['data'] ? $result['data'] : [];
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param string $apiUrl
     * @return string
     */
    public function get($apiUrl = '')
    {
        return $this->apiHost . $apiUrl;
    }
}
