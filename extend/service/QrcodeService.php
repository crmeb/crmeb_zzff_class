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

namespace service;


use app\admin\model\wechat\WechatQrcode as QrcodeModel;

class QrcodeService
{
    /**
     * 获取临时二维码  单个
     * */
     public static function getTemporaryQrcode($type,$id){
         return QrcodeModel::getTemporaryQrcode($type,$id)->toArray();
     }/**
     * 获取永久二维码  单个
     * */
     public static function getForeverQrcode($type,$id){
         return QrcodeModel::getForeverQrcode($type,$id)->toArray();
     }

     public static function getQrcode($id,$type = 'id')
     {
        return QrcodeModel::getQrcode($id,$type);
     }

     public static function scanQrcode($id,$type = 'id')
     {
         return QrcodeModel::scanQrcode($id,$type);
     }
}