<?php


namespace app\admin\model\special;


use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class Grade 年级部
 * @package app\admin\model\special
 */
class Grade extends ModelBasic
{
    use ModelTrait;

    public static function getAll()
    {
        return self::order('sort desc,add_time desc')->field(['name', 'id'])->select();
    }

    public static function getAllList($where)
    {
        $data = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->select();
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    public static function setWhere($where)
    {
        $model = self::order('sort desc,add_time desc');
        if ($where['name'] != '') $model = $model->where('name', 'like', "%$where[name]%");
        if ($where['cid'] != '') $model = $model->where('id', $where['cid']);
        return $model;
    }
}