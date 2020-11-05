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

return [
    // 默认控制器名
    'default_controller' => 'Index',
    // 默认操作名
    'default_action' => 'index',
    // 自动搜索控制器
    'controller_auto_search' => true,
    'session' => [
        // SESSION 前缀
        'prefix' => 'wap',
        // 驱动方式 支持redis memcache memcached
        'type' => '',
        // 是否自动开启 SESSION
        'auto_start' => true,
    ],
    'template' => [
        // 模板引擎类型 支持 php think 支持扩展
        'type' => 'Think',
        // 模板路径
        'view_path' => APP_PATH . 'wap/view/first/',
        // 模板后缀
        'view_suffix' => 'html',
        // 模板文件名分隔符
        'view_depr' => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin' => '{',
        // 模板引擎普通标签结束标记
        'tpl_end' => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end' => '}',
    ],
    // 视图输出字符串内容替换
    'view_replace_str' => [
        '{__PLUG_PATH}' => PUBILC_PATH . 'static/plug/',
        '{__STATIC_PATH}' => PUBILC_PATH . 'static/',
        '{__PUBLIC_PATH}' => PUBILC_PATH,
        '{__WAP_PATH}' => PUBILC_PATH . 'wap/first/',
        '{__FRAME_PATH}' => PUBILC_PATH . '/system/frame/',
        '{__PC_KS3}' => PUBILC_PATH . 'pc/ks3-js-sdk/'
    ],

    'exception_handle' => \app\wap\controller\WapException::class,
    'empty_controller' => 'AuthController',
    'white_phone' => [
        '15594500161',
        '13891589031',
        '17628005686'
    ],
];
