<?php
namespace app\admin\model\order;

use service\PHPExcelService;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\User;

/**
 * 跑腿小哥管理Model
 * Class DeliveryMember
 * @package app\admin\model\store
 */
class DeliveryMember extends ModelBasic
{
    use ModelTrait;

    public static function delivery_list($where){
        $list=self::setWhere($where,'a')->join('__USER__ u','u.uid=a.uid','LEFT')->alias('a')
            ->field(['a.*','u.nickname'])->page((int)$where['page'],(int)$where['limit'])->select();
        $data=count($list) ? $list->toArray() : [];
        foreach ($data as &$item){
            $item['add_time']=date('Y-m-d H:i:s',$item['add_time']);
            if($item['sex']==0){
                $item['sex_name']='未知';
            }else if($item['sex']==1){
                $item['sex_name']='男';
            }else if($item['sex']==2){
                $item['sex_name']='女';
            }
            switch ($item['status']){
                case -2:
                    $item['status_name']='禁止';
                    break;
                case -1:
                    $item['status_name']='审核失败';
                    break;
                case 0:
                    $item['status_name']='审核中';
                    break;
                case 1:
                    $item['status_name']='正常';
                    break;
            }
        }
        $count=self::setWhere($where)->count();
        return compact('data','count');
    }
    /*
     * 设置查询条件
     * $where 查询数组
     * $alias 别名
     * $model 模型
     * */
    public static function setWhere($where,$alias='',$model=null){
        if($model===null) $model=new self();
        if($alias) $alias.='.';
        if($where['sex']!='') $model->where($alias.'sex',$where['sex']);
        if($where['status']!='') $model->where($alias.'status',$where['status']);
        if($where['real_name']!='') $model->where($alias.'real_name|'.$alias.'phone|'.$alias.'school|'.$alias.'major',$where['real_name']);
        if($where['start_time']!='' && $where['end_time']!='') $model->where($alias.'add_time','between',[strtotime($where['start_time']),strtotime($where['end_time'])]);
        return $model->where($alias.'is_del',0);
    }
}