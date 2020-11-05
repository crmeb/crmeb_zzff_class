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

namespace app\admin\model\system;

use basic\ModelBasic;
use traits\ModelTrait;

/**
 *
 * Class SystemConfigContent
 * @package app\admin\model\system
 */
class SystemConfigContent extends ModelBasic
{
    use ModelTrait;

    /**
     * 配置数据
     * @var array
     */
    protected static $config;

    public static function initialWhere()
    {
        return self::where(['is_del' => 0, 'is_show' => 1]);
    }

    /**
     * 获取配置
     * @param $name
     * @return string
     */
    public static function getValue($name, $filed = 'config_name', $valueName = 'content')
    {
        $content = self::initialWhere()->where([$filed => $name])->value($valueName);
        return htmlspecialchars_decode($content);
    }
}