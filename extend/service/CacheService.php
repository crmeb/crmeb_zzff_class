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


use think\Cache;

class CacheService
{
    protected static $globalCacheName = '_cached_1515146130';


    public static function set($name, $value, $expire = 0)
    {
        return self::handler()->set($name,$value,$expire);
    }

    public static function get($name,$default = false)
    {
        return self::handler()->get($name,$default);
    }

    public static function rm($name)
    {
        return self::handler()->rm($name);
    }

    public static function handler()
    {
        return Cache::tag(self::$globalCacheName);
    }

    public static function clear()
    {
        return Cache::clear(self::$globalCacheName);
    }
}