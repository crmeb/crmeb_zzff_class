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

namespace app\admin\model\ump;

use traits\ModelTrait;
use basic\ModelBasic;
use think\Db;

class EventRegistration extends ModelBasic
{
    use ModelTrait;


    public static function systemPage($where = array()){
        $model = self::setWherePage(self::setWhere($where));
        $model = $model->where('is_del',0)->order('add_time DESC');
        $list = $model ->page((int)$where['page'], (int)$where['limit'])->select()->each(function ($item){
            $item['address']=$item['province'].$item['city'].$item['district'].$item['detail'];
        });
        $count = self::setWherePage(self::setWhere($where))->count();
        return ['count' => $count, 'data' => $list];
    }
    /*
  * 设置搜索条件
  *
  */
    public static function setWhere($where)
    {
        $model=new self;
        if ($where['title'] != '') {
            $model = $model->where('title','like',"%$where[title]%");
        }

        return $model;
    }
    public static function delArticleCategory($id){
        $data['is_del']=1;
        return self::edit($data,$id);
    }
}