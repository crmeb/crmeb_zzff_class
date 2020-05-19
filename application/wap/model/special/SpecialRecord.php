<?php

namespace app\wap\model\special;

use basic\ModelBasic;
use traits\ModelTrait;

/**
 * Class SpecialRecord
 * @package app\wap\model\special
 */
class SpecialRecord extends ModelBasic
{
    use ModelTrait;

    /**
     * 记录用户浏览记录
     * @param $specialId
     * @param $uid
     * @return false|int|object
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function record($specialId, $uid)
    {
        $info = self::where(['special_id' => $specialId, 'uid' => $uid])->find();
        if ($info) {
            $info->number = $info->number + 1;
            $info->update_time = time();
            $res = $info->save();
        } else {
            $res = self::set([
                'number' => 1,
                'add_time' => time(),
                'update_time' => time(),
                'uid' => $uid,
                'special_id' => $specialId
            ]);
        }
        return $res;
    }
}