<?php
namespace app\admin\model\live;

/**
 * 直播嘉宾
 */
use basic\ModelBasic;
use traits\ModelTrait;

class LiveHonouredGuest extends ModelBasic
{
    use ModelTrait;

    /*
     * 设置where条件
     * @param array $where 条件
     * */
    public static function setWhere($where,$model = null,$alial='',$join='')
    {
        $model = $model === null ? new static() : $model;
        if($alial){
            $model = $model->alias($alial);
            $alial.='.';
        }
        if($where['nickname'] != '') $model = $model->where("{$alial}nickname".($join ? '|'.$join : ''),'LIKE',"$where[nickname]");
        return $model->where("{$alial}live_id",$where['live_id']);
    }

    /*
     * 获取嘉宾列表
     * @param array $where 查询条件
     * @return array
     * */
    public static function getGuestList($where)
    {
        $data = self::setWhere($where,null,'a','u.nickname')->order('a.sort desc,a.add_time')->join('user u','a.uid=u.uid')->field(['a.*','u.avatar','u.nickname as u_nickname'])
            ->page((int)$where['page'],(int)$where['limit'])->select();
        $count = self::setWhere($where,null,'a','u.nickname')->join('user u','a.uid=u.uid')->count();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item){
            $item['nickname'] = $item['nickname'] ? : $item['u_nickname'];
            $item['_type_name'] = $item['type'] ? '讲师': '助教';
        }
        return compact('data','count');
    }
}