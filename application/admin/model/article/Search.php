<?php
namespace app\admin\model\article;

use traits\ModelTrait;
use basic\ModelBasic;
/**
 * 关键词 Model
 * Class WechatNews
 * @package app\admin\model\article
 */
class Search extends ModelBasic {

    use ModelTrait;

    public static function saveSearch($name){
        if(!self::be(['name'=>$name])){
            if($res=self::set(['name'=>$name,'add_time'=>time()]))
                return $res;
            else
                return self::setErrorInfo('添加失败');
        }else
            return self::setErrorInfo('请勿重复添加');
    }
    public static function getAll(){
        return self::order('add_time desc')->select()->toArray();
    }
}