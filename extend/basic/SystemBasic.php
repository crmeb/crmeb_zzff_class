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

use service\JsonService;

class SystemBasic extends \think\Controller
{
    /**
     * 操作失败提示框
     * @param string $msg 提示信息
     * @param string $backUrl 跳转地址
     * @param string $title 标题
     * @param int $duration 持续时间
     * @return mixed
     */
    protected function failedNotice($msg = '操作失败', $backUrl = 0, $info = '', $duration = 3)
    {
        $type = 'error';
        $this->assign(compact('msg','backUrl','info','duration','type'));
        return $this->fetch('public/notice');
    }

    /**
     * 失败提示一直持续
     * @param $msg
     * @param int $backUrl
     * @param string $title
     * @return mixed
     */
    protected function failedNoticeLast($msg = '操作失败', $backUrl = 0, $info = '')
    {
        return $this->failedNotice($msg,$backUrl,$info,0);
    }

    /**
     * 操作成功提示框
     * @param string $msg 提示信息
     * @param string $backUrl 跳转地址
     * @param string $title 标题
     * @param int $duration 持续时间
     * @return mixed
     */
    protected function successfulNotice($msg = '操作成功',$backUrl = 0,$info = '',$duration = 3)
    {
        $type = 'success';
        $this->assign(compact('msg','backUrl','info','duration','type'));
        return $this->fetch('public/notice');
    }

    /**
     * 成功提示一直持续
     * @param $msg
     * @param int $backUrl
     * @param string $title
     * @return mixed
     */
    protected function successfulNoticeLast($msg = '操作成功',$backUrl = 0,$info = '')
    {
        return $this->successfulNotice($msg,$backUrl,$info,0);
    }

    /**
     * 错误提醒页面
     * @param string $msg
     * @param int $url
     */
    protected function failed($msg = '哎呀…亲…您访问的页面出现错误', $url = 0)
    {
        if($this->request->isAjax()){
            exit(JsonService::fail($msg,$url)->getContent());
        }else{
            $this->assign(compact('msg','url'));
            exit($this->fetch('public/error'));
        }
    }

    /**
     * 成功提醒页面
     * @param string $msg
     * @param int $url
     */
    protected function successful($msg, $url = 0)
    {
        if($this->request->isAjax()){
            exit(JsonService::successful($msg,$url)->getContent());
        }else{
            $this->assign(compact('msg','url'));
            exit($this->fetch('public/success'));
        }
    }

    protected function exception($msg = '无法打开页面')
    {
        $this->assign(compact('msg'));
        exit($this->fetch('public/exception'));
    }
}
