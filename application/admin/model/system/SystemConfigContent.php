<?php

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