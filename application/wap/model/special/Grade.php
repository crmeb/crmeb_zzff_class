<?php
namespace app\wap\model\special;

use basic\ModelBasic;
use traits\ModelTrait;

class Grade extends ModelBasic
{
    use ModelTrait;

    public static function getPickerData(){
        $data=self::order('sort desc')->select();
        $pickdata=[];
        foreach ($data as $item){
            $val['value']=$item['id'];
            $val['text']=$item['name'];
            $pickdata[]=$val;
        }
        return $pickdata;
    }
}