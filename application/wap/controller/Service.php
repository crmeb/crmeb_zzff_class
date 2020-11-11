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

use app\wap\model\store\StoreService;
use service\SystemConfigService;
use app\wap\model\user\User;
use think\Request;

class Service extends AuthController
{
    public function service_list(Request $request){
        $params = Request::instance()->param();
        $merchant =array("id"=>0,"mer_name"=>"");
        $list = StoreService::field('uid,avatar,nickname')->where('mer_id',$merchant['id'])->where('status',1)->order("id desc")->select();
        $this->assign(compact('list','merchant'));
        return $this->fetch();
    }
    public function service_ing(Request $request){
        $params = Request::instance()->param();
        $to_uid = $params['to_uid'];
        $merchant = array("id"=>0,"mer_name"=>"");
        if(!isset($to_uid) || empty($to_uid))$this->failed('未获取到接收用户信息！');
        if($this->userInfo['uid'] == $to_uid)$this->failed('您不能进行自言自语！');

        //发送用户信息
        $now_user = StoreService::where('mer_id',$merchant['id'])->where(array("uid"=>$this->userInfo['uid']))->find();
        if(!$now_user)$now_user = User::getUserInfo($this->userInfo['uid']);
        $this->assign('user',$now_user);

        //接收用户信息
        $to_user = StoreService::where('mer_id',$merchant['id'])->where(array("uid"=>$to_uid))->find();
        if(!$to_user)$to_user = User::getUserInfo($to_uid);
        $this->assign([
            'to_user'=>$to_user,
            'merchant'=>$merchant,
        ]);
        return $this->fetch();
    }
    public function service_new(){
        return $this->fetch();
    }
}