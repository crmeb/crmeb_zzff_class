<?php
namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;


class UserSign extends ModelBasic
{
    use ModelTrait;


    public static function getUserSignList($where){
        $array = array();

        $model=self::alias('s')->join('User u','s.uid=u.uid');
        if (isset($where['title']) && $where['title']){
            $model=$model->where('s.uid|u.nickname','like','%'.$where['title'].'%');
        }
        $data=$model->field('s.*,u.nickname')->page((int)$where['page'],(int)$where['limit'])->order('s.add_time DESC')->select();
        count($data) && $data=$data->toArray();
        foreach ($data as &$item){
            $item['add_time']=date('Y-m-d H:i:s',$item['add_time']);
        }
        $count=self::count();
        return compact('data','count');
    }
}