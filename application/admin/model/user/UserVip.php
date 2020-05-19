<?php
namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;
/**
 * 会员用户管理 model
 * Class User
 * @package app\admin\model\user
 */

class UserVip extends ModelBasic
{
    use ModelTrait;

    public static function setWhere($where){
        $model=self::alias('a')
            ->join('user u','a.uid=u.uid','left')
            ->join('system_vip p','p.id=a.vip_id','left')
            ->where('a.mer_id',0)
            ->where('a.is_del',0)
            ->order('a.id desc');
        if($where['vip_id']!='') $model->where('a.vip_id',$where['vip_id']);
        if($where['status']!='') $model->where('a.status',$where['status']);
        if($where['is_forever']!='') $model->where('a.is_forever',$where['is_forever']);
        if($where['title']!='') $model->where('a.nickname|p.title','like',"%$where[title]%");
        return $model;
    }

    public static function getUserVipList($where){
        $data=self::setWhere($where)->field(['a.add_time as vip_time','u.nickname','p.is_forever','a.id','a.status',
            'p.title','p.discount','p.valid_date'])->page((int)$where['page'],(int)$where['limit'])->select();
        count($data) && $data=$data->toArray();
        foreach ($data as &$item){
            $item['vip_time']=date('Y-m-d H:i:s',$item['vip_time']);
            $item['valid_date']=$item['is_forever']==1 ? '永久时效' : $item['valid_date'];
            $item['is_forever']=$item['is_forever']==0 ? '非永久会员' : '永久会员';
        }
        $count=self::setWhere($where)->count();
        return compact('data','count');
    }
}