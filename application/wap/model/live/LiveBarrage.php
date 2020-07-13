<?php
namespace app\wap\model\live;

/**
 * 直播间评论表
 */

use app\admin\model\system\SystemGroupData;
use basic\ModelBasic;
use service\SystemConfigService;
use traits\ModelTrait;
use app\wap\model\user\User;

class LiveBarrage extends ModelBasic
{

    use ModelTrait;


    public static function getCommentList($uids,$live_id,$page = 0,$limit = 10)
    {
        $model = self::where('live_id',$live_id)->where('is_show',1);
        if($uids) $model = $model->where('uid','in',$uids);
        $list = $model->field('type,barrage as content,uid,live_id,id')->order('add_time desc')->page($page,$limit)->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item){
            $userinfo = User::where('uid',$item['uid'])->field(['nickname','avatar'])->find();
            if($userinfo){
                $item['nickname'] =$userinfo['nickname'];
                $item['avatar'] =$userinfo['avatar'];
            }else{
                $item['nickname'] ='';
                $item['avatar'] ='';
            }
            $type = LiveHonouredGuest::where(['uid'=>$item['uid'],'live_id'=>$item['live_id']])->value('type');
            if(is_null($type))
                $item['user_type'] = 2;
            else
                $item['user_type'] = $type;
            if ($item['type'] == 4) {
                $live_reward_list = LiveReward::where(['id' => $item['content']])->find();
                if ($live_reward_list ? $live_reward_list = $live_reward_list->toArray(): []){
                    $live_gift = SystemGroupData::getDateValue($live_reward_list['gift_id']);
                    $item['content'] = "赠送给主播";
                    $item['gift_num'] = $live_reward_list['gift_num'];
                    $item['gift_image'] = $live_gift ? $live_gift['live_gift_show_img'] : "";
                    $item['gift_name'] = $live_reward_list['gift_name'];
               }
            }
        }
        $page--;
        if(count($list) == 0 || count($list) < $limit){
            $ystemConfig = SystemConfigService::more(['site_name','site_logo']);
            $data = [
                'nickname' => $ystemConfig['site_name'],
                'avatar' => $ystemConfig['site_logo'],
                'user_type' => 2,
                'content'=>LiveStudio::where('id',$live_id)->value('auto_phrase'),
                'id' => 0,
                'type'=>1,
                'uid'=>0
            ];
            array_push($list,$data);
        }
        return ['list'=>$list,'page'=> $page];
    }


}