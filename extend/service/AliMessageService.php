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

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

/**
 * 阿里云短信
 * Class AliMessageService
 * @package service
 */
class AliMessageService
{
    /**
     * 初始化
     * @throws ClientException
     */
    public static function init()
    {
        AlibabaCloud::accessKeyClient(SystemConfigService::get('accessKeyId'), SystemConfigService::get('accessKeySecret'))
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
    }

    /**
     * 发送短信
     * @param string $tel 短信接收号码
     * @param string $setSignName 短信签名
     * @param string $setTemplateCode 短信模板ID
     * @param array $setTemplateParam 短信内容
     * @param string $setOutId 外部流水扩展字段
     */
    public static function sendmsg($tel = '', $setTemplateParam = [], $setOutId = '')
    {
        try {
            self::init();
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $tel,
                        'SignName' => SystemConfigService::get('smsSignName'),
                        'TemplateCode' => SystemConfigService::get('smsTemplateCode'),
                        'TemplateParam' => json_encode(is_array($setTemplateParam) ? $setTemplateParam : ['code' => $setTemplateParam]),
                    ],
                ])->request()->toArray();
            return $result;
        } catch (ClientException $e) {
            return false;
        } catch (ServerException $e) {
            return false;
        }
    }

    /**
     * 生成随机验证码
     * @return int
     */
    public static function getVerificationCode($length = 6)
    {
        $str = '123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $str[mt_rand(0, strlen($str) - 1)];
        }
        return $code;
    }

}