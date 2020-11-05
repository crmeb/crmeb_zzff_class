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
namespace app\wap\model\special;

use app\wap\model\live\LiveStudio;
use basic\ModelBasic;
use traits\ModelTrait;
use think\Db;

class SpecialWatch extends ModelBasic
{
    use ModelTrait;

    /**
     * 素材观看时间
     */
    public static function materialViewing($uid,$special_id=0,$task_id=0,$time=0){
        $watch=Db::name('special_watch')->where(['uid'=>$uid,'special_id'=>$special_id,'task_id'=>$task_id])->find();
       if($watch) return false;
        $data=[
            'uid'=>$uid,
            'special_id'=>$special_id,
            'task_id'=>$task_id,
            'viewing_time'=>$time,
            'add_time'=>time()
        ];
        $res=Db::name('special_watch')->insert($data);
        return $res;
    }

    /**
     * 查看素材是否观看
     */
    public static function whetherWatch($uid,$special_id=0,$task_id=0){
        $watch=Db::name('special_watch')->where(['uid'=>$uid,'special_id'=>$special_id,'task_id'=>$task_id])->find();
        if($watch) return true;
        else return false;
    }

}