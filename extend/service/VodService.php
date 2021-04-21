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

use think\Request;
use service\JsonService;

class VodService
{

    protected static $method='GET'; //获取方式

    protected static $AccessKeyId=''; //阿里云AccessKeyId

    protected static $accessKeySecret=''; //阿里云AccessKeySecret


    final static function init()
    {
        self::$AccessKeyId=SystemConfigService::get('accessKeyId');//阿里云AccessKeyId
        self::$accessKeySecret=SystemConfigService::get('accessKeySecret');//阿里云AccessKeySecret
    }
    /**获取视频上传地址和凭证
     * @param string $videoId
     * @param string $FileName
     * @param string $type 1=获取视频上传凭证 2=获取视频播放凭证 3=获取视频播放地址 4=删除完整视频
     */
    public static function videoUploadAddressVoucher($FileName='',$type=1,$videoId='',$image='')
    {
        self::init();
        $apiParams=[];
        $site_url=SystemConfigService::get('site_url');
        if($site_url){
            $arr = parse_url($site_url);
            if($arr['scheme']){
                $scheme=$arr['scheme'];
            }else{
                $scheme='http';
            }
        }else{
            $scheme='http';
        }
        if($scheme=='https'){
            $requestUrl="https://vod.cn-shanghai.aliyuncs.com/?";
        }else{
            $requestUrl="http://vod.cn-shanghai.aliyuncs.com/?";
        }
        if($videoId!='' && $type==1){
            $apiParams['Action']         ='RefreshUploadVideo';
            $apiParams['VideoId']        = $videoId;
        }else if($videoId!='' && $type==2){
            $apiParams['Action']         ='GetVideoPlayAuth';
            $apiParams['VideoId']        = $videoId;
        }else if($videoId!='' && $type==3){
            $apiParams['Action']         ='GetPlayInfo';
            $apiParams['VideoId']        = $videoId;
        }else if($videoId!='' && $type==4){
            $apiParams['Action']         ='DeleteVideo';
            $apiParams['VideoIds']        = $videoId;
        }else if($videoId=='' && $type==1){
            $apiParams['Action']         ='CreateUploadVideo';
            $apiParams['Title']          = self::video_name($FileName);
            $apiParams['FileName']       = $FileName;
            $apiParams['CoverURL']       = $image;
        }
        $apiParams['AccessKeyId']        = self::$AccessKeyId;
        $apiParams['Format']             = 'JSON';
        $apiParams['SignatureMethod']    = 'HMAC-SHA1';
        $apiParams['SignatureVersion']   = '1.0';
        $apiParams['SignatureNonce']     = md5(uniqid(mt_rand(), true));
        $apiParams['Timestamp']          = gmdate('Y-m-d\TH:i:s\Z');
        $apiParams['Version']            = '2017-03-21';
        $apiParams['Signature']=self::computeSignature($apiParams,self::$accessKeySecret);
        foreach ($apiParams as $apiParamKey => $apiParamValue) {
            $requestUrl .= "$apiParamKey=" . urlencode($apiParamValue) . '&';
        }
        return substr($requestUrl, 0, -1);
    }
    public static function signString($source, $accessSecret)
    {
        return base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
    }
    /**
     * 视频名称
     */
    public static function video_name($FileName)
    {
        return mb_substr(substr($FileName, strrpos($FileName, '.')+1),0,128,'utf8');
    }
    /**签名机制
     * @param $parameters
     * @param $accessKeySecret
     * @return mixed
     */
    public static function computeSignature($parameters, $accessKeySecret)
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . self::percentEncode($key) . '=' . self::percentEncode($value);
        }
        $stringToBeSigned =
            self::$method.'&%2F&' . self::percentEncode(substr($canonicalizedQueryString, 1));
        return self::signString($stringToBeSigned, $accessKeySecret . '&');
    }
    /**
     * @param $str
     * @return string|string[]|null
     */
    public static function percentEncode($str)
    {
        $res = urlencode($str);
        $res = str_replace(array('+', '*'), array('%20', '%2A'), $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
}
