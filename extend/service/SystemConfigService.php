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

namespace service;


use app\admin\model\system\SystemConfig;

class SystemConfigService
{
    protected static $configList = null;

    public static function config($key)
    {
        if(self::$configList === null) self::$configList = self::getAll();
        return isset(self::$configList[$key]) ? self::$configList[$key] : null;
    }

    public static function get($key)
    {
        return SystemConfig::getValue($key);
    }

    public static function more($keys)
    {
        return SystemConfig::getMore($keys);
    }

    public static function getAll()
    {
        return SystemConfig::getAllConfig()?:[];
    }

    public static function setUrl($keys){
        $site_url=self::get('site_url');
        if(is_array($keys)){
            foreach ($keys as &$item){
                if(is_array($item) && isset($item['pic'])){
                    $item['pic']=strstr($item['pic'],'http')===false ? $site_url.$item['pic'] : $item['pic'];
                }else{
                    $item=strstr($item,'http')===false ? $site_url.$item : $item;
                }
            }
        }else{
            $keys=strstr($keys,'http')===false ? $site_url.$keys : $keys;
        }
        return $keys;
    }

}