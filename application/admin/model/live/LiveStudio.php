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

namespace app\admin\model\live;

/**
 * 直播信息表
 */

use app\admin\model\order\StoreOrder;
use basic\ModelBasic;
use traits\ModelTrait;
use \GatewayWorker\Lib\Gateway;
use service\SystemConfigService;

class LiveStudio extends ModelBasic
{

    use ModelTrait;

    protected $insert = ['add_time'];

    //直播间号码不能连续存在的数字
    protected static $taboo = ['4', '38', '13', '14', '7', '7086', '5454', '3838', '36', '69', '47', '18'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    /**
     * 生成并检查直播间号码
     * @param string $StreamName 直播间号码
     * @return string
     * */
    public static function getliveStreamName($StreamName = '')
    {
        if ($StreamName) {
            if (preg_match('/[\x7f-\xff]/', $StreamName)) return self::setErrorInfo('直播间号码不能为中文');
            if (strlen($StreamName) < 6) return self::setErrorInfo('直播间号码不能少于6位');
            if (self::be(['stream_name' => $StreamName])) return self::setErrorInfo('直播间号码已存在');
            return $StreamName;
        } else {
            $num = mt_rand(10, 99999999);//生成随机数
            //开头不允许为0
            if (strpos((string)$num, chr(0)) === 0) return self::getliveStreamName();
            //不允许连续出现预设的数字
            foreach (self::$taboo as $val) {
                if (strstr((string)$num, $val)) return self::getliveStreamName();
            }
            if (strlen($num) > 6)
                $num = str_pad((string)$num, 8, 0, STR_PAD_RIGHT);//大于6位的填充到8位
            else
                $num = str_pad((string)$num, 6, 0, STR_PAD_RIGHT);//小于6位的填充到6位
            if (self::be(['stream_name' => $num])) return self::getliveStreamName();
            return $num;
        }
    }

    /**
     * 设置直播间直播状态
     * @param $StreamName
     * @return bool|false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function setLivePalyStatus($StreamName, $is_play, $action)
    {
        $liveInfo = self::where('stream_name', $StreamName)->find();
        if (!$liveInfo) return self::setErrorInfo('暂未查到直播间');
        live_log('log', ['title'=>'开始','liveInfo'=>$liveInfo->toArray(),'streamName'=>$StreamName,'is_play'=>$is_play]);
        if ($liveInfo->is_play == 1 && $action == 'publish') {
            return true;
        } else if ($liveInfo->is_play == 0 && $action == 'publish_done') {
            return true;
        }
        $liveInfo->is_play = $is_play;
        try {
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
            if ($liveInfo->is_play) {
                //开启自动录制，进行手开启动录制直播间
                if ($liveInfo->is_recording) {
                    $res = $aliyunLive->liveRecording($liveInfo->stream_name)->executeResponse();
                    live_log('log', ['title'=>'直播录制开启','res'=>$res,'error'=>$aliyunLive->getErrorInfo()]);
                    if ($res) $liveInfo->recording = 1;
                }
                //向所有在线用户广播直播地址
                $workerman = \think\Config::get('workerman.channel', []);
                Gateway::$registerAddress = $workerman['ip'] . ':' . $workerman['port'];
                if (Gateway::getClientIdCountByGroup($liveInfo->id)) {
                    Gateway::sendToGroup($liveInfo->id, json_encode([
                        'type' => 'live_ing',
                        'pull_url' => $aliyunLive->getPullSteam($liveInfo->stream_name),
                    ]));
                }
            } else {
                //开启自动录制，进行手动停止录制直播间
                if ($liveInfo->recording) {
                    $res = $aliyunLive->liveRecording($liveInfo->stream_name, false)->executeResponse();
                    live_log('log', ['title'=>'直播录制关闭','res'=>$res,'error'=>$aliyunLive->getErrorInfo()]);
                    $liveInfo->recording = 0;
                }
                //向所有在线用户广播直播地址
                $workerman = \think\Config::get('workerman.channel', []);
                Gateway::$registerAddress = $workerman['ip'] . ':' . $workerman['port'];
                if (Gateway::getClientIdCountByGroup($liveInfo->id)) {
                    Gateway::sendToGroup($liveInfo->id, json_encode([
                        'type' => 'live_end',
                        'pull_url' => false,
                    ]));
                }
            }
        } catch (\Exception $e) {
            live_log('error', ['code' => $e->getCode(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            if ($liveInfo->recording) $liveInfo->recording = 0;
            self::setErrorInfo($e->getMessage());
        }
        return $liveInfo->save();
    }

    /**
     * 设置直播查询条件
     * @param $where
     * @param null $model
     * @param string $alias
     * @return $this
     */
    public static function setLiveWhere($where, $model = null, $alias = '')
    {
        $model = is_null($model) ? new self() : $model;
        if ($alias) {
            $model = $model->alias($alias);
            $alias .= '.';
        }
        if ($where['special_id']) $model = $model->where("{$alias}special_id", $where['special_id']);
        if (isset($where['admin_id']) && $where['admin_id']) $model = $model->where("S.admin_id", $where['admin_id']);
        if ($where['stream_name']) $model = $model->where("{$alias}stream_name", $where['stream_name']);
        if ($where['start_time'] && $where['end_time']) $model = $model->where("{$alias}add_time", 'between', [strtotime($where['start_time']), strtotime($where['end_time'])]);
        return $model->where("{$alias}is_del", 0);
    }

    /**
     * 查询直播间列表
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function getLiveList($where)
    {
        $data = self::setLiveWhere($where,null,'L')
            ->field('L.*, S.admin_id as admin_id')->join('__SPECIAL__ S', 'L.special_id=S.id', 'LEFT')->order('L.sort desc,L.add_time desc')->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['studio_pwd'] = $item['studio_pwd'] ?: '';
            $item['buy_user_num'] = StoreOrder::where(['paid' => 1, 'cart_id' => $item['special_id']])->count('id');
        }
        $count = self::setLiveWhere($where, null, 'L')->join('__SPECIAL__ S', 'L.special_id=S.id', 'LEFT')->count();
        return compact('data', 'count');
    }

}