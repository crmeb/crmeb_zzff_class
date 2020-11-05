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

    public function SpecialSubject()
    {
        return $this->hasMany('SpecialSubject','grade_id');
    }
}