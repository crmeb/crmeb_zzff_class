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

namespace app\wap\controller;

use app\wap\model\activity\EventRegistration;
use app\wap\model\activity\EventSignUp;
use basic\WapBasic;
use think\Db;
use service\JsonService;
use service\SystemConfigService;
use service\UtilService;
use think\Cookie;
use think\exception\HttpException;
use think\response\Json;
use think\Session;
use think\Url;
/**
 * 文章分类控制器
 * Class Article
 * @package app\wap\controller
 */
class Activity extends AuthController
{

    /*
  * 白名单
  * */
    public static function WhiteList()
    {
        return [
            'details',
            'index',
            'activityList'
        ];
    }

    public function index()
    {

        return $this->fetch('activity_list');
    }

    /**
     * 活动列表
     */
    public function activityList($page=1,$limit=20){
        $list=EventRegistration::eventRegistrationList($page,$limit);
        foreach ($list as &$value){
            $value['count']=EventSignUp::where('activity_id',$value['id'])->where('paid',1)->count();
        }
        return JsonService::successful($list);
    }

    /**
     * 活动扫码
     */
    public function scanningCode($order_id=''){
        if (!$order_id) $this->failed('参数有误！');
        $order=EventSignUp::where('order_id',$order_id)->find();
        if(!$order) $this->failed('订单不存在！');
        $activity=EventRegistration::where('id',$order['activity_id'])->field('title,image,phone,province,city,district,detail')->find();
        if(!$activity) $this->failed('活动不存在！');
        $activity['order_id']=$order_id;
        return JsonService::successful($activity);
    }

    /**用户活动核销
     * @param string $order_id
     * @param int $aid
     * @param string $code
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function scanCodeSignIn($type,$order_id=''){
        if (!$order_id || !$type) $this->failed('参数有误！');
        $order=EventSignUp::where('order_id',$order_id)->find();
        if(!$order) $this->failed('订单不存在！');
        if($order['status']) $this->failed('该订单已核销！');
        $res=EventSignUp::where('order_id',$order_id)->where('paid',1)->update(['status'=>1]);
        if($res) return JsonService::successful('核销成功');
        else return JsonService::fail('核销失败');
    }

    /**
     * 用户报名活动列表
     */
    public function  activitySignInList($page=1,$limit=20,$navActive=0){
        $uid=$this->userInfo['uid'];
        $model=EventSignUp::where('uid',$uid)->where('paid',1)->page((int)$page,(int)$limit);
        switch ($navActive){
            case 1:
               $model=$model->where('status',0);
               break;
            case 2:
                $model=$model->where('status',1);
              break;
        }
        $orderList=$model->order('add_time DESC')->field('order_id,status,pay_price,activity_id,user_info,uid')->select();
        $orderList=count($orderList)>0 ? $orderList->toArray() : [];
        foreach ($orderList as &$item){
            $activity=EventRegistration::where('id',$item['activity_id'])->find();
            $item['activity']=EventRegistration::singleActivity($activity);
        }
        return JsonService::successful($orderList);
    }

    /**活动订单详情
     * @param string $order_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function activitySignIn($order_id=''){
        if (!$order_id) $this->failed('参数有误！');
        $order=EventSignUp::where('order_id',$order_id)->find();
        if(!$order) $this->failed('订单不存在！');
        if($order['activity_id']){
            $activity=EventRegistration::where('id',$order['activity_id'])->field('title,image,province,city,district,detail,start_time,end_time,signup_start_time,signup_end_time,price')->find();
            if(!$activity) $this->failed('活动不存在！');
            $activity=EventRegistration::singleActivity($activity);
            $start_time=date('y/m/d H:i',$activity['start_time']);
            $end_time=date('y/m/d H:i',$activity['end_time']);
            $activity['time']=$start_time.'~'.$end_time;
            $order['activity']=$activity;
        }else{
            $this->failed('活动不存在！');
        }
        $order['pay_time']=date('y/m/d H:i',$order['pay_time']);
        if(!$order['write_off_code']){
            $write_off_code=EventSignUp::qrcodes_url($order_id,5);
            EventSignUp::where('order_id',$order_id)->update(['write_off_code'=>$write_off_code]);
            $order['write_off_code']=$write_off_code;
        }
        return JsonService::successful($order);
    }

    /**检测活动状态
     * @param string $order_id
     */
    public function orderStatus($order_id=''){
        if (!$order_id) $this->failed('参数有误！');
        $order=EventSignUp::where('order_id',$order_id)->where('paid',1)->find();
        if(!$order) $this->failed('订单不存在！');
        if($order['status']){
            return JsonService::successful('ok');
        }else{
            return JsonService::fail('error');
        }
    }
}