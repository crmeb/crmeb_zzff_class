<?php

namespace app\push\controller;

use app\wap\model\live\LiveStudio;
use app\wap\model\live\LiveUser;
use GatewayWorker\Lib\Gateway;
use app\wap\model\live\LiveHonouredGuest;
use app\wap\model\user\User;
use app\wap\model\live\LiveBarrage;
use think\Log;

class Push
{

    /**
     * @var array 消息内容
     * */
    protected $message_data = [
        'type' => '',
        'message' => '',
    ];

    /**
     * @var string 消息类型
     * */
    protected $message_type = '';

    /**
     * @var string $client_id
     * */
    protected $client_id = '';

    /**
     * @var int 当前登陆用户
     * */
    protected $uid = null;

    /**
     * @var null 本类实例化结果
     * */
    protected static $instance = null;

    /**
     *
     * */
    protected function __construct($message_data = [])
    {

    }

    /**
     * 实例化本类
     * */
    public static function instance()
    {
        if (is_null(self::$instance)) self::$instance = new static();
        return self::$instance;
    }

    /**
     * 检测参数并返回
     * @param array || string $keyValue 需要提取的键值
     * @param null || bool $value
     * @return array;
     * */
    protected function checkValue($keyValue = null, $value = null)
    {
        if (is_null($keyValue))
            $message_data = $this->message_data;
        if (is_string($keyValue))
            $message_data = isset($this->message_data[$keyValue]) ? $this->message_data[$keyValue] : (is_null($value) ? '' : $value);
        if (is_array($keyValue))
            $message_data = array_merge($keyValue, $this->message_data);
        if (is_bool($value) && $value === true && is_array($message_data) && is_array($keyValue)) {
            $newData = [];
            foreach ($keyValue as $key => $item) {
                $newData [] = $message_data[$key];
            }
            return $newData;
        }
        return $message_data;
    }

    /**
     * 开始设置回调
     * @param string $typeFnName 回调函数名
     * @param string $client_id
     * @param array $message_data
     *
     * */
    public function start($typeFnName, $client_id, $message_data)
    {

        $this->message_type = $typeFnName;

        $this->message_data = $message_data;

        $this->client_id = $client_id;

        $this->uid = Gateway::getUidByClientId($client_id);
        //记录用户上线
        if ($this->uid && Gateway::isOnline($client_id) && ($live_id = $this->checkValue('room'))) {
            LiveUser::setLiveUserOnline($live_id, $this->uid, 1);
            $error['uid'] = $this->uid;
            $error['is_line'] = Gateway::isOnline($client_id);
            $error['live_id'] = $this->checkValue('room');
        }else{
            $error['uid'] = $this->uid;
            $error['is_line'] = Gateway::isOnline($client_id);
            $error['live_id'] = $this->checkValue('room');
            Log::write(json_encode($error));
        }

        if (method_exists($this, $typeFnName))
            call_user_func([$this, $typeFnName]);
        else
            throw new \Exception('缺少回调方法');
    }


    /**
     * 心跳检测
     *
     * */
    protected function ping()
    {
        Gateway::sendToClient($this->client_id, json_encode(['ping' => 'ok']));
    }

    /**
     * 绑定用户相应客户端
     * @param string $client_id
     * @param array $message_data
     * @return
     * */
    protected function handshake()
    {
        $message_data = $this->checkValue(['uid' => 0, 'room' => 0]);
       // Log::write("心跳用户：".json_encode($message_data));
        if (!$message_data['uid']){
            Gateway::closeClient($this->client_id);
            throw new \Exception("缺少用户uid，无法绑定用户");
        }
        $new_message = [
            'type' => $this->message_type,
            'uid' => $message_data['uid'],
            'room' => $message_data['room'],
            'client_id' => $this->client_id,
            'time' => date('H:i:s'),
            'msg' => '绑定成功!'
        ];
        Log::write(json_encode($new_message));
        Gateway::bindUid($this->client_id, $message_data['uid']);

        //如果有群组id加入群组
        if ($message_data['room']) {
            // 加入某个群组（可调用多次加入多个群组） 将clientid加入roomid分组中
            Gateway::joinGroup($this->client_id, $message_data['room']);
            Gateway::sendToGroup($message_data['room'], json_encode([
                'type' => 'join_Group',
            ]));
        }
        Gateway::sendToClient($this->client_id, json_encode($new_message));
    }

