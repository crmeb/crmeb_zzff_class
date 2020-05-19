<?php

namespace Api;

use think\Config;
use OSS\Model\RefererConfig;

/**
 * Class AliyunOss
 * @package Api
 */
class AliyunOss extends AliyunSdk
{

    /**
     * OSS存储桶名
     * @var string
     */
    protected $OssBucket;

    /**
     * OSS 地域节点
     * @var string
     */
    protected $OssEndpoint;

    /**
     * 外网访问地址
     * @var string
     */
    protected $uploadUrl;

    /**
     * 上传验证规则
     * @var string
     */
    protected $autoValidate;

    /**
     * 是否开启防盗链
     * @var array
     */
    protected $referer;

    /**
     * 初始化参数
     */
    protected function _initialize()
    {
        $this->OssEndpoint = isset($this->config['OssEndpoint']) ? $this->config['OssEndpoint'] : null;
        $this->OssBucket = isset($this->config['OssBucket']) ? $this->config['OssBucket'] : null;
        $this->uploadUrl = isset($this->config['uploadUrl']) ? $this->config['uploadUrl'] : null;
        $this->checkUploadUrl();
        $this->referer = isset($this->config['referer']) && is_array($this->config['referer']) ? $this->config['referer'] : [];
    }

    /**
     * 验证合法上传域名
     */
    protected function checkUploadUrl()
    {
        if ($this->uploadUrl) {
            if (strstr($this->uploadUrl, 'http') === false) {
                $this->uploadUrl = 'http://' . $this->uploadUrl;
            }
        }
    }

    /**
     * 初始化
     * @return null|\OSS\OssClient
     * @throws \OSS\Core\OssException
     */
    public function init()
    {
        if ($this->client === null) {
            $this->client = new \OSS\OssClient($this->AccessKey, $this->AccessKeySecret, $this->OssEndpoint);
            if (!$this->client->doesBucketExist($this->OssBucket)) {
                $this->client->createBucket($this->OssBucket, \OSS\OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
            }
            if ($this->referer) {
                $refererConfig = new RefererConfig();
                // 设置允许空Referer。
                $refererConfig->setAllowEmptyReferer(true);
                foreach ($this->referer as $url) {
                    $refererConfig->addReferer($url);
                }
                $this->client->putBucketReferer($this->OssBucket, $refererConfig);
            }
        }
        return $this->client;
    }

    /**
     * 设置防盗链
     * @param array $referer
     * @return $this
     */
    public function setReferer(array $referer = [])
    {
        $this->referer = $referer;
        return $this;
    }

    /**
     * 验证规则
     * @param array $autoValidate
     * @return $this
     */
    public function validate(array $autoValidate = [])
    {
        if (!$autoValidate) {
            $autoValidate = Config::get('upload.Validate');
        }
        $this->autoValidate = $autoValidate;
        return $this;
    }

    /**
     * 设置OSS存储桶名
     * @param string $OssBucket
     * @return $this
     * */
    public function setOssBucketAttr($OssBucket)
    {
        $this->OssBucket = $OssBucket;
        return $this;
    }

    /**
     * 设置OSS存储外网访问域名
     * @param string $OssEndpoint
     * @return $this
     * */
    public function setOssEndpointAttr($OssEndpoint)
    {
        $this->OssEndpoint = $OssEndpoint;
        return $this;
    }

    /**
     * 提取文件名
     * @param string $path
     * @param string $ext
     * @return string
     */
    protected function saveFileName($path = null, $ext = 'jpg')
    {
        return ($path ? substr(md5($path), 0, 5) : '') . date('YmdHis') . rand(0, 9999) . '.' . $ext;
    }

    /**
     * 获取文件后缀
     * @param \think\File $file
     * @return string
     */
    protected function getExtension(\think\File $file)
    {
        $pathinfo = pathinfo($file->getInfo('name'));
        return isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
    }

    /**
     * 上传图片
     * @param $fileName
     * @return bool
     */
    public function upload($fileName)
    {
        $fileHandle = request()->file($fileName);
        $key = $this->saveFileName($fileHandle->getRealPath(), $this->getExtension($fileHandle));
        try {
            if ($this->autoValidate) {
                $fileHandle->validate($this->autoValidate);
                $this->autoValidate = null;
            }
            $uploadInfo = $this->init()->uploadFile($this->OssBucket, $key, $fileHandle->getRealPath());
            if (!isset($uploadInfo['info']['url'])) {
                return self::setErrorInfo('Upload failure');
            }
            return [
                'url' => $uploadInfo['info']['url'],
                'key' => $key
            ];
        } catch (\Throwable $e) {
            return self::setErrorInfo($e);
        }
    }

    /**
     * 文件流上传
     * @param string $fileContent
     * @param string|null $key
     * @return bool|mixed
     */
    public function stream(string $fileContent, string $key = null)
    {
        try {
            if (!$key) {
                $key = $this->saveFileName();
            }
            $uploadInfo = $this->init()->putObject($this->OssBucket, $key, $fileContent);
            if (!isset($uploadInfo['info']['url'])) {
                return self::setErrorInfo('Upload failure');
            }
            return [
                'url' => $uploadInfo['info']['url'],
                'key' => $key
            ];
        } catch (Throwable $e) {
            return self::setErrorInfo($e);
        }
    }

    /**
     * 删除指定资源
     * @param 资源key
     * @return array
     * */
    public function delOssFile($key)
    {
        try {
            return $this->init()->deleteObject($this->OssBucket, $key);
        } catch (\Exception $e) {
            return self::setErrorInfo($e);
        }
    }

    /**
     * 获取签名
     * @param string $callbackUrl
     * @param string $dir
     * @return string
     */
    public function getSignature($callbackUrl = '', $dir = '')
    {

        $base64CallbackBody = base64_encode(json_encode([
            'callbackUrl' => $callbackUrl,
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded"
        ]));

        $policy = json_encode([
            'expiration' => $this->gmtIso8601(time() + 30),
            'conditions' =>
                [
                    [0 => 'content-length-range', 1 => 0, 2 => 1048576000],
                    [0 => 'starts-with', 1 => '$key', 2 => $dir]
                ]
        ]);
        $base64Policy = base64_encode($policy);
        $signature = base64_encode(hash_hmac('sha1', $base64Policy, $this->AccessKeySecret, true));
        return [
            'accessid' => $this->AccessKey,
            'host' => $this->uploadUrl,
            'policy' => $base64Policy,
            'signature' => $signature,
            'expire' => time() + 30,
            'callback' => $base64CallbackBody
        ];
    }

    /**
     * 获取ISO时间格式
     * @param $time
     * @return string
     */
    protected function gmtIso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    /**
     * 获取防盗链信息
     * @param string $bucket
     * @return RefererConfig
     * @throws \OSS\Core\OssException
     */
    public function getBucketReferer($bucket = '')
    {
        return $this->init()->getBucketReferer($bucket ? $bucket : $this->OssBucket);
    }

    /**
     * 清除防盗链
     * @param string $bucket
     * @return \OSS\Http\ResponseCore
     * @throws \OSS\Core\OssException
     */
    public function deleteBucketReferer($bucket = '')
    {
        $refererConfig = new RefererConfig();
        return $this->init()->putBucketReferer($bucket ? $bucket : $this->OssBucket, $refererConfig);
    }

}