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

class RecommendBanner extends ModelBasic
{
    use ModelTrait;

    public static function valiWhere($alias = '', $model = null)
    {
        if (is_null($model)) $model = new self();
        if ($alias) {
            $model = $model->alias($alias);
            $alias .= '.';
        }
        return $model->where("{$alias}is_show", 1);
    }

    public static function getRecemmodBannerList($where)
    {
        $data = self::valiWhere()->where('recommend_id', $where['id'])->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        $count = self::valiWhere()->where('recommend_id', $where['id'])->count();
        return compact('data', 'count');
    }
}