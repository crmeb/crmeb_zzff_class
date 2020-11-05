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


use Api\AliyunLive;
use Api\AliyunLive as ApiAliyunLive;
use app\admin\model\system\SystemGroupData;
use app\wap\model\live\LiveBarrage;
use app\wap\model\live\LiveGoods;
use app\wap\model\live\LiveHonouredGuest;
use app\wap\model\live\LiveReward;
use app\wap\model\live\LiveStudio;
use app\wap\model\live\LiveUser;
use app\wap\model\special\SpecialBuy;
use app\wap\model\special\Special;
use app\wap\model\special\SpecialTask;
use app\wap\model\user\User;
use app\wap\model\user\UserBill;
use service\GroupDataService;
use service\UtilService;
use think\Config;
use service\JsonService;
use think\Db;
use think\Url;
use service\SystemConfigService;

class Live extends AuthController
{

    /*
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'get_live_record_list',
        ];

    }

    /**
     * 阿里云直播句柄
     * @var \Api\AliyunLive
     */
    protected $aliyunLive;

    protected function _initialize()
    {
        parent::_initialize();
        $this->aliyunLive = \Api\AliyunLive::instance([
            'AccessKey' => SystemConfigService::get('accessKeyId'),
            'AccessKeySecret' => SystemConfigService::get('accessKeySecret'),
            'OssEndpoint' => SystemConfigService::get('end_point'),
            'OssBucket' => SystemConfigService::get('OssBucket'),
            'appName' => SystemConfigService::get('aliyun_live_appName'),
            'payKey' => SystemConfigService::get('aliyun_live_play_key'),
            'key' => SystemConfigService::get('aliyun_live_push_key'),
            'playLike' => SystemConfigService::get('aliyun_live_playLike'),
            'rtmpLink' => SystemConfigService::get('aliyun_live_rtmpLink'),
        ]);
    }


    /**
     * 直播间主页
     * @param string $stream_name
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($stream_name = '', $special_id = 0, $live_id = 0, $record_id = 0)
    {
        if ($special_id && $special_id != 0 && $live_id && $live_id != 0) {
            $special_task = SpecialTask::where(['special_id' => $special_id, 'live_id' => $live_id])->value('live_id');
            if (!$special_task) {
                $this->failed('直播间不存在');
            }
            $stream_name = LiveStudio::where('id', $live_id)->value('stream_name');
        }
        if (!$stream_name) return $this->failed('缺少在直播间号！');
        $liveInfo = LiveStudio::where('stream_name', $stream_name)->find();
        if (!$liveInfo) return $this->failed('直播间不存在');
        if ($liveInfo->is_del) return $this->failed('直播间已被删除');
        $userInfo = LiveUser::setLiveUser($this->uid, $liveInfo->id);
        if ($userInfo === false) return $this->failed(LiveUser::getErrorInfo('用户写入不成功'));
        $specialLive = \app\wap\model\special\Special::where(['is_show' => 1, 'is_del' => 0, 'id' => $liveInfo->special_id])->find();
        if (!$specialLive) return $this->failed('专题不存在或者已被删除');
        if ($specialLive->pay_type == 1 && !SpecialBuy::PaySpecial($specialLive->id, $this->uid)) {
            if ($specialLive->member_pay_type == 1){
                return $this->failed('您还没有支付请支付后再进行观看', Url::build('special/details', ['id' => $liveInfo->special_id]));
            }
        }
        $AliyunLive = $this->aliyunLive;
        if ($liveInfo->is_play)
            $PullUrl = $AliyunLive->getPullSteam($liveInfo->stream_name);
        else {
            $record_id = $record_id ? $record_id : $liveInfo->playback_record_id;
            if ($liveInfo->is_playback && $record_id) {
                $res = $AliyunLive->queryLiveRecordFile($liveInfo->stream_name, $record_id);
                if ($res === false) {
                    $liveInfo->is_playback = 0;
                    $liveInfo->playback_record_id = '';
                    $liveInfo->save();
                }
                $PullUrl = isset($res['RecordIndexInfo']['RecordUrl']) ? $res['RecordIndexInfo']['RecordUrl'] : false;
            } else
                $PullUrl = false;
        }
        $live_status = 0;
        $datatime = strtotime($liveInfo->start_play_time);
        $endTime = strtotime($liveInfo->stop_play_time);
        if ($datatime < time() && $endTime > time())
            $live_status = 1;//正在直播
        else if ($datatime < time() && $endTime < time())
            $live_status = 2;//直播结束
        else if ($datatime > time())
            $live_status = 0;//尚未直播
        $user_type = LiveHonouredGuest::where(['uid' => $this->uid, 'live_id' => $liveInfo->id])->value('type');
        if (is_null($user_type)) $user_type = 2;
        $uids = LiveHonouredGuest::where(['live_id' => $liveInfo->id])->column('uid');
        $liveInfo['abstract'] = $specialLive->abstract;
        $this->assign([
            'goldInfo' => json_encode(SystemConfigService::more("gold_name,gold_rate,gold_image")),
            'liveInfo' => json_encode($liveInfo),
            'UserSum' => bcadd(LiveUser::where(['live_id' => $liveInfo->id, 'is_open_ben' => 0, 'is_online' => 1])->sum('visit_num'), $liveInfo->online_num, 0),
            'live_title' => $liveInfo->live_title,
            'PullUrl' => $PullUrl,
            //'requirejs' => true,
            'is_ban' => $userInfo->is_ban,
            'room' => $liveInfo->id,
            'datatime' => $datatime,
            'workerman' => json_encode(Config::get('workerman.chat', [])),
            'phone_type' => UtilService::getDeviceType(),
            'live_status' => $live_status,
            'user_type' => $user_type,
            'OpenCommentCount' => LiveBarrage::where(['live_id' => $liveInfo->id, 'is_show' => 1])->count(),
            'OpenCommentTime' => LiveBarrage::where(['live_id' => $liveInfo->id, 'is_show' => 1])->order('add_time asc')->value('add_time'),
            'CommentCount' => LiveBarrage::where(['live_id' => $liveInfo->id, 'is_show' => 1])->where('uid', 'in', $uids)->count(),
            'CommentTime' => LiveBarrage::where(['live_id' => $liveInfo->id, 'is_show' => 1])->where('uid', 'in', $uids)->order('add_time asc')->value('add_time'),
        ]);
        return $this->fetch();
    }

    /**
     * 获取助教评论
     */
    public function get_comment_list()
    {
        list($page, $limit, $live_id, $add_time) = UtilService::getMore([
            ['page', 0],
            ['limit', 20],
            ['live_id', 0],
            ['add_time', 0],
        ], $this->request, true);
        if (!$live_id) return JsonService::fail('缺少参数!');
        $uids = LiveHonouredGuest::where(['live_id' => $live_id])->column('uid');
        if (!$uids) {
            $ystemConfig = \service\SystemConfigService::more(['site_name', 'site_logo']);
            $data = [
                'nickname' => $ystemConfig['site_name'],
                'avatar' => $ystemConfig['site_logo'],
                'user_type' => 2,
                'content' => LiveStudio::where('id', $live_id)->value('auto_phrase'),
                'id' => 0,
                'type' => 1,
                'uid' => 0
            ];
            return JsonService::successful(['list' => [$data], 'page' => 0]);
        }
        return JsonService::successful(LiveBarrage::getCommentList($uids, $live_id, $page, $limit, $add_time));
    }

