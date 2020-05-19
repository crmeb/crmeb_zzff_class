<?php

namespace app\push\controller;


use GatewayWorker\Lib\Gateway;
use think\Hook;
use Workerman\Lib\Timer;


class Events
{

    //定时器间隔
    protected static $interval = 2;
    //定时器
    protected static $timer = null;

    /**
     *
     * @var object
     */
    protected static $worker;

    /**
     * 事件处理类
     * @var string
     */
    protected static $evevtRunClass = \app\push\controller\EvevtRun::class;

    /**
     * 消息事件回调
     * @var string
     */
    protected static $eventClassName = \app\push\controller\Push::class;

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $message)
    {
        $message_data = json_decode($message, true);
        if (!$message_data) return;
        try {
            if (!isset($message_data['type'])) throw new \Exception('缺少消息参数类型');
            //消息回調处理
            $evevtName = self::$eventClassName . '::instance';
            if (is_callable($evevtName))
                $evevtName()->start($message_data['type'], $client_id, $message_data);
            else
                throw new \Exception('消息处理回调不存在。[' + $evevtName + ']');
        } catch (\Exception $e) {
            var_dump([
                'file' => $e->getFile(),
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * 当用户连接时触发的方法
     * @param integer $client_id 连接的客户端
     * @return void
     */
    public static function onConnect($client_id)
    {
        Gateway::sendToClient($client_id, json_encode(array(
            'type' => 'init',
            'client_id' => $client_id
        )));
    }

    /**
     * 当用户断开连接时触发的方法
     * @param integer $client_id 断开连接的客户端
     * @return void
     */
    public static function onClose($client_id)
    {
        Gateway::sendToClient($client_id, json_encode([
            'type' => 'logout',
            'message' => "client[$client_id]"
        ]));
    }

    /**
     * 当进程启动时
     * @param integer $businessWorker 进程实例
     */
    public static function onWorkerStart($worker)
    {
        //在进程1上开启定时器 每self::$interval秒执行
        self::$worker = $worker;
        if ($worker->id === 0) {
            $last = time();
            $task = [6 => $last, 10 => $last, 30 => $last, 60 => $last, 180 => $last, 300 => $last];
            self::$timer = Timer::add(self::$interval, function () use (&$task) {
                try {
                    $now = time();
                    Hook::exec(self::$evevtRunClass);
                    foreach ($task as $sec => &$time) {
                        if (($now - $time) >= $sec) {
                            $time = $now;
                            Hook::exec(self::$evevtRunClass, 'task_' . $sec);
                        }
                    }
                } catch (\Throwable $e) {
                }

            });
        }

    }

    /**
     * 当进程关闭时
     * @param integer $businessWorker 进程实例
     */
    public static function onWorkerStop($worker)
    {
        if ($worker->id === 0) Timer::del(self::$timer);
    }
}
