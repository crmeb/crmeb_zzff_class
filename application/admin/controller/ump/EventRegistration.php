<?php

namespace app\admin\controller\ump;

use app\admin\controller\AuthController;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\ump\EventRegistration as EventRegistrationModel;
use app\admin\model\ump\EventSignUp as EventSignUpModel;

class EventRegistration extends AuthController

{


    /**
     * 分类管理
     * */
     public function index(){

         return $this->fetch();
     }
     public function event_registration_list(){
         $where = Util::getMore([
             ['title',''],
             ['page',1],
             ['limit',20],
         ],$this->request);
         return Json::successlayui(EventRegistrationModel::systemPage($where));
     }
    public function set_show($is_show = '', $id = '')
    {
        if ($is_show == '' || $id == '') return Json::fail('缺少参数');
        if (EventRegistrationModel::update(['is_show' => $is_show], ['id' => $id]))
            return Json::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        else
            return Json::fail($is_show == 1 ? '显示失败' : '隐藏失败');
    }
     public function create(){
         $id = $this->request->param('id');
         $news = [];
         $news['image'] = '';
         $news['qrcode_img'] = '';
         $news['title'] = '';
         $news['start_time'] = '';
         $news['end_time'] = '';
         $news['signup_start_time'] = '';
         $news['signup_end_time'] = '';
         $news['province'] = '';
         $news['city'] = '';
         $news['district'] = '';
         $news['detail'] = '';
         $news['number'] = 0;
         $news['activity_rules'] = '';
         $news['content'] = '';
         $news['sort'] = 0;
         $news['restrictions'] = 0;
         $news['is_fill'] = 1;
         $news['is_show'] = 0;
         $news['pay_type'] = 0;
         $news['price'] = 0;
         $news['member_pay_type'] = 0;
         $news['member_price'] = 0;
         if($id){
             $news = EventRegistrationModel::where('id',$id)->find();
             $news['signup_start_time'] =date('Y-m-d H:i:s',$news['signup_start_time']);
             $news['signup_end_time'] =date('Y-m-d H:i:s',$news['signup_end_time']);
             $news['start_time'] =date('Y-m-d H:i:s',$news['start_time']);
             $news['end_time'] =date('Y-m-d H:i:s',$news['end_time']);
             if(!$news) return $this->failed('数据不存在!');
         }else{
             $id=0;
         }
         $this->assign('news',json_encode($news));
         $this->assign('id',$id);
         return $this->fetch();
         return $this->fetch();
     }
    /**
     * 删除分类
     * */
    public function delete($id)
    {
        $res = EventRegistrationModel::delArticleCategory($id);
        if(!$res)
            return Json::fail(EventRegistrationModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }
    /**
     * 添加和修改图文
     */
    public function add_new(){
        $data = Util::postMore([
            ['id',0],
            'title',
            'image',
            'qrcode_img',
            'activity_rules',
            'content',
            'number',
            'province',
            'city',
            'district',
            'detail',
            'signup_start_time',
            'signup_end_time',
            'start_time',
            'end_time',
            ['sort',0],
            ['restrictions',0],
            ['pay_type',0],
            'price',
            ['member_pay_type',0],
            'member_price',
            ['is_fill',1],
            ['is_show',0],
            ]);
        $data['signup_start_time']=strtotime($data['signup_start_time']);
        $data['signup_end_time']=strtotime($data['signup_end_time']);
        $data['start_time']=strtotime($data['start_time']);
        $data['end_time']=strtotime($data['end_time']);
        if(bcsub($data['signup_end_time'],$data['signup_start_time'],0)<=0 || bcsub($data['start_time'],$data['signup_end_time'],0)<=0 || bcsub($data['end_time'],$data['start_time'],0)<=0) return Json::fail('活动时间有误');
        if(!$data['pay_type']){
            $data['price']=0;
            $data['member_pay_type']=0;
            $data['member_price']=0;
        }
        if(!$data['member_pay_type']) $data['member_price']=0;
        if($data['id']){
            $id = $data['id'];
            unset($data['id']);
            EventRegistrationModel::beginTrans();
            $res1 = EventRegistrationModel::edit($data,$id,'id');
            EventRegistrationModel::checkTrans($res1);
            if($res1){
                return Json::successful('修改活动成功!',$id);
            }else
                return Json::fail('修改活动失败，您并没有修改什么!',$id);
        }else{
            $data['add_time'] = time();
            EventRegistrationModel::beginTrans();
            $res2 = EventRegistrationModel::set($data);
            EventRegistrationModel::checkTrans($res2);
            if($res2)
                return Json::successful('添加活动成功!',$res2->id);
            else
                return Json::successful('添加活动失败!',$res2->id);
        }
    }
    /**
     * 查看报名人员
     */
    public function viewStaff($id){
        $activity = EventRegistrationModel::where('id',$id)->find();
        if(!$activity) return Json::fail('活动不存在!');
        $this->assign('aid',$id);
        return $this->fetch('view_staff');
    }
    public function get_sign_up_list(){
        $id = $this->request->param('id');
        $where=Util::getMore([
            ['id',$id],
            ['page',1],
            ['limit',20],
            ['status',''],
            ['real_name',''],
            ['excel',0],
        ]);
        return Json::successlayui(EventSignUpModel::getUserSignUpAll($where));
    }
    /**用户活动核销
     * @param string $order_id
     * @param int $aid
     * @param string $code
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function scanCodeSignIn($id){
        if (!$id) $this->failed('参数有误！');
        $order=EventSignUpModel::where('id',$id)->find();
        if(!$order) $this->failed('订单不存在！');
        if($order['status']) $this->failed('订单已核销！');
        $res=EventSignUpModel::where('id',$id)->where('paid',1)->update(['status'=>1]);
        if($res) return Json::successful('ok');
        else return Json::fail('核销失败');
    }
}

