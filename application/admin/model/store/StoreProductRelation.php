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


namespace app\admin\model\store;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 点赞and收藏 model
 * Class StoreProductRelation
 * @package app\admin\model\store
 */
class StoreProductRelation extends ModelBasic
{
    use ModelTrait;


    public static function getCollect($pid){
      $model = new self();
      $model = $model->where('r.product_id',$pid)->where('r.type','collect');
      $model = $model->alias('r')->join('__WECHAT_USER__ u','u.uid=r.uid');
      $model = $model->field('r.*,u.nickname');
      return self::page($model);
    }
    public static function getLike($pid){
      $model = new self();
      $model = $model->where('r.product_id',$pid)->where('r.type','like');
      $model = $model->alias('r')->join('__WECHAT_USER__ u','u.uid=r.uid');
      $model = $model->field('r.*,u.nickname');
      return self::page($model);
    }

}