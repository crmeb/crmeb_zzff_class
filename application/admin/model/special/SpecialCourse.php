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

namespace app\admin\model\special;


use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class SpecialCourse 专题课程
 * @package app\admin\model\special
 */
class SpecialCourse extends ModelBasic
{
    use ModelTrait;

    public static function getAddTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public static function setWhere($where, $alias = '', $model = null)
    {
        if ($model === null) $model = new  self();
        if ($alias) $model = $model->alias($alias);
        $alias = $alias ? $alias . '.' : '';
        if ($where['is_show'] !== '') $model = $model->where("{$alias}is_show", $where['is_show']);
        if ($where['special_id']) $model = $model->where("{$alias}special_id", $where['special_id']);
        if ($where['course_name']) $model = $model->where("{$alias}course_name", 'LIKE', "%$where[course_name]%");
        return $model;
    }

    public static function getCourseList($where)
    {
        $data = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($data as &$item) {
            $item['special_name'] = Special::where('id', $item['special_id'])->value('title');
            $item['number'] = SpecialTask::where(['coures_id' => $item['id'], 'is_show' => 1])->count();
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    public static function DelCourse($id)
    {
        $coures = self::get($id);
        if (!$coures) return false;
        if (SpecialTask::where('coures_id', $id)->count()) return self::setErrorInfo('请先删除此课程下的任务再尝试删除');
        return $coures->delete();
    }

}