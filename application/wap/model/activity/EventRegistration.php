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
namespace app\wap\model\activity;

use traits\ModelTrait;
use basic\ModelBasic;
use think\Db;

class EventRegistration extends ModelBasic
{
    use ModelTrait;

    /**活动列表
     * @param int $page
     * @param int $limit
     * @return array
     */
    public static function eventRegistrationList($page=1,$limit=10){
        $list=self::PreWhere()->page((int)$page,(int)$limit)->select();
        $list=count($list)>0 ? $list->toArray() : [];
        foreach ($list as &$v){
            $v=self::singleActivity($v);
            $start_time=date('y/m/d H:i',$v['start_time']);
            $end_time=date('y/m/d H:i',$v['end_time']);
            $v['time']=$start_time.'~'.$end_time;
        }
        return $list;
    }

    /**获取单个活动
     * @param int $id
     */
    public static function oneActivitys($id= false){
        $activity=self::PreWhere()->find($id ? $id : true);
        if($activity){
            $activity=self::singleActivity($activity->toArray());
            $activity['count']=EventSignUp::where('activity_id',$id)->where('paid',1)->count();
            $activity['surplus']=bcsub($activity['number'],$activity['count'],0);
            $activity['surplus']=$activity['surplus']<0 ? 0 : $activity['surplus'];
            if($activity['surplus']<=0) $activity['status']=2;
            $activity['signup_start_time']=date('Y-m-d H:i',$activity['signup_start_time']);
            $activity['signup_end_time']=date('Y-m-d H:i',$activity['signup_end_time']);
            $activity['start_time']=date('Y-m-d H:i',$activity['start_time']);
            $activity['end_time']=date('Y-m-d H:i',$activity['end_time']);
        }
        return $activity;
    }
    /**活动过滤
     * @return EventRegistration
     */
    public static function PreWhere(){
        return self::where('is_show',1)->where('is_del',0)->order('sort DESC,add_time DESC');
    }

    /**判断活动状态
     * @param $activity
     * @return mixed
     */
    public static function singleActivity($activity){
        if(bcsub($activity['signup_start_time'],time(),0)>0){
            $activity['status']=0;//报名尚未开始
        }elseif (bcsub($activity['signup_start_time'],time(),0)<=0 && bcsub($activity['signup_end_time'],time(),0)>0){
            $activity['status']=1;//报名开始
        }elseif (bcsub($activity['signup_end_time'],time(),0)<=0 && bcsub($activity['start_time'],time(),0)>0){
            $activity['status']=2;//报名结束 活动尚未开始
        }elseif (bcsub($activity['start_time'],time(),0)<=0 && bcsub($activity['end_time'],time(),0)>0){
            $activity['status']=3;//活动中
        }elseif (bcsub($activity['end_time'],time(),0)<0){
            $activity['status']=4;//活动结束
        }else{
            $activity['status']=-1;
        }
        return $activity;
    }

}