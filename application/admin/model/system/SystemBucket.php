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


namespace app\admin\model\system;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class SystemBucket
 * @package app\admin\model\system
 */
class SystemBucket extends ModelBasic
{
    use ModelTrait;
    const endpoint='.aliyuncs.com';

    /**获取数据表中的储存空间信息
     * @param $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function bucKetList($where){
        $model=new self();
        if($where['endpoint']!='') $model=$model->where('endpoint',$where['endpoint']);
        $list=$model->where('is_del',0)->order('add_time desc')->select();
        return $list;
    }

    /**
     * 拉取保存存储空间
     *  @param $list
     */
    public static function addListBucket($list=[])
    {
        foreach ($list as $key=>$value){
            $data=[
                'bucket_name'=>$value->getName(),
                'endpoint'=>$value->getLocation().self::endpoint,
                'domain_name'=>$value->getName().'.'.$value->getLocation().self::endpoint,
                'creation_time'=>$value->getCreatedate(),
                'add_time'=>time()
            ];
            if(!self::be(['bucket_name'=>$data['bucket_name']])){
                self::set($data);
            }
        }
        return true;
    }
    /**
     * 保存存储空间
     *  @param $value
     */
    public static function addBucket($value=[])
    {
        $data=[
            'bucket_name'=>$value['bucket_name'],
            'endpoint'=>$value['endpoint'],
            'domain_name'=>$value['bucket_name'].'.'.$value['endpoint'],
            'creation_time'=>date('Y/m/d H:i',time()),
            'add_time'=>time()
        ];
        $res=true;
        if(!self::be(['bucket_name'=>$data['bucket_name']])){
            $res=self::set($data);
        }
        return $res;
    }
}
