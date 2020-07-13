<?php
namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;


class SignPoster extends ModelBasic
{
    use ModelTrait;


    public static function getSignPosterList($where){
        $data=self::page((int)$where['page'],(int)$where['limit'])->order('sort DESC,add_time DESC')->select();
        count($data) && $data=$data->toArray();
        foreach ($data as &$item){
            $item['sign_time']=date('Y-m-d',$item['sign_time']);
        }
        $count=self::count();
        return compact('data','count');
    }
}