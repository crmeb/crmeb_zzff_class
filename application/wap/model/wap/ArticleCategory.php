<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
//

namespace app\wap\model\wap;

use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class ArticleCategory
 * @package app\wap\model
 */
class ArticleCategory extends ModelBasic
{
    use ModelTrait;

    public static function cidByArticleList($cid, $first, $limit, $field = '*')
    {
        $model = Db::name('article');
        if ($cid) $model->where("CONCAT(',',cid,',') LIKE '%,$cid,%'", 'exp');
        return $model->field($field)->where('status', 1)->where('hide', 0)->order('sort DESC,add_time DESC')->limit($first, $limit)->select();
    }
}