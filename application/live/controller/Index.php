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

namespace app\live\controller;

use app\admin\model\live\LiveStudio;
use service\SystemConfigService;

class Index extends \think\Controller
{

    /**
     * 直播推流回调
     * */
    public function serve()
    {
        $request = $this->request->request();
        $id = isset($request['id']) ? $request['id'] : '';
        $action = $this->request->request('action', '');
        $is_play = $action == 'publish' ? 1 : 0;
        LiveStudio::setLivePalyStatus($id, $is_play, $action);
    }

    /**
     * 直播录制回调
     * */
    public function record()
    {

    }

    public function test()
    {
        $aliyunLive = \Api\AliyunLive::instance([
            'AccessKey' => SystemConfigService::get('accessKeyId'),
            'AccessKeySecret' => SystemConfigService::get('accessKeySecret'),
            'OssEndpoint' => SystemConfigService::get('aliyun_live_end_point'),
            'OssBucket' => SystemConfigService::get('aliyun_live_oss_bucket'),
            'appName' => SystemConfigService::get('aliyun_live_appName'),
            'payKey' => SystemConfigService::get('aliyun_live_play_key'),
            'key' => SystemConfigService::get('aliyun_live_push_key'),
            'playLike' => SystemConfigService::get('aliyun_live_playLike'),
            'rtmpLink' => SystemConfigService::get('aliyun_live_rtmpLink'),
        ]);
        $res = $aliyunLive->createLiveRecordConfig(95292930,1582271537);
        var_dump($res,$aliyunLive->getErrorInfo());
    }
}