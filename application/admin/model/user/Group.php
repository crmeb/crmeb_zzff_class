<?php

namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\User;

/**
 * 用户管理 model
 * Class User
 * @package app\admin\model\user
 */
class Group extends ModelBasic
{
    use ModelTrait;

    public static function getUids()
    {
        return self::where('uid', 'neq', 0)->column('uid');
    }

    public static function getAll($where)
    {
        $model = new self();
        if ($where['nickname'] != '') $model->where('uid|user_name|phone|share_name', $where['nickname']);
        return self::page($model, function ($item) {
            $item['share_count'] = self::where(['share_uid' => $item['uid']])->count();
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }, $where);
    }

    public static function setMember($uid)
    {
        $share_uid = self::where('uid', $uid)->value('share_uid');
        return self::GetDb('group_of_members')->where('uid', $share_uid)->where('is_del', 0)->value('shop_uid');
    }

    //记录当前用户所在店铺存在则删除
    public static function saveGroupAndShop($uid, $shop_uid)
    {
        if (self::GetDb('group_of_members')->where(['uid' => $uid])->count()) {
            self::GetDb('group_of_members')->where(['uid' => $uid])->update(['is_del' => 1]);
        }
        self::GetDb('group_of_members')->insert(['uid' => $uid, 'shop_uid' => $shop_uid, 'add_time' => time()]);
    }

}