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

namespace app\admin\controller\wechat;

use app\admin\controller\AuthController;
use service\WechatService;
use think\Cache;
use think\Db;
use think\Request;

/**
 * 微信菜单  控制器
 * Class Menus
 * @package app\admin\controller\wechat
 */
class Menus extends AuthController
{

    public function index()
    {
        $menus = Db::name('cache')->where('key', 'wechat_menus')->value('result');
        $menus = $menus ?: '[]';
        $this->assign('menus', $menus);
        return $this->fetch();
    }

    public function save(Request $request)
    {
        $buttons = $request->post('button/a', []);
        if (!count($buttons)) return $this->failed('请添加至少一个按钮');
        try {
            WechatService::menuService()->add($buttons);
            Db::name('cache')->insert(['key' => 'wechat_menus', 'result' => json_encode($buttons), 'add_time' => time()], true);
            return $this->successful('修改成功!');
        } catch (\Exception $e) {
            return $this->failed($e->getMessage());
        }
    }
}