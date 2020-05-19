<?php
namespace app\admin\model\user;

use service\SystemConfigService;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\User;
use app\admin\model\user\UserBill;
use app\admin\model\user\Group as GroupModel;
/**
 * 用户管理 model
 * Class User
 * @package app\admin\model\user
 */

class Member extends ModelBasic
{
    use ModelTrait;

    public static function getShareUid(){
        $share=self::where('is_end',0)->field('member_id')->select();
        $share_uid=[];
        foreach ($share as $item){
            $member_id=$item['member_id'] ? explode(',',$item['member_id']) : [];
            if(count($member_id)){
//                array_shift($member_id);
                foreach ($member_id as $uid){
                    $share_uid[]=$uid;
                }
            }
        }
        return $share_uid;
    }
    /*
     * 普通小组开始分化新组
     * $member 小组成员uid
     * $reward_money 奖励金
     * */
    public static function setGroup($member,$reward_money){
        $member_id=$member;
        //不是初始化小组,直接开始新小组
        //1小组店长
        $one = array_shift($member_id);
        //2小组店长
        $two = array_shift($member_id);
        //1小组副店长
        $one_vice = [array_shift($member_id), array_shift($member_id)];
        //2小组副店长
        $two_vice = $member_id;
        $data = [
            [
                'uid' => $one,
                'member_id' => $one . (is_array($one_vice) ? ',' . implode(',', $one_vice) : ''),
                'add_time' => time(),
            ],
            [
                'uid' => $two,
                'member_id' => $two . (is_array($two_vice) ? ',' . implode(',', $two_vice) : ''),
                'add_time' => time(),
            ],
        ];
        //记录当前用户所在店铺
        GroupModel::saveGroupAndShop($one,$one);
        //修改用店长
        GroupModel::saveGroupAndShop($two,$two);
        //修改副店长的店长信息
        self::saveNowShop($one_vice,$one);
        self::saveNowShop($two_vice,$two);
        self::insertAll($data);
    }
    public static function saveNowShop($uids,$shop_uid){
        if(is_array($uids)){
            foreach ($uids as $uid){
                GroupModel::saveGroupAndShop($uid,$shop_uid);
            }
        }else{
            GroupModel::saveGroupAndShop($uids,$shop_uid);
        }
    }
    /*
     * 初始化小组新开组
     *  $one_start 新开组用户uid
     * $reward_money 组长达标提成金额
     * */
    public static function setOneStart($one_start){
        $maker_spread_money=SystemConfigService::get('maker_spread_money');
        User::bcInc($one_start,'now_money',$maker_spread_money);
        $status=Group::where('share_uid',$one_start)->count() ? 1 : 0;
        if(UserBill::be(['uid'=>$one_start,'category'=>'now_money','type'=>'rake_back','status'=>0]) && $status==1){
            UserBill::where(['uid'=>$one_start,'category'=>'now_money','type'=>'rake_back','status'=>0])->update(['status'=>1]);
        }
        UserBill::income('组长达标提成',$one_start,'now_money','rake_back',$maker_spread_money,0,0,'组长达标提成'.(float)$maker_spread_money.'元',$status);
        self::set(['uid'=>$one_start,'member_id'=>$one_start,'add_time'=>time(),'is_start'=>1]);
        GroupModel::saveGroupAndShop($one_start,$one_start);
    }
    /*
     * 普通用户回归分享人店铺
     *  $shop_uid 店铺uid
     *  $uid 用户id
     *  $maxNumber 几人小组
     *  $reward_money 组长达标提成金额
     * */
    public static function regressGroup($shop_uid,$uid,$maxNumber,$reward_money){
        $regress_money=SystemConfigService::get('regress_money');
        $member=Member::where(['is_end'=>0,'uid'=>$shop_uid])->find();
        if(!$member) return false;
        $member_id=$member->member_id=$member->member_id ? explode(',',$member->member_id) : [];
        array_push($member_id,$uid);
        $member->is_end = count($member_id) >= $maxNumber ? 1: 0;
        //尚未结束直接添加
        $member->member_id = implode(',',$member_id);
        if($member->is_end){
            //是初始化小组的从新开组
            if($member->is_start){
                $one_start=array_shift($member_id);
                //如果是初始小组取第一个从新开组
                self::setOneStart($one_start,$reward_money);
            }else{
                //如果不是初始小组,而且当前小组已经满7人将小组第一人踢出,去找分享人
                $one_start=array_shift($member_id);
                //店长达成金额
                $maker_spread_money=SystemConfigService::get('maker_spread_money');
                User::bcInc($one_start,'now_money',$maker_spread_money);
                $status=Group::where('share_uid',$one_start)->count() ? 1 : 0;
                if(UserBill::be(['uid'=>$one_start,'category'=>'now_money','type'=>'rake_back','status'=>0]) && $status==1){
                    UserBill::where(['uid'=>$one_start,'category'=>'now_money','type'=>'rake_back','status'=>0])->update(['status'=>1]);
                }
                UserBill::income('组长达标提成',$one_start,'now_money','rake_back',$maker_spread_money,0,0,'组长达标提成'.(float)$maker_spread_money.'元',$status);
                //当前分享人的店长uid
                self::regressGroup(GroupModel::setMember($one_start),$one_start,$maxNumber,$reward_money);
            }
            //处理小组关系
            self::setGroup($member_id,$reward_money);
        }
        GroupModel::saveGroupAndShop($uid,$shop_uid);
        $share_uid=GroupModel::where('uid',$uid)->value('share_uid');
        User::bcInc($share_uid,'now_money',$regress_money);
        UserBill::income('客户达标提成',$share_uid,'now_money','rake_back',$regress_money,$uid,0,'客户达标提成'.(float)$regress_money.'元');
        return $member->save();
    }

    public static function getGroup($uid){
        $shop_uid=\think\Db::name('group_of_members')->where('uid',$uid)->where('is_del',0)->value('shop_uid');
        $member=self::where('uid',$shop_uid)->where('is_end',0)->find();
        $member->member_id= $member->member_id ? explode(',',$member->member_id) : [];
        $member_list=[];
        foreach ($member->member_id as $key=>$item) {
            $member_list[]=GroupModel::where('uid', $item)->field(['phone', 'user_name','share_uid','share_name','full_name','add_time'])->find();
        }
        return compact('member_list');
    }


}