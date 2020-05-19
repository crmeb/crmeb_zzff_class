<?php
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
        return $model->where(["{$alias}is_show"=>1]);
    }
    public static function getLabelAttr($value){
        return is_string($value) ? json_decode($value,true) : $value;
    }
}