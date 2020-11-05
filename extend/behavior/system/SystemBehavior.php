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

namespace behavior\system;

use app\admin\model\system\SystemAdmin;
use app\admin\model\system\SystemLog;
use think\Request;

/**
 * 系统后台行为
 * Class SystemBehavior
 * @package behavior\system
 */
class SystemBehavior
{
    public static function adminVisit($adminInfo,$type = 'system')
    {
        if(strtolower(Request::instance()->controller()) != 'index') SystemLog::adminVisit($adminInfo->id,$adminInfo->account,$type);
    }

    public static function systemAdminLoginAfter($adminInfo)
    {
        SystemAdmin::edit(['last_ip'=>Request::instance()->ip(),'last_time'=>time()],$adminInfo['id']);
    }

    /**
     * 商户注册成功之后
     */
    public static function merchantRegisterAfter($merchantInfo)
    {

    }

}