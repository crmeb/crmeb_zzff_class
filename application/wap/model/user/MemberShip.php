<?php
namespace app\wap\model\user;

use traits\ModelTrait;
use basic\ModelBasic;
use think\Db;
use app\wap\model\user\User;
use app\wap\model\user\MemberRecord;


class MemberShip extends ModelBasic
{
    use ModelTrait;

    public static function membershipList(){
        $list=self::where('is_publish',1)->where('is_del',0)->where('type',1)->where('is_free',0)->order('sort DESC')->select();
        $list=count($list)>0 ? $list->toArray() :[];
        foreach ($list as &$vc){
            $vc['sale']=bcsub($vc['original_price'],$vc['price'],2);
        }
        return $list;
    }

    public static function getUserMember($order,$userInfo){
        $member=self::where('is_publish',1)->where('is_del',0)->where('type',1)->where('id',$order['member_id'])->find();
        $is_permanent=0;
        if($member['is_permanent']){
            $is_permanent=1;
            $overdue_time=0;
        }else {
            switch ($userInfo['level']) {
                case 1:
                    $overdue_time = bcadd(bcmul($member['vip_day'], 86400, 0), $userInfo['overdue_time'], 0);
                    break;
                case 0:
                    $overdue_time = bcadd(bcmul($member['vip_day'], 86400, 0),time(),0);
                    break;
            }
        }
        $data=[
            'oid'=>$order['id'],
            'uid'=>$order['uid'],
            'price'=>$member['price'],
            'validity'=>$member['vip_day'],
            'purchase_time'=>time(),
            'is_permanent'=>$is_permanent,
            'is_free'=>$member['is_free'],
            'overdue_time'=>$overdue_time,
            'add_time'=>time(),
        ];
        $res=MemberRecord::set($data);
        if($res) $res1=User::edit(['level'=>1,'overdue_time'=>$overdue_time,'is_permanent'=>$is_permanent],$order['uid'],'uid');
        $res2=$res && $res1;
        return $res2;
    }

    /**价格最低的会员
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function memberMinOne(){
        return self::where('is_publish',1)->where('is_del',0)->where('type',1)->where('is_free',0)->order('price ASC')->find();
    }

    /**
     * 免费
     */
    public static function memberFree($uid){
        $free=self::where('is_publish',1)->where('is_del',0)->where('type',1)->where('is_free',1)->find();
        $data['free']=count($free) >0 ? $free->toArray() :[];
        $data['is_record'] = 0;
        if($data['free']) {
            $record = MemberRecord::where('uid', $uid)->where('is_free', 1)->find();
            if (count($record)>0) $data['is_record'] = 1;
        }else{
            $data['free']=[];
        }
        return $data;
    }

    /**
     * 会员过期
     */
    public static function memberExpiration(){
        $userList=User::where('level',1)->where('is_permanent',0)->where('overdue_time','<=',time())->select();
        foreach ($userList as $key=>$value){
            $res=User::edit(['level'=>0],$value['uid'],'uid');
            if(!$res) continue;
        }
        return true;
    }
}