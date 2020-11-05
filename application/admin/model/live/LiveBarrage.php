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
 * 直播间评论表
 */
use basic\ModelBasic;
use traits\ModelTrait;

class LiveBarrage extends ModelBasic
{

    use ModelTrait;

    /*
     * 设置where条件
     * @param array $where 查询条件
     * @param object $model 模型实例化对象
     * @param string $alias 表别名
     * @param string $jsonField json 模糊查询字段
     * @return object
     * */
    public static function setWhere($where,$model =null ,$alias ='',$jsonField='')
    {
        $model = is_null($model) ? new self() : $model;
        if($alias){
            $model = $model->alias($alias);
            $alias.='.';
        }
        if($where['nickname']) $model = $model->where("{$alias}barrage".($jsonField ? '|'.$jsonField : ""),'like',"%$where[nickname]%");
        if($where['live_id']) $model = $model->where("{$alias}live_id",$where['live_id']);
        if($where['start_time'] && $where['end_time']) $model = $model->where("{$alias}add_time",'between',[strtotime($where['start_time']),strtotime($where['end_time'])]);
        return $model;
    }
    /*
     * 查询评论列表
     * @param array $where 查询条件
     * @return array
     * */
    public static function getLiveCommentList($where)
    {
        $data = self::setWhere($where,null,'a','u.nickname')->join('user u','u.uid=a.uid')->field(['a.*','u.avatar','u.nickname'])
            ->page((int)$where['page'],(int)$where['limit'])->order('id','desc')->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item){
            $type = LiveHonouredGuest::where(['uid'=>$item['uid'],'live_id'=>$where['live_id']])->value('type');
            if($type === null)
                $item['type_name'] = '听众';
            else if($type === 1)
                $item['type_name'] = '讲师';
            else if($type === 0)
                $item['type_name'] = '助教';
            $item['add_time'] = date('Y-m-d H:i:s',$item['add_time']);
        }
        $count = self::setWhere($where,null,'a','u.nickname')->join('user u','u.uid=a.uid')->count();
        return compact('data','count');
    }

}