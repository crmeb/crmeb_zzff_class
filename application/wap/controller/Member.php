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
use service\CacheService;
use service\JsonService;
use service\SystemConfigService;
use service\UtilService;
use think\Cookie;
use think\Request;
use think\Session;
use think\Url;
use app\wap\model\user\User;
use app\wap\model\user\MemberShip;



class Member extends AuthController
{

    /*
 * 白名单
 * */
    public static function WhiteList()
    {
        return [
            'member_recharge'
        ];
    }

    /**
     * 会员页
     * @return mixed
     */
    public function member_manage($type=1,$bid=0){
        $this->assign(['type'=>$type,'bid'=>$bid]);
        return $this->fetch();
    }
    /**
     * 会员购买页
     * @return mixed
     */
    public function member_recharge(){

        $servicePhone=SystemConfigService::get('site_phone')?:'';
        $this->assign(['servicePhone'=>$servicePhone]);
        return $this->fetch();
    }

}