    /**
     * 接受客户端发送的消息
     * @param string $client_id 客户端client_id
     * @param array $message_data 发送的数据
     * @return
     *
     * */
    protected function send()
    {
        list($toUid, $message, $room, $type) = $this->checkValue(['uid' => 0, 'content' => '', 'room' => false, 'ms_type' => 0], true);
        $client_id = $this->client_id;
        if (!$this->uid) {
            //认证用户信息失败，关闭用户链接
            Gateway::closeClient($client_id);
            throw new \Exception("缺少用户uid");
        }
        $userInfo = User::get($this->uid);
        if (!$userInfo) {
            //认证用户信息失败，关闭用户链接
            Gateway::closeClient($client_id);
            throw new \Exception("用户信息缺少");
        }
        if ($room && Gateway::getClientIdCountByGroup($room)) {
            $user_type = LiveHonouredGuest::where(['uid' => $this->uid, 'live_id' => $room])->value('type');
            if (is_null($user_type)) $user_type = 2;
            $res = LiveBarrage::set([
                'live_id' => $room,
                'uid' => $this->uid,
                'type' => $type,
                'barrage' => $message,
                'add_time' => time(),
                'is_show' => 1
            ]);
            if (!$res) throw new \Exception("写入历史记录失败");
            Gateway::sendToGroup($room, json_encode([
                'message' => $message,
                'm_type' => $type,
                'type' => 'message',
                'user_type' => $user_type,
                'userInfo' => $userInfo,
                'id' => $res['id']
            ]));
        } else {
            $new_message = [
                'type' => 'reception',
                'content' => $message,
                'time' => date('H:i:s'),
                'timestamp' => time(),
            ];
            if (Gateway::isUidOnline($toUid)) return Gateway::sendToUid($toUid, json_encode($new_message));
        }
    }

    /**
     * 消息撤回
     * @param string $client_id
     * @param array $message_data
     * */
    protected function recall()
    {
        list($id, $room) = $this->checkValue(['id' => 0, 'room' => ''], true);

        if (!$id)
            throw new \Exception('缺少撤回消息的id');

        if (!$room)
            throw new \Exception('缺少房间号');

        if (LiveBarrage::del($id)) {
            Gateway::sendToGroup($room, json_encode([
                'type' => 'recall',
                'id' => $id
            ]), Gateway::getClientIdByUid($this->uid));
        }
    }

    /**更新直播间人数
     * @throws \think\Exception
     */
    protected function room_user_count(){
        list($room, $uid) = $this->checkValue(['room' => '', 'uid' => 0], true);
        $onLine_user_count = LiveUser::where(['is_open_ben' => 0, 'live_id' => $room, 'is_online' => 1])->count();
        $onLine_num = LiveStudio::where(['id' => $room])->value('online_num');
        $user_type = 3;
        if (!$uid || $uid == 0) {
            Gateway::closeClient( $this->client_id);
            $notice_content = "<span style='color: darkred'>欢迎新朋友进入直播间</span>";
        }else{
            $user_type = LiveHonouredGuest::where(['uid' => $uid, 'live_id' => $room])->field('type')->find();
            $user_type = (isset($user_type['type']) && $user_type) ? $user_type['type'] : 3;
            $user_info = User::where(['uid' => $uid])->field('uid, account, nickname, phone, avatar')->find();
            $user_name = $user_info['nickname'] ? $user_info['nickname'] : ($user_info['account'] ? $user_info['account'] : "新游客");
            $notice_content = $user_name. " 来了";
        }

        Gateway::sendToGroup($room, json_encode(['onLine_user_count' => $onLine_user_count + $onLine_num, 'type' => 'room_user_count', 'notice_content' => $notice_content, 'user_type' => $user_type]));
    }

}