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

namespace basic;


use Exception;
use service\JsonService;
use think\Config;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;
use think\Request;
use think\Url;

class WapException extends Handle
{
    public function render(Exception $e)
    {
        //可以在此交由系统处理
        if(Config::get('app_debug')) return parent::render($e);
        // 参数验证错误
        if ($e instanceof ValidateException) {
            return json($e->getError(), 422);
        }
        // 请求异常
        if ($e instanceof HttpException && request()->isAjax()) {
            return JsonService::fail('系统错误');
        }else{
            $url = 0;
            $title = '系统错误';
            $msg = addslashes($e->getMessage());
            exit(view('public/error',compact('title', 'msg', 'url'))->getContent());
        }
    }
}