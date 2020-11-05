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

namespace behavior\wechat;


use think\Db;

/**
 * 素材消息行为
 * Class MaterialBehavior
 * @package behavior\wechat
 */
class MaterialBehavior
{
    public static function db()
    {
        return Db::name('WechatMedia');
    }

    public static function wechatMaterialAfter($data,$type)
    {
        $data['type'] = $type;
        $data['add_time'] = time();
        $data['temporary'] = 0;
        self::db()->insert($data);
    }

    public static function wechatMaterialTemporaryAfter($data,$type)
    {
        $data['type'] = $type;
        $data['add_time'] = time();
        $data['temporary'] = 1;
        self::db()->insert($data);
    }
}