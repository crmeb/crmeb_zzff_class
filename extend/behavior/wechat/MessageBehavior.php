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

namespace behavior\wechat;


use app\admin\model\wechat\WechatMessage;
use app\admin\model\wechat\WechatReply;
use app\wap\model\user\User;
use app\wap\model\user\WechatUser;
use service\HookService;
use service\QrcodeService;

/**
 * 用户消息行为
 * Class MessageBehavior
 * @package behavior\wechat
 */
class MessageBehavior
{
    /**
     * 微信消息前置操作
     * @param $message
     */
    public static function wechatMessageBefore($message)
    {
        WechatUser::saveUser($message->FromUserName);

        $event = isset($message->Event) ?
            $message->MsgType.(
            $message->Event == 'subscribe' && isset($message->EventKey) ? '_scan' : ''
            ).'_'.$message->Event : $message->MsgType;
        WechatMessage::setMessage(json_encode($message),$message->FromUserName,strtolower($event));
    }
    /*
     * 微信自定义菜单点击
     * */
    public static function wechatEventView($message)
    {
        //User::ResetSpread($message->FromUserName);//重置用户上级
    }

    /**
     * 用户文字消息
     * @param $message
     * @return array|\EasyWeChat\Message\Image|\EasyWeChat\Message\News|\EasyWeChat\Message\Text|\EasyWeChat\Message\Voice
     */
    public static function wechatMessageText($message)
    {
        return WechatReply::reply($message->Content);
    }

    /**
     * 用户文字消息前置操作
     * @param $message
     */
    public static function wechatMessageTextBefore($message)
    {
    }

    /**
     * 用户关注行为
     * @return array|\EasyWeChat\Message\Image|\EasyWeChat\Message\News|\EasyWeChat\Message\Text|\EasyWeChat\Message\Voice
     */
    public static function wechatEventSubscribe($message)
    {
        return WechatReply::reply('subscribe');
    }

    /**
     * 用户关注前置操作
     * @param $message
     */
    public static function wechatEventSubscribeBefore($message)
    {
//        WechatUser::saveUser($message->FromUserName);
    }

    /**
     * 微信菜单点击事件
     * @param $message
     * @return array|\EasyWeChat\Message\Image|\EasyWeChat\Message\News|\EasyWeChat\Message\Text|\EasyWeChat\Message\Voice
     */
    public static function wechatEventClick($message)
    {
        return WechatReply::reply($message->EventKey);
    }

    /**
     * 微信菜单click点击前置操作
     * @param $message
     */
    public static function wechatEventClickBefore($message)
    {

    }


    /**
     * 用户扫码关注
     * TODO 处理二维码携带参数
     * @param $message
     */
    public static function wechatEventScanSubscribe($message, $eventKey = '')
    {
        if ($eventKey && ($qrInfo = QrcodeService::getQrcode($message->Ticket, 'ticket'))) {
            QrcodeService::scanQrcode($message->Ticket, 'ticket');
            return HookService::resultListen('wechat_qrcode_' . strtolower($qrInfo['third_type']), $qrInfo, $message, true, QrcodeEventBehavior::class);
        }
        return WechatReply::reply('');
    }

    /**
     * 用户扫码
     * TODO 处理二维码携带参数
     * @param $message
     */
    public static function wechatEventScan($message)
    {
        if ($message->EventKey && ($qrInfo = QrcodeService::getQrcode($message->Ticket, 'ticket'))) {
            QrcodeService::scanQrcode($message->Ticket, 'ticket');
            return HookService::resultListen('wechat_qrcode_' . strtolower($qrInfo['third_type']), $qrInfo, $message, true, QrcodeEventBehavior::class);
        }
        return WechatReply::reply('');
    }

    public static function wechatEventUnsubscribeBefore($message)
    {
        WechatUser::unSubscribe($message->FromUserName);
    }

}