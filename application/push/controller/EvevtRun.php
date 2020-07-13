<?php

namespace app\push\controller;

use app\wap\model\live\LiveUser;
use GatewayWorker\Lib\Gateway;


/*
 * 定时任务
 *
 * */

class EvevtRun
{

    /*
     * 默认定时器执行事件
     * */
    public function run()
    {

    }

    /*
     * 每隔6秒执行
     * */
    public function task_6()
    {
        $list = LiveUser::where('is_open_ben',0)->whereOr('is_online',1)->whereOr('is_ban',1)->field('is_ban,ban_time,is_open_ben,open_ben_time,is_online,uid')->select();
        foreach ($list as $item){
            $uid=$item['uid'];
            unset($item['uid']);
            $isUpdate = false;
            $is_online=Gateway::isUidOnline($uid);
            if($item['is_online'] != $is_online) $isUpdate = true;
            $item['is_online'] = $is_online;
            if($item['is_ban'] && $item['ban_time'] && $item['ban_time'] < time()){
                $isUpdate = true;
                $item['is_ban'] = 0;
                $item['ban_time'] = 0;
                if($item['is_online']){
                    Gateway::sendToUid($uid,json_encode([
                        'type'=>'ban',
                        'value'=>1
                    ]));
                }
            }
            if($item['is_open_ben'] && $item['open_ben_time'] && $item['open_ben_time'] < time()){
                $isUpdate = true;
                $item['is_open_ben'] = 0;
                $item['open_ben_time'] = 0;
            }
            $isUpdate && LiveUser::where(['uid'=>$uid])->update([
                'is_open_ben'=>$item['is_open_ben'],
                'open_ben_time'=>$item['open_ben_time'],
                'is_ban'=>$item['is_ban'],
                'ban_time'=>$item['ban_time'],
                'is_online'=>$item['is_online'],
            ]);
            unset($uid);
        }
    }

    /*
     * 每隔10秒执行
     * */
    public function task_10()
    {

    }

    /*
     * 每隔30秒执行
     * */
    public function task_30()
    {

    }

    /*
     * 每隔60秒执行
     * */
    public function task_60()
    {
    }
    /*
     * 每隔180秒执行
     * */
    public function task_180()
    {

    }

    /*
     * 每隔300秒执行
     * */
    public function task_300()
    {

    }

}