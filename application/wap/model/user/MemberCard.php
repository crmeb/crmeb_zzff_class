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
namespace app\wap\model\user;

use service\SystemConfigService;
use service\UtilService;
use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\wap\model\user\User;
use app\wap\model\user\MemberCardBatch;//会员卡批次

class MemberCard extends ModelBasic
{
    use ModelTrait;

   public static function confirmActivation($data,$user){
        $code=self::where('card_number',$data['member_code'])->where('status',1)->find();
        if(!$code) return self::setErrorInfo('会员卡不存在!');
        if($code['card_password']!=$data['member_pwd']) return self::setErrorInfo('会员卡密码有误!');
        if($code['use_uid'] && $code['use_time']) return self::setErrorInfo('会员卡已使用!');
        if($user['level'] && $user['is_permanent']) return self::setErrorInfo('您已是永久会员，无需使用会员卡!');
        $res=self::edit(['use_uid'=>$user['uid'],'use_time'=>time()],$code['id'],'id');
        $batch=MemberCardBatch::where('id',$code['card_batch_id'])->find();
        if(!$batch['status']) return self::setErrorInfo('会员卡未激活!');
        if($res && $batch) $res1=MemberCardBatch::edit(['use_num'=>bcadd($batch['use_num'],1,0)],$code['card_batch_id'],'id');
        if($res1) {
            switch ($user['level']) {
                case 1:
                    $overdue_time = bcadd(bcmul($batch['use_day'], 86400, 0), $user['overdue_time'], 0);
                    break;
                case 0:
                    $overdue_time = bcadd(bcmul($batch['use_day'], 86400, 0),time(),0);
                    break;
            }
            $data=[
                'oid'=>0,
                'uid'=>$user['uid'],
                'type'=>1,
                'code'=>$data['member_code'],
                'price'=>0,
                'validity'=>$batch['use_day'],
                'purchase_time'=>time(),
                'is_permanent'=>0,
                'is_free'=>0,
                'overdue_time'=>$overdue_time,
                'add_time'=>time(),
            ];
            $res4=MemberRecord::set($data);
           if($res4) $res2=User::edit(['level'=>1,'overdue_time'=>$overdue_time,'is_permanent'=>0],$user['uid'],'uid');
        }
        $res3=$res && $res1 && $res2;
        return $res3;
   }


}