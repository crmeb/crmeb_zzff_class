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
use \think\Route;
//兼容模式 不支持伪静态可开启
//\think\Url::root('index.php?s=');
Route::group('admin',function(){
    Route::rule('/index2','admin/Index/index2','get');
//    Route::controller('index','admin/Index');
//    resource('system_menus','SystemMenus');
//    Route::rule('/menus','SystemMenus','get');
//    Route::resource('menus','admin/SystemMenus',['var'=>['menus'=>'menu_id']]);
//    Route::miss(function(){
//        return '页面不存在!';
//    });
});