    /**
     * 获取助教，讲师，普通人的评论
     */
    public function get_open_comment_list()
    {
        list($page, $limit, $live_id, $add_time) = UtilService::getMore([
            ['page', 0],
            ['limit', 20],
            ['live_id', 0],
            ['add_time', 0],
        ], $this->request, true);
        if (!$live_id) return JsonService::fail('缺少参数!');
        return JsonService::successful(LiveBarrage::getCommentList(false, $live_id, $page, $limit, $add_time));
    }
    /**
     * 获取直播间下的录制内容
     * @param string $record_id
     * @param string $stream_name
     * @param string $start_time
     * @param string $end_time
     * @param int $page
     * @param int $limit
     */
    public function get_live_record_list($special_id = '', $start_time = '', $end_time = '', $page = 1, $limit = 10)
    {
        if (!$special_id) return JsonService::fail('参数缺失');
        $specialInfo = Special::get($special_id);
        if (!$specialInfo) return JsonService::fail('直播专题不存在');
        $liveStudio = LiveStudio::where(['special_id' => $specialInfo['id']])->find();
        if (!$liveStudio) return JsonService::fail('缺少直播间');
        if (!$liveStudio['stream_name']) return JsonService::fail('缺少直播间id');
        $aliyunLive = $this->aliyunLive;
        $beginToday = mktime(0, 0, 0, date('m'), date('d') - 3, date('Y'));
        if ($start_time && $end_time) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            if ($start_time > $end_time) return JsonService::fail('开始时间不能大于结束时间');
            $time = bcsub($end_time, $start_time, 0) / 86400;
            if ($time > 4) return JsonService::fail('开始和结束的时间不能间隔4天');
        }
        $res = $aliyunLive->queryLiveRecordFiles(
            $liveStudio['stream_name'],
            $start_time ? ApiAliyunLive::setTimeFormat($start_time) : ApiAliyunLive::setTimeFormat($beginToday),
            $end_time ? ApiAliyunLive::setTimeFormat($end_time) : ApiAliyunLive::setTimeFormat(time()),
            $page,
            $limit
        );
        $data = [];
        $count = 0;
        if ($res) {
            if (isset($res['RecordIndexInfoList']['RecordIndexInfo'])) {
                foreach ($res['RecordIndexInfoList']['RecordIndexInfo'] as $item) {
                    $data['list'][] = [
                        'StreamName' => $item['StreamName'],
                        'RecordId' => $item['RecordId'],
                        'playback_record_id' => $liveStudio['playback_record_id'],
                        'RecordUrl' => $item['RecordUrl'],
                        'StartTime' => $item['StartTime'],
                        'EndTime' => $item['EndTime'],
                    ];
                }
            }
            if (isset($res['TotalNum'])) $count = $res['TotalNum'];
            $data['page'] = $page++;
            $data['count'] = $count;
            return JsonService::successful($data);
        } else {
            return JsonService::fail("网络错误");
        }
    }

}
