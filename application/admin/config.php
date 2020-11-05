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
    'session'                => [
        // SESSION 前缀
        'prefix'         => 'admin',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '{__ADMIN_PATH}'=>PUBILC_PATH.'system/',//后台
        '{__FRAME_PATH}'=>PUBILC_PATH.'system/frame/',//H+框架
        '{__PLUG_PATH}'=>PUBILC_PATH.'static/plug/',//前后台通用
        '{__MODULE_PATH}'=>PUBILC_PATH.'system/module/',//后台功能模块
        '{__STATIC_PATH}'=>PUBILC_PATH.'static/',//全站通用
        '{__PUBLIC_PATH}'=>PUBILC_PATH,//静态资源路径
        '{__PC_KS3}'=>PUBILC_PATH.'pc/ks3-js-sdk/'
    ],
];
