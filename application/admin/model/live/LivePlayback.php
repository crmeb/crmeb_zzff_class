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
 * 直播间用户表
 */
use basic\ModelBasic;
use traits\ModelTrait;

class LivePlayback extends ModelBasic
{
    use ModelTrait;

    /**储存直播回放
     * @param $item
     * @param $record_id
     */
    public static function livePlaybackAdd($item){
        if(!self::be(['RecordId'=>$item['RecordId'],'stream_name'=>$item['StreamName']])){
            $data=[
                'stream_name'=>$item['StreamName'],
                'playback_url'=>$item['RecordUrl'],
                'start_time'=>strtotime($item['StartTime']),
                'end_time'=>strtotime($item['EndTime']),
                'RecordId'=>$item['RecordId'],
                'add_time'=>time(),
            ];
            return self::set($data);
        }else{
            return true;
        }
    }
    /**
     *  设置查询条件
     */
    public static function setUserWhere($where,$model = null,$alias='a',$jsonField='u.nickname')
    {
        $model = is_null($model) ? new self() : $model ;
        if($alias){
            $model = $model->alias($alias);
            $alias.='.';
        }
        if($where['start_time'] && $where['end_time']) $model = $model->where("{$alias}add_time",'between',[strtotime($where['start_time']),strtotime($where['end_time'])]);
        return $model->order("{$alias}sort desc,{$alias}add_time desc")->where("{$alias}stream_name",$where['stream_name'])->where("{$alias}is_del",0);
    }

    /**
     * 查询直播间用户列表
     * @param array $where
     */
    public static function getLivePlaybackList($where)
    {
        $data = self::setUserWhere($where)
            ->page((int)$where['page'],(int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item){
            $item['_add_time']  = date('Y-m-d H:i:s',$item['add_time']);
            $item['StartTime']  = date('Y-m-d H:i:s',$item['start_time']);
            $item['EndTime']  = date('Y-m-d H:i:s',$item['end_time']);
        }
        $count = self::setUserWhere($where)->count();
        return compact('data','count');
    }

}
