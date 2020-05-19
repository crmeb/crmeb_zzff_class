<?php

namespace Api;

class AliyunLive extends AliyunSdk
{

    /**
     * 推流地址
     * @var string
     */
    protected $rtmpLink;

    /**
     * 播放地址
     * @var string
     */
    protected $playLike;

    /**
     * 推流Key
     * @var string
     */
    protected $key;

    /**
     * 播放key
     * @var string
     */
    protected $payKey;

    /**
     * 应用名
     * @var string
     */
    protected $appName;

    /**
     * OSS存储桶名
     * @var string
     */
    protected $OssBucket;

    /**
     * OSS存储外网访问域名
     * @var string
     */
    protected $OssEndpoint;

    /**
     * 自动加载阿里云类
     * @var array
     */
    protected $autoLoadPath = ['live'];

    /**
     * 实例化阿里云接口
     * */
    protected function _initialize()
    {
        $this->OssEndpoint = isset($this->config['OssEndpoint']) ? $this->config['OssEndpoint'] : null;
        $this->appName = isset($this->config['appName']) ? $this->config['appName'] : null;
        $this->OssBucket = isset($this->config['OssBucket']) ? $this->config['OssBucket'] : null;
        $this->payKey = isset($this->config['payKey']) ? $this->config['payKey'] : null;
        $this->key = isset($this->config['key']) ? $this->config['key'] : null;
        $this->playLike = isset($this->config['playLike']) ? $this->config['playLike'] : null;
        $this->rtmpLink = isset($this->config['rtmpLink']) ? $this->config['rtmpLink'] : null;
        $this->client = $this->client === null ?
            new \DefaultAcsClient(\DefaultProfile::getProfile("cn-hangzhou", $this->AccessKey, $this->AccessKeySecret)) :
            $this->client;
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
     * 设置播放域名
     * @param string $PlayLike
     * @return $this
     * */
    public function setPlayLike($PlayLike)
    {
        $this->playLike = $PlayLike;
        return $this;
    }


    /**
     * 设置推流域名
     * @param string $rtmpLink
     * @return $this
     * */
    public function setRtmpLink($rtmpLink)
    {
        $this->rtmpLink = $rtmpLink;
        return $this;
    }

    /**
     * 设置应用名
     * @param string $AppName
     * @return $this
     * */
    public function setAppName($AppName)
    {
        $this->appName = $AppName;
        return $this;
    }

    /**
     * 获取直播间推流url和播放url
     * @param string $appName 直播间号
     * @param string $streamName 直播间号
     * @param int $expire 过期时间
     * @return array
     * */
    public function foundLiveStudio($streamName, $expire = 1800)
    {
        $time = time() + $expire;
        $LiveName = '/' . $this->appName . '/' . $streamName;
        //推流地址
        $pushUrl = 'rtmp://' . $this->rtmpLink . $LiveName . '?auth_key=' . md5($LiveName . '-' . $time . '-0-0-' . $this->key);
        //rtmp 播放地址
        $rtmpUrl = 'rtmp://' . $this->playLike . $LiveName . '?auth_key=' . md5($LiveName . '-' . $time . '-0-0-' . $this->payKey);
        //flv 播放地址
        $flvUrl = 'http://' . $this->playLike . $LiveName . '.flv?auth_key=' . md5($LiveName . '.flv-' . $time . '-0-0-' . $this->payKey);
        //m3u8 播放地址
        $m3u8Url = 'http://' . $this->playLike . $LiveName . '.m3u8?auth_key=' . md5($LiveName . '.m3u8-' . $time . '-0-0-' . $this->payKey);
        return compact('pushUrl', 'rtmpUrl', 'flvUrl', 'm3u8Url');
    }

    /**
     * 生成推流地址
     * @param string $streamName 直播间号码
     * @param boolean $complete 加速域名
     * @param int $time 有效时间单位秒
     * @return array
     */
    public function getPushSteam($streamName, $complete = false, $time = 1800)
    {
        $time = time() + $time;
        $videohost = $this->rtmpLink;
        $appName = $this->appName;
        $privateKey = $this->key;
        if ($privateKey) {
            $auth_key = md5("/{$appName}/{$streamName}-{$time}-0-0-{$privateKey}");
            if ($complete) {
                $url = "rtmp://{$videohost}/{$appName}/{$streamName}?auth_key={$time}-0-0-{$auth_key}";
            } else {
                $url['href'] = "rtmp://{$videohost}/{$appName}/";
                $url['code'] = "{$streamName}?auth_key={$time}-0-0-{$auth_key}";
            }
        } else {
            $url = $complete ? ['href' => "rtmp://{$videohost}/{$appName}/", 'code' => $streamName] : "rtmp://{$videohost}/{$appName}/{$streamName}";
        }
        return $url;
    }

    /**
     * 生成拉流地址
     * @param $streamName 用户专有名
     * @param $vhost 加速域名
     * @param $type 视频格式 支持rtmp、flv、m3u8三种格式
     */
    public function getPullSteam($streamName, $vhost = '', $time = 1800, $type = 'm3u8')
    {
        $time = time() + $time;
        $vhost = $vhost ? $vhost : $this->playLike;
        $appName = $this->appName;
        $privateKey = $this->payKey;
        $url = '';
        switch ($type) {
            case 'rtmp':
                $host = 'rtmp://' . $vhost;
                $url = "/{$appName}/{$streamName}";
                break;
            case 'flv':
                $host = 'http://' . $vhost;
                $url = "/{$appName}/{$streamName}.flv";
                break;
            case 'm3u8':
                $host = 'http://' . $vhost;
                $url = "/{$appName}/{$streamName}.m3u8";
                break;
        }
        if ($privateKey) {
            $auth_key = md5($url . '-' . $time . '-0-0-' . $privateKey);
            $url = $host . $url . "?auth_key={$time}-0-0-{$auth_key}";
        } else {
            $url = $host . $url;
        }
        return $url;
    }

    /**
     *  实时在线人数
     * @param string $streamName 直播房间号
     * @param string $actionName 执行动作
     * @return $this
     * */
    public function onlineUserNum($streamName, $actionName = __FUNCTION__)
    {
        $this->action[] = $actionName;
        $request = new \live\Request\V20161101\DescribeLiveStreamOnlineUserNumRequest();
        $this->request[$actionName] = $request->setDomainName($this->playLike)->setAppName($this->appName)->setStreamName($streamName);
        return $this;
    }

    /**
     * 恢复某个直播间
     * @param string $streamName 用户id
     * @param string $actionName 动作名称 默认本方法名
     * @return array
     * */
    public function resumeLive($streamName, $actionName = __FUNCTION__)
    {
        $this->action[] = $actionName;
        $request = new \live\Request\V20161101\ResumeLiveStreamRequest();
        $this->request[$actionName] = $request->setAppName($this->appName)->setLiveStreamType('publisher')->setDomainName($this->playLike)->setStreamName($streamName);
        return $this;
    }

    /**
     * 禁掉某个直播间
     * @param string $streamName 用户id
     * @param string $resumeTime 警用时间 时间格式 2015-12-01T17:37:00Z
     * @param string $actionName 动作名称 默认本方法名
     * @return array
     * */
    public function forbid($streamName, $resumeTime, $actionName = __FUNCTION__)
    {
        $this->action[] = $actionName;
        $request = new \live\Request\V20161101\ForbidLiveStreamRequest();
        $this->request[$actionName] = $request->setAppName($this->appName)->setLiveStreamType('publisher')->setDomainName($this->playLike)->setStreamName($streamName)->setResumeTime($resumeTime);
        return $this;
    }

    /**
     * 设置回调地址
     * @param string $notifyUrl 回调地址
     * @param string $actionName 动作名称 默认本方法名
     * @return $this
     * */
    public function setLiveNotifyUrl($notifyUrl, $actionName = __FUNCTION__)
    {
        $this->action[] = $actionName;
        $request = new \live\Request\V20161101\SetLiveStreamsNotifyUrlConfigRequest();
        $this->request[$actionName] = $request->setDomainName($this->playLike)->setNotifyUrl($notifyUrl);
        return $this;
    }

    /**
     * 手动录制直播间
     * @param string $StreamName 直播间号
     * @param boolean $CommandType 暂停 或 开始
     * @param string $actionName 动作名称 默认本方法名
     * @return $this
     * */
    public function liveRecording($StreamName, $CommandType = true, $actionName = __FUNCTION__)
    {
        $this->action[] = $actionName;
        $Command = $CommandType ? "start" : "stop";
        $request = new \live\Request\V20161101\RealTimeRecordCommandRequest();
        $this->request[$actionName] = $request->setDomainName($this->playLike)->setAppName($this->appName)->setStreamName($StreamName)->setCommand($Command);
        return $this;
    }

    /**
     * 添加录制回调
     * @param $onDemandUrl
     * @param string $actionName
     * @return $this
     */
    public function addLiveRecordNotifyConfig($onDemandUrl, $actionName = __FUNCTION__)
    {
        $this->action[] = $actionName;
        $request = new \live\Request\V20161101\AddLiveRecordNotifyConfigRequest();
        $this->request[$actionName] = $request->setDomainName($this->playLike)->setNotifyUrl($onDemandUrl)->setOnDemandUrl($onDemandUrl);
        return $this;
    }

    /**
     * 直播间录制配置
     * @param string $OnDemand 按需录制 0表示关闭。1表示通过HTTP回调方式 7表示默认不录制
     * @param int $CycleDuration 15-360 分钟
     * @return $this
     * */
    public function liveRecordConfig($Duration = 2, $OnDemand = 7, $actionName = __FUNCTION__)
    {
        $this->action[] = $actionName;
        $CycleDuration = $Duration * 3600;
        $request = new \live\Request\V20161101\AddLiveAppRecordConfigRequest();
        $this->request[$actionName] = $request->setDomainName($this->playLike)->setAppName($this->appName)
            ->setOssBucket($this->OssBucket)->setOssEndpoint($this->OssEndpoint)->setOnDemand($OnDemand)
//            ->setStartTime(self::setTimeFormat())->setEndTime(self::setTimeFormat(time() + $CycleDuration))
            ->setRecordFormats([
                [
                    'Format' => 'mp4',
                    'CycleDuration' => $CycleDuration,
                    'OssObjectPrefix' => 'live/{AppName}/{StreamName}/{Sequence}{EscapedStartTime}{EscapedEndTime}',
                    'SliceOssObjectPrefix' => 'live/{AppName}/{StreamName}/{UnixTimestamp}_{Sequence}'
                ],
            ]);
        return $this;
    }

    /**
     * 解除录制配置
     * @param string $StreamName 直播间号
     * @param string $actionName 动作名称 默认本方法名
     * @return $this
     * */
    public function liveDelRecording(string $StreamName, $actionName = __FUNCTION__)
    {
        $this->action[] = $actionName;
        $request = new \live\Request\V20161101\DeleteLiveAppRecordConfigRequest();
        $request->setDomainName($this->playLike)->setAppName($this->appName);
        if ($StreamName) $request->setStreamName($StreamName);
        $this->request[$actionName] = $request;
        return $this;
    }

    /**
     * 创建直播索引文件
     * 开播后调用创建录制索引返回：
     * {
     * "RecordInfo":{
     * "AppName":"xxx",
     * "CreateTime":"2016-05-27T09:40:56Z",
     * "DomainName":"xxx",
     * "Duration":588.849,
     * "EndTime":"2016-05-25T05:47:11Z",
     * "Height":480,
     * "OssBucket":"bucket",
     * "OssEndpoint":"oss-cn-hangzhou.aliyuncs.com",
     * "OssObject":"atestObject.m3u8",
     * "RecordId":"c4d7f0a4-b506-43f9-8de3-07732c3f3d82",  $this->queryLiveRecordFile() 使用索引id
     * "RecordUrl":"http://xxx.xxx/atestObject.m3u8",
     * "StartTime":"2016-05-25T05:37:11Z",
     * "StreamName":"xxx",
     * "Width":640
     * },
     * "RequestId":"550439A3-F8EC-4CA2-BB62-B9DB43EEEF30"
     * }
     * @param string $StreamName 直播间号
     * @param string $EndTime 结束时间 按照当前时间后多少分钟
     * @param string $OssObject OSS 存储的录制文件名 示例:{AppName}/{StreamName}/{Date}/{Hour}/{Minute}_{Second}.m3u8
     * @return array|bool
     * */
    public function createLiveRecordConfig($StreamName, $EndTime, $OssObject = null)
    {
        $Time = time();
        $StartTime = date("Y-m-d\\TH:i:s\\Z", $Time);
        $EndTime = bcadd($Time, bcmul($EndTime, 60), 0);
        $EndTime = date("Y-m-d\\TH:i:s\\Z", $EndTime);
        $OssObject = is_null($OssObject) ? '{AppName}/{StreamName}/{Date}/{Hour}/{Minute}_{Second}.m3u8' : $OssObject;
        $request = new \live\Request\V20161101\CreateLiveStreamRecordIndexFilesRequest();
        $request->setAppName($this->appName)->setDomainName($this->playLike)->setStartTime($StartTime)->setEndTime($EndTime)
            ->setOssBucket($this->OssBucket)->setOssEndpoint($this->OssEndpoint)->setOssObject($OssObject)->setStreamName($StreamName);
        return $this->query($request);
    }

    /**
     * 查询录制的索引文件
     *
     * */
    public function queryLiveRecordFiles($StreamName, $StartTime, $EndTime, $page = 1, $limit = 10, $Order = 'desc')
    {
        $request = new \live\Request\V20161101\DescribeLiveStreamRecordIndexFilesRequest();
        $request->setStreamName($StreamName)->setDomainName($this->playLike)->setAppName($this->appName)->setStartTime($StartTime)
            ->setEndTime($EndTime)->setOrder($Order)->setPageNum($page)->setPageSize($limit);
        return $this->query($request);
    }

    /**
     * 查询单个录制的文件
     * @param string $StreamName 直播间号码
     * @param string $RecordId 索引文件id
     * @return array
     * */
    public function queryLiveRecordFile($StreamName, $RecordId)
    {
        $request = new \live\Request\V20161101\DescribeLiveStreamRecordIndexFileRequest();
        $request->setStreamName($StreamName)->setDomainName($this->playLike)->setAppName($this->appName)->setRecordId($RecordId);
        return $this->query($request);
    }

    /**
     * 查询直播间录制回放
     * @param string $StreamName 直播间号
     * @param string $StartTime 开始时间 示例：2015-12-01T17:36:00Z
     * @param string $EndTime 结束时间 示例：2015-12-01T17:36:00Z
     * @return JSON
     * */
    public function queryLiveRecordList($StreamName, $StartTime, $EndTime)
    {
        $StartTime = date("Y-m-d\\TH:i:s\\Z", $StartTime);
        $EndTime = date("Y-m-d\\TH:i:s\\Z", $EndTime);
        $request = new \live\Request\V20161101\DescribeLiveStreamRecordContentRequest();
        $request->setAppName($this->appName)->setDomainName($this->playLike)->setStreamName($StreamName)->setStartTime($StartTime)->setEndTime($EndTime);
        return $this->query($request);
    }

    /**
     * 执行单个请求
     * @param object $request
     * @return array | boolean
     * */
    public function query($request)
    {
        try {
            $response = $this->client->getAcsResponse($request);
            $response = json_encode($response);
            return json_decode($response, true);
        } catch (\Exception $e) {
            return self::setErrorInfo($e);
        }
    }

    /**
     * 执行多个请求
     * @param string $action 需要执行的动作
     * @return array
     *
     * */
    public function executeResponse($action = null)
    {
        $thsiAction = null;
        try {
            if ($action === null && count($this->action) == 1) $action = $this->action[0];
            if ($action) {
                $thsiAction = $action;
                $request = isset($this->request[$action]) ? $this->request[$action] : false;
                if (!$request) return self::setErrorInfo('请求的资源不存在！');
                $response = $this->client->getAcsResponse($request);
                $response = json_encode($response);
                $this->action = [];
                $this->request = [];
                return json_decode($response, true);
            } else {
                $responses = [];
                foreach ($this->request as $act => $request) {
                    $thsiAction = $act;
                    $response = $this->client->getAcsResponse($request);
                    $response = json_encode($response);
                    $responses[$act] = json_decode($response, true);
                }
                $responses = count($responses) == 1 && count($this->action) == 1 ? $responses[$this->action[0]] : $responses;
                $this->action = [];
                $this->request = [];
                return $responses;
            }
        } catch (\Exception $e) {
            return self::setErrorInfo($e, $thsiAction);
        }
    }
}