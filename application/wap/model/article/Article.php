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
namespace app\wap\model\article;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class Search
 * @package app\wap\model
 */
class Article extends ModelBasic
{
    use ModelTrait;

    public static function PreWhere($alias='',$model=null){
        if(is_null($model)) $model=new self();
        if($alias){
            $model->alias($alias);
            $alias.='.';
        }
        return $model->where(["{$alias}is_show"=>1,"{$alias}hide"=>0]);
    }
    public static function getLabelAttr($value){
        return is_string($value) ? json_decode($value,true) : $value;
    }

    /**
     * 活动列表
     */
    public static function getUnifiendList($where){
        $model=self::PreWhere();
        if($where['cid']) $model=$model->where('cid',$where['cid']);
        $list=$model->page((int)$where['page'],(int)$where['limit'])->order('sort DESC,add_time DESC')->select();
        $list=count($list) >0 ? $list->toArray() : [];
        foreach ($list as &$item){
            $item['add_time']=date('Y-m-d H:i',$item['add_time']);
        }
        return $list;
    }
}