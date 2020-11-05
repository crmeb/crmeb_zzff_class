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

namespace app\admin\controller;


use service\UtilService;

class Common extends AuthController
{
    /**
     * 删除原来图片
     * @param $url
     */
    public function rmPublicResource($url)
    {
        $res = UtilService::rmPublicResource($url);
        if ($res->status)
            return $this->successful('删除成功!');
        else
            return $this->failed($res->msg);
    }


}