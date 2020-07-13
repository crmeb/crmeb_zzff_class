<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2018/02/28
 */

namespace app\wap\model\user;


use service\SystemConfigService;
use basic\ModelBasic;
use service\WechatService;
use traits\ModelTrait;
class UserSign extends ModelBasic
{
    use ModelTrait;

    public static function checkUserSigned($uid)
    {
        return UserBill::be(['uid'=>$uid,'add_time'=>['>',strtotime('today')],'category'=>'gold_num','type'=>'sign']);
    }

    public static function userSignedCount($uid)
    {
        return self::userSignBillWhere($uid)->count();
    }

    /**
     * @param $uid
     * @return Model
     */
    public static function userSignBillWhere($uid)
    {
        return UserBill::where(['uid'=>$uid,'category'=>'gold_num','type'=>'sign']);
    }

    /**近期用户签到记录
     * @param $uid
     */
    public static function userSignInlist($uid,$page,$limit){
        $list=self::userSignBillWhere($uid)->field('number,add_time')->order('add_time DESC')
            ->page((int)$page,(int)$limit)->select();
         $list=count($list) >0 ? $list->toArray() : [] ;
         foreach ($list as &$value){
             $value['number']=(int)$value['number'];
             $value['add_time']=date('Y-m-d H:i:s',$value['add_time']);
         }
         return $list;
    }
    public static function sign($userInfo,$gold_name)
    {
        $uid = $userInfo['uid'];
        $gold_coin= SystemConfigService::get('single_gold_coin')?:0;
        $balance=bcadd($gold_coin,$userInfo['gold_num'],0);
        self::beginTrans();
        $res1 = UserBill::income('用户签到',$uid,'gold_num','sign',$gold_coin,0,$balance,'签到获得'.floatval($gold_coin).$gold_name);
        $res2 = User::bcInc($uid,'gold_num',$gold_coin,'uid');
        $res3=self::userSign($gold_coin,$uid,$balance);
        $res = $res1 && $res2 && $res3;
        self::checkTrans($res);
        if($res)
            return $gold_coin;
        else
            return false;
    }
    public static function userSign($gold_coin,$uid,$balance){
        $data=[
            'uid'=>$uid,
            'title'=>'签到奖励',
            'number'=>$gold_coin,
            'balance'=>$balance,
            'add_time'=>time()
        ];
        return self::set($data);
    }
}