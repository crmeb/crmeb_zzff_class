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

define('INSTALL_DATE',1534816243);
define('SERIALNUMBER','70nr0t');
//专题类型
define('SPECIAL_TYPE',array(
    1 => "图文专题",
    2 => "音频专题",
    3 => "视频专题",
    4 => "直播专题",
    5 => "专栏专题",
    6 => "其他专题"
));
define('SPECIAL_IMAGE_TEXT',1);
define('SPECIAL_AUDIO',2);
define('SPECIAL_VIDEO',3);
define('SPECIAL_LIVE',4);
define('SPECIAL_COLUMN',5);
define('SPECIAL_OTHER',6);
//后台专题付费方式
define('PAY_TYPE', array(
    0 => "免费",
    1 => "付费",
    2 => "加密",
));
define('PAY_NO_MONEY', 0);
define('PAY_MONEY', 1);
define('PAY_PASSWORD', 2);
//后台专题会员付费方式
define('MEMBER_PAY_TYPE', array(
    0 => "免费",
    1 => "付费",
));
define('MEMBER_PAY_NO_MONEY', 0);
define('MEMBER_PAY_MONEY', 1);
