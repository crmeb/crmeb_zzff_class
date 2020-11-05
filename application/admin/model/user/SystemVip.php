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

namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;
/**
 * 会员设置 model
 * Class User
 * @package app\admin\model\user
 */

class SystemVip extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where){
        $model=self::where('mer_id',0)->where('is_del',0)->order('id desc');
        if($where['is_show']!='') $model->where('is_show',$where['is_show']);
        if($where['title']!='') $model->where('title','like',"%$where[title]%");
        if($where['is_forever']!='') $model->where('is_forever',$where['is_forever']);
        if($where['start_time']!='' && $where['end_time']!='')
            $model->whereTime('add_time','between',[strtotime($where['start_time']),strtotime($where['end_time'])]);
        return $model;
    }
    public static function getSytemVipList($where){
        $data=($list=self::setWhere($where)
            ->page((int)$where['page'],(int)$where['limit'])
            ->select()) && count($list) ?  $list->toArray() : [];
        foreach ($data as &$item){
            $item['valid_date']=$item['is_forever']==1 ? '永久时效' : $item['valid_date'];
            $item['is_forever']=$item['is_forever']==0 ? '非永久会员' : '永久会员';
        }
        $count=self::setWhere($where)->count();
        return compact('data','count');
    }
    public static function getSytemVipSelect(){
        return self::where('mer_id',0)->where('is_del',0)->where('is_show',1)->column('title','id');
    }
}