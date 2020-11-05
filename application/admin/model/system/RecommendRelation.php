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

namespace app\admin\model\system;


use app\admin\model\article\Article;
use app\admin\model\special\Special;
use app\admin\model\special\SpecialTask;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class SystemAdmin
 * @package app\admin\model\system
 */
class RecommendRelation extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    public static function setAddTimeAttr($value)
    {
        return time();
    }

    public static function getAll($where, $id)
    {
        $data = self::where('recommend_id', $id)->order('add_time desc')->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($data as &$itme) {
            if ($itme['type'] == 0) {
                $itme['type_name'] = '专题';
                $link = Special::PreWhere()->where('id', $itme['link_id'])->find();
                $itme['count'] = SpecialTask::getTaskCount($itme['link_id']);
            } else if ($itme['type'] == 1) {
                $itme['type_name'] = '图文';
                $link = Article::PreWhere()->where('id', $itme['link_id'])->find();
                $itme['count'] = 0;
            }
            $itme['title'] = $link['title'];

        }
        $count = self::where('recommend_id', $id)->count();
        return compact('data', 'count');
    }
}