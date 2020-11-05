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

namespace app\admin\controller\user;

use app\admin\model\user\UserSign as UserSignModel;
use app\admin\controller\AuthController;
use service\UtilService as Util;
use service\JsonService as Json;
use service\FormBuilder as Form;
use think\Url;
use think\Request;
use service\UploadService as Upload;

class UserSign extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function getUserSignList()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['title', ''],
        ]);
        return Json::successlayui(UserSignModel::getUserSignList($where));
    }

}