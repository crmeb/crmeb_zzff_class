<?php

namespace app\admin\controller\user;

use app\admin\controller\AuthController;
use service\JsonService;
use service\UtilService;
use service\QrcodeService;
use traits\CurdControllerTrait;
use app\admin\model\user\User;
use app\admin\model\user\UserBill;
use app\admin\model\wechat\WechatUser as UserModel;
use app\admin\model\order\StoreOrder;
/**
 * 用户推广员管理
 * Class UserSpread
 * @package app\admin\controller\user
 */
class UserSpread extends AuthController
{
    use CurdControllerTrait;

    public function index()
    {
        $this->assign( 'year',getMonth());
        return $this->fetch();
    }

    public function spread_list()
    {
        $where=UtilService::getMore([
            ['nickname',''],
            ['start_time',''],
            ['end_time',''],
            ['sex',''],
            ['excel',''],
            ['subscribe',''],
            ['order',''],
            ['page',1],
            ['limit',20],
            ['user_type',''],
        ]);
        return JsonService::successlayui(UserModel::agentSystemPage($where));
    }

    public function get_badge_list()
    {
        $where = UtilService::postMore([
            ['data',''],
            ['nickname',''],
            ['excel',''],
        ]);
        return JsonService::successful(UserModel::getSpreadBadge($where));
    }
    /**
     * 一级推荐人页面
     * @return mixed
     */
    public function stair($uid = ''){
        if($uid == '') return $this->failed('参数错误');
        $this->assign('uid',$uid ? : 0);
        $this->assign( 'year',getMonth());
        return $this->fetch();
    }
    /*
    *  统计推广订单
    * @param int $uid
    * */
    public function stair_order($uid = 0)
    {
        if($uid == '') return $this->failed('参数错误');
        $this->assign('uid',$uid ? : 0);
        $this->assign( 'year',getMonth());
        return $this->fetch();
    }

    public function get_stair_order_list(){
        $where = UtilService::getMore([
            ['uid',$this->request->param('uid',0)],
            ['data',''],
            ['order_id',''],
            ['type',''],
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(UserModel::getStairOrderList($where));
    }

    public function get_stair_order_badge()
    {
        $where = UtilService::getMore([
            ['uid',''],
            ['data',''],
            ['order_id',''],
            ['type',''],
        ]);
        return JsonService::successful(UserModel::getStairOrderBadge($where));
    }

    public function get_stair_list()
    {
        $where = UtilService::getMore([
            ['uid',$this->request->param('uid',0)],
            ['data',''],
            ['nickname',''],
            ['type',''],
            ['page',1],
            ['limit',20],
        ]);
        return JsonService::successlayui(UserModel::getStairList($where));
    }

    public function get_stair_badge()
    {
        $where = UtilService::getMore([
            ['uid',''],
            ['data',''],
            ['nickname',''],
            ['type',''],
        ]);
        return JsonService::successful(UserModel::getSairBadge($where));
    }

    /**
     * 二级推荐人页面
     * @return mixed
     */
    public function stair_two($uid = '')
    {
        if($uid == '') return $this->failed('参数错误');
        $spread_uid=User::where('spread_uid',$uid)->column('uid','uid');
        if(count($spread_uid))
            $spread_uid_two=User::where('spread_uid','in',$spread_uid)->column('uid','uid');
        else
            $spread_uid_two=[0];
        $list = User::alias('u')
            ->where('u.uid','in',$spread_uid_two)
            ->field('u.avatar,u.nickname,u.now_money,u.spread_time,u.uid')
            ->where('u.status',1)
            ->order('u.add_time DESC')
            ->select()
            ->toArray();
        foreach ($list as $key=>$value) $list[$key]['orderCount'] = StoreOrder::getOrderCount($value['uid'])?:0;
        $this->assign('list',$list);
        return $this->fetch('stair');
    }

    /*
     * 批量清除推广权限
     * */
    public function delete_promoter()
    {
        list($uids)=UtilService::postMore([
            ['uids',[]]
        ],$this->request,true);
        if(!count($uids)) return JsonService::fail('请选择需要解除推广权限的用户！');
        User::beginTrans();
        try{
            if(User::where('uid','in',$uids)->update(['is_promoter'=>0])){
                User::commitTrans();
                return JsonService::successful('解除成功');
            }else{
                User::rollbackTrans();
                return JsonService::fail('解除失败');
            }
        }catch (\PDOException $e){
            User::rollbackTrans();
            return JsonService::fail('数据库操作错误',['line'=>$e->getLine(),'message'=>$e->getMessage()]);
        }catch (\Exception $e){
            User::rollbackTrans();
            return JsonService::fail('系统错误',['line'=>$e->getLine(),'message'=>$e->getMessage()]);
        }

    }

    /*
     * 查看公众号推广二维码
     * @param int $uid
     * @return json
     * */
    public function look_code($uid='',$action='')
    {
        if(!$uid || !$action) return JsonService::fail('缺少参数');
        try{
            if(method_exists($this,$action)){
                $res = $this->$action($uid);
                if($res)
                    return JsonService::successful($res);
                else
                    return JsonService::fail(isset($res['msg']) ? $res['msg'] : '获取失败，请稍后再试！' );
            }else
                return JsonService::fail('暂无此方法');
        }catch (\Exception $e){
            return JsonService::fail('获取推广二维码失败，请检查您的微信配置',['line'=>$e->getLine(),'messag'=>$e->getMessage()]);
        }
    }


    /*
     * 获取公众号二维码
     * */
    public function wechant_code($uid)
    {
        $qr_code = QrcodeService::getForeverQrcode('spread',$uid);
        if(isset($qr_code['url']))
            return ['code_src'=>$qr_code['url']];
        else
            throw new \think\Exception('获取失败，请稍后再试！');
    }

    /*
     * 解除单个用户的推广权限
     * @param int $uid
     * */
    public function delete_spread($uid=0)
    {
        if(!$uid) return JsonService::fail('缺少参数');
        if(User::where('uid',$uid)->update(['is_promoter'=>0]))
            return JsonService::successful('解除成功');
        else
            return JsonService::fail('解除失败');
    }

    /*
     * 清除推广人
     * */
    public function empty_spread($uid=0)
    {
        if(!$uid) return JsonService::fail('缺少参数');
        $res =  User::where('uid',$uid)->update(['spread_uid'=>0]);
        if($res)
            return JsonService::successful('清除成功');
        else
            return JsonService::fail('清除失败');
    }
    /**
     * 个人资金详情页面
     * @return mixed
     */
    public function now_money($uid = ''){
        if($uid == '') return $this->failed('参数错误');
        $list = UserBill::where('uid',$uid)->where('category','now_money')
            ->field('mark,pm,number,add_time')
            ->where('status',1)->order('add_time DESC')->select()->toArray();
        foreach ($list as &$v){
            $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
}