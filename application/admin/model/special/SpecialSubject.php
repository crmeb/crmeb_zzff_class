<?php


namespace app\admin\model\special;



use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class SpecialSubject 科目
 * @package app\admin\model\special
 */
class SpecialSubject extends ModelBasic
{
    use ModelTrait;

    public static function get_subject_list($where){
        $data=self::setWhere($where)->page((int)$where['page'],(int)$where['limit'])->select();
        foreach ($data as &$item){
            $item['grade_name']=Grade::where('id',$item['grade_id'])->value('name');
            $item['add_time']=date('Y-m-d H:i:s',$item['add_time']);
            $item['special_count']=Special::PreWhere()->where('subject_id',$item['id'])->count();
        }
        $count=self::setWhere($where)->count();
        return compact('data','count');
    }

    public static function setWhere($where){
        $model=self::order('sort desc,add_time desc');
        if($where['name']) $model=$model->where('name','like',"%$where[name]%");
        if($where['cid']) $model=$model->where('grade_id',$where['cid']);
        return $model;
    }

    public static function getSubjectAll(){
        return self::order('sort desc,add_time desc')->field('name,id')->select();
    }
}