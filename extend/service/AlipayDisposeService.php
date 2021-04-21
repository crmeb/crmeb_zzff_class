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

use think\Log;
use think\Request;
use think\Url;
use behavior\wechat\PaymentBehavior;
use service\HookService;
use service\SystemConfigService;
use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\RefererConfig;
use OSS\Model\CorsConfig;
use OSS\Model\CorsRule;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class AlipayDisposeService
{

    protected static $AccessKeyId=''; //阿里云AccessKeyId

    protected static $accessKeySecret=''; //阿里云AccessKeySecret

    /**
     * 初始化
     */
    final static function init()
    {
        self::$AccessKeyId=SystemConfigService::get('accessKeyId');//阿里云AccessKeyId
        self::$accessKeySecret=SystemConfigService::get('accessKeySecret');//阿里云AccessKeySecret
        if(self::$AccessKeyId=='' || self::$accessKeySecret=='')return exception('阿里云AccessKeyId或阿里云AccessKeySecret没有配置');
    }

    //对象存储OSS

    /**
     * 创建存储空间
     * @param string $endpoint
     * @param string $bucket
     * @param int $jurisdiction 1：私有 2：公共读 3：公共读写
     * @param int $type 1：标准储存 2：低频访问储存 3：归档储存
     */
    public static function ossDispose($endpoint='',$bucket='',$jurisdiction= 1,$type=1)
    {
        self::init();
        try {
            $ossClient = new OssClient(self::$AccessKeyId, self::$accessKeySecret, $endpoint);
            //检测存储空间是否存在
            $res = $ossClient->doesBucketExist($bucket);
            if($res) return false;
            switch ($type){
                case 1:
                    $storage= OssClient::OSS_STORAGE_STANDARD;
                    break;
                case 2:
                    $storage= OssClient::OSS_STORAGE_IA;
                    break;
                case 3:
                    $storage= OssClient::OSS_STORAGE_ARCHIVE;
                    break;
            }
            // 设置存储空间的存储类型为低频访问类型，默认是标准类型。
            $options = array(
                OssClient::OSS_STORAGE => $storage
            );
            switch ($jurisdiction){
                case 1:
                    $jurisdictions= OssClient::OSS_ACL_TYPE_PRIVATE;
                    break;
                case 2:
                    $jurisdictions= OssClient::OSS_ACL_TYPE_PUBLIC_READ;
                    break;
                case 3:
                    $jurisdictions= OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE;
                    break;
            }
            // 设置存储空间的权限为公共读，默认是私有读写。
            $res=$ossClient->createBucket($bucket, $jurisdictions, $options);
        } catch (OssException $e) {
            return $e->getMessage();
        }
        return $res;
    }
    /**
     * 列举存储空间
     * @param string $endpoint
     */
    public static function ossBucketList($endpoint)
    {
        self::init();
        try{
            $ossClient = new OssClient(self::$AccessKeyId, self::$accessKeySecret, $endpoint);
            $bucketListInfo = $ossClient->listBuckets();
        } catch(OssException $e) {
            return $e->getMessage();
        }
        $bucketList = $bucketListInfo->getBucketList();
        return $bucketList;
    }
    /**
     * 删除存储空间
     * @param string $endpoint
     * @param string $bucket
     */
    public static function deleteBucket($endpoint='',$bucket='')
    {
        self::init();
        try {
            $ossClient = new OssClient(self::$AccessKeyId, self::$accessKeySecret, $endpoint);
            $res = $ossClient->deleteBucket($bucket);
        } catch (OssException $e) {
            return $e->getMessage();
        }
        return $res;
    }
    /**
     *判断存储空间是否存在
     * @param string $endpoint
     * @param string $bucket
     */
    public static function doesBucketExist($endpoint='',$bucket='')
    {
        self::init();
        try {
            $ossClient = new OssClient(self::$AccessKeyId, self::$accessKeySecret, $endpoint);
            $res = $ossClient->doesBucketExist($bucket);
        } catch (OssException $e) {
            return $e->getMessage();
        }
        return $res;
    }
    /**
     * 设置防盗链
     * @param string $endpoint
     * @param string $bucket
     */
    public static function bucketReferer($endpoint='',$bucket='')
    {
        self::init();
        $refererConfig = new RefererConfig();
        // 设置允许空Referer。
        $refererConfig->setAllowEmptyReferer(true);
        // 添加Referer白名单。Referer参数支持通配符星号（*）和问号（？）。
        //$refererConfig->addReferer("www.aliiyun.com");
        //$refererConfig->addReferer("www.aliiyuncs.com");
        try{
            $ossClient = new OssClient(self::$AccessKeyId, self::$accessKeySecret, $endpoint);
            $res=$ossClient->putBucketReferer($bucket, $refererConfig);
        } catch(OssException $e) {
            return $e->getMessage();
        }
        return $res;
    }

    /**跨域规则设置
     * @throws OssException
     * @param string $endpoint
     * @param string $bucket
     */
    public static function putBucketCors($endpoint='',$bucket='')
    {
        $corsConfig = new CorsConfig();
        $rule = new CorsRule();
        // AllowedHeaders和ExposeHeaders不支持通配符。
        $rule->addAllowedHeader("*");
        // AllowedOlowedMethods最多支持一个星号（*）通配符。星号（*）表示允许所有的域来源或者操作。
        $rule->addAllowedOrigin("*");
        $rule->addAllowedMethod("GET");
        $rule->addAllowedMethod("POST");
        $rule->addAllowedMethod("PUT");
        $rule->addAllowedMethod("DELETE");
        $rule->addAllowedMethod("HEAD");
        $rule->setMaxAgeSeconds(600);
        // 每个存储空间最多允许10条规则。
        $corsConfig->addRule($rule);
        self::init();
        try{
            $ossClient = new OssClient(self::$AccessKeyId, self::$accessKeySecret, $endpoint);
            // 已存在的规则将被覆盖。
            $res=$ossClient->putBucketCors($bucket, $corsConfig);
        } catch(OssException $e) {
            return $e->getMessage();
        }
        return $res;
    }
    /**获取跨域规则
     * @throws OssException
     * @param string $endpoint
     * @param string $bucket
     */
    public static function getCrossDomainRules($endpoint='',$bucket='')
    {
        self::init();
        $corsConfig = null;
        try{
            $ossClient = new OssClient(self::$AccessKeyId, self::$accessKeySecret, $endpoint);

            $corsConfig = $ossClient->getBucketCors($bucket);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
        print($corsConfig->serializeToXml() . "\n");
    }
    /**
     * 获取存储空间的地域
     * @param string $endpoint
     * @param string $bucket
     */
    public static function ossRegion($endpoint='',$bucket=''){
        self::init();
        try {
            $ossClient = new OssClient(self::$AccessKeyId, self::$accessKeySecret, $endpoint);
            $Regions = $ossClient->getBucketLocation($bucket);
        } catch (OssException $e) {
            return $e->getMessage();
        }
        return $Regions;
    }

    /**
     * 获取存储空间元信息
     * @param string $endpoint
     * @param string $bucket
     */
    public static function ossMetas($endpoint='',$bucket=''){
        self::init();
        try {
            $ossClient = new OssClient(self::$AccessKeyId, self::$accessKeySecret, $endpoint);
            $Metas = $ossClient->getBucketMeta($bucket);
        } catch (OssException $e) {
            return $e->getMessage();
        }
        return $Metas['oss-requestheaders'];
    }

    /**获取文件元信息
     * @param string $endpoint
     * @param string $bucket
     * @param string $ObjectName
     */
    public static function getFileMetaInformation($endpoint='',$bucket='',$ObjectName='')
    {
        self::init();
        try {
            $ossClient = new OssClient(self::$AccessKeyId, self::$accessKeySecret, $endpoint);
            // 获取文件的全部元信息。
            $objectMeta = $ossClient->getObjectMeta($bucket, $ObjectName);
            return $objectMeta;
        } catch (OssException $e) {
            return $e->getMessage();
        }
    }

    //视频直播配置

    /**
     * 添加直播域名
     * @param string $domainName
     * @param string $type
     */
    public static function addLiveDomain($data)
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($data['region'])
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('AddLiveDomain')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $data['region'],
                        'LiveDomainType' => $data['live_domain_type'],
                        'DomainName' => $data['domain_name'],
                        'Region' => $data['region'],
                        'Scope' => $data['scope'],
                    ],
                ])
                ->request();
           return  $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**删除已添加的直播域名
     * @param $domainName
     */
    public static function deleteLiveDomains($domainName='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('DeleteLiveDomain')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'DomainName' => $domainName,
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**查询直播域名配置信息
     * @param string $domainName
     * @param string $regionId
     */
    public static function describeLiveDomainDetails($domainName='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('DescribeLiveDomainDetail')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'DomainName' => $domainName,
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**域名停用
     * @param string $domainName
     * @param string $regionId
     */
    public static function stopLiveDomains($domainName='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('StopLiveDomain')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'DomainName'=>$domainName,
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**域名启用
     * @param string $domainName
     * @param string $regionId
     */
    public static function startLiveDomains($domainName='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('StartLiveDomain')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'DomainName'=>$domainName,
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**添加直播域名播流域名和推流域名的映射关系配置
     * @param string $PullDomain 播流域名
     * @param string $PushDomain 推流域名
     * @param string $regionId
     */
    public static function addLiveDomainMappings($PullDomain='',$PushDomain='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('AddLiveDomainMapping')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'PullDomain'=>$PullDomain,
                        'PushDomain'=>$PushDomain,
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }
    /**删除直播域名播流域名和推流域名的映射关系配置
     * @param string $PullDomain 播流域名
     * @param string $PushDomain 推流域名
     * @param string $regionId
     */
    public static function deleteLiveDomainMappings($PullDomain='',$PushDomain='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('DeleteLiveDomainMapping')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'PullDomain'=>$PullDomain,
                        'PushDomain'=>$PushDomain,
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**设置推流回调配置
     * @param string $domainName
     * @param string $regionId
     * @param string $NotifyUrl
     */
    public static function setLiveStreamsNotifyUrlConfigs($domainName='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        $site_url=SystemConfigService::get('site_url');
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('SetLiveStreamsNotifyUrlConfig')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'DomainName'=>$domainName,
                        'NotifyUrl'=>$site_url.'/live/index/serve',
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }
    /**查看推流回调配置
     * @param string $domainName
     * @param string $regionId
     * @param string $NotifyUrl
     */
    public static function describeLiveStreamsNotifyUrlConfig($domainName='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('DescribeLiveStreamsNotifyUrlConfig')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'DomainName'=>$domainName,
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }
    /**删除推流回调配置
     * @param string $domainName
     * @param string $regionId
     */
    public static function deleteLiveStreamsNotifyUrlConfigs($domainName='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('DeleteLiveStreamsNotifyUrlConfig')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'DomainName'=>$domainName,
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**调用AddLiveAppRecordConfig配置APP录制，输出内容保存到OSS中
     * @param string $domainName
     * @param string $regionId
     * @param string $StreamName
     * @param string $AppName
     * @param string $OssBucket
     * @param string $OssEndpoint
     * @param string $format 视频格式
     * @param string $duration 录制时长
     */
    public static function addLiveAppRecordConfigs($domainName='',$regionId='',$AppName='',$StreamName='',$OssBucket='',$OssEndpoint='',$format='',$duration='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        if($format=='m3u8'){
            $query=[
                'RegionId' => $regionId,
                'StreamName' => $StreamName,
                'AppName' => $AppName,
                'DomainName' => $domainName,
                'OssBucket' => $OssBucket,
                'OssEndpoint' => $OssEndpoint,
                'RecordFormat.1.Format' => "m3u8",
                'RecordFormat.1.OssObjectPrefix' => "record/{AppName}/{StreamName}/{EscapedStartTime}_{EscapedEndTime}",
                'RecordFormat.1.SliceOssObjectPrefix' => "record/{AppName}/{StreamName}/{UnixTimestamp}_{Sequence}",
                'RecordFormat.1.CycleDuration' => bcmul($duration,60,0),
            ];
        }else if($format=='mp4'){
            $query=[
                'RegionId' => $regionId,
                'StreamName' => $StreamName,
                'AppName' => $AppName,
                'DomainName' => $domainName,
                'OssBucket' => $OssBucket,
                'OssEndpoint' => $OssEndpoint,
                'RecordFormat.1.Format' => "mp4",
                'RecordFormat.1.OssObjectPrefix' => "record/{AppName}/{StreamName}/{EscapedStartTime}_{EscapedEndTime}",
                'RecordFormat.1.CycleDuration' => bcmul($duration,60,0),
            ];
        }else{
            $query=[
                'RegionId' => $regionId,
                'StreamName' => $StreamName,
                'AppName' => $AppName,
                'DomainName' => $domainName,
                'OssBucket' => $OssBucket,
                'OssEndpoint' => $OssEndpoint,
                'RecordFormat.1.Format' => "flv",
                'RecordFormat.1.OssObjectPrefix' => "record/{AppName}/{StreamName}/{EscapedStartTime}_{EscapedEndTime}",
                'RecordFormat.1.CycleDuration' => bcmul($duration,60,0),
            ];
        }
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('AddLiveAppRecordConfig')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => $query,
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**解除录制配置
     * @param string $domainName
     * @param string $regionId
     * @param string $AppName
     * @param string $StreamName
     */
    public static function deleteLiveAppRecordConfigs($domainName='',$regionId='',$AppName='',$StreamName='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('DeleteLiveAppRecordConfig')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'AppName' => $AppName,
                        'DomainName' => $domainName,
                        'StreamName' => $StreamName,
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**配置域名
     * @param string $domainName
     * @param string $regionId
     */
    public static function batchSetLiveDomainConfigs($domainName='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        $site_url=SystemConfigService::get('site_url');
        $url=parse_url($site_url)['host'];
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('BatchSetLiveDomainConfigs')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'DomainNames' => $domainName,
                        'Functions' => "[{'functionArgs':[{'argName':'key','argValue':'Access-Control-Allow-Origin'},{'argName':'value','argValue':'*'}],'functionName':'set_resp_header'},
                        {'functionArgs':[{'argName':'refer_domain_allow_list','argValue':'$url'},{'argName':'allow_empty','argValue':'on'}],'functionName':'referer_white_list_set'}
                        ]",
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**查询直播域名配置key
     * @param string $domainName
     * @param string $regionId
     */
    public static function describeLiveDomainConfigs($domainName='',$regionId='')
    {
        self::init();
        AlibabaCloud::accessKeyClient(self::$AccessKeyId, self::$accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('live')
                ->version('2016-11-01')
                ->action('DescribeLiveDomainConfigs')
                ->method('POST')
                ->host('live.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $regionId,
                        'DomainName' => $domainName,
                        'FunctionNames'=>'aliauth'
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }
}