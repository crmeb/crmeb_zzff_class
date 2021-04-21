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

namespace  app\wap\model\routine;

use app\wap\model\store\StoreOrder;
use app\wap\model\user\WechatUser;
use service\RoutineTemplateService;
use app\wap\model\special\Special;
use app\admin\model\wechat\StoreService as ServiceModel;
use app\admin\model\wechat\RoutineTemplate as RoutineTemplateModel;

/**
 * 发送订阅消息
 * Class RoutineTemplate
 * @package app\wap\model\routine
 */
class RoutineTemplate{

    /**
     * 订单支付成功发送模板消息
     * @param string $formId
     * @param string $orderId
     */
    public static function sendOrderSuccess(array $data,$uid,$link=''){
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid),RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_PAY_SUCCESS),$link,$data);
    }
    /**管理员通知
     * @param array $data
     * @param null $url
     * @param string $defaultColor
     * @return bool
     */
    public static function sendAdminNoticeTemplate(array $data,$url = null,$defaultColor = '')
    {
        $kefuIds = ServiceModel::where('notify',1)->column('uid');
        $adminList = array_unique($kefuIds);
        if(!is_array($adminList) || empty($adminList)) return false;
        foreach ($adminList as $uid){
            try{
                $openid = WechatUser::uidToOpenid($uid);
            }catch (\Exception $e){
                continue;
            }
            RoutineTemplateService::sendTemplate($openid,RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_PAY_SUCCESS),'',$data);
        }
    }
    /**
     * 账户变动订阅消息
     * $userinfo 用户消息
     * */
    public static function sendAccountChanges(array $data,$uid,$link=''){
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid),RoutineTemplateService::setTemplateId(RoutineTemplateService::USER_BALANCE_CHANGE),$link,$data);
    }
    /**
     * 退款成功发送消息
     * @param array $order
     */
    public static function sendOrderRefundSuccess($data = array(),$uid,$link=''){
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid),RoutineTemplateService::setTemplateId(RoutineTemplateService::ORDER_REFUND_STATUS),$link,$data);
    }

    /**开播提醒
     * @param array $data
     * @param $uid
     * @param string $link
     */
    public static function sendBroadcastReminder($data = array(),$uid,$link=''){
        RoutineTemplateService::sendTemplate(WechatUser::uidToOpenid($uid),RoutineTemplateService::setTemplateId(RoutineTemplateService::LIVE_BROADCAST),$link,$data);
    }

    /**获取用户相关的订阅消息模版ID
     * @param $type
     * @param int $id
     * @return string
     */
    public static function getTemplateIdList($type,$id=0)
    {
        $list= RoutineTemplateModel::create_template($type,$id);
        $templateIds=implode(',',$list);
        return $templateIds;
    }
}
