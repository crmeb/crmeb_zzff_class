<?php

namespace app\admin\controller\user;

use app\admin\model\user\UserVip as UserVipModel;
use app\admin\controller\AuthController;
use app\admin\model\user\SystemVip;
use service\UtilService as Util;
use service\JsonService as Json;
use service\FormBuilder as Form;
use think\Url;

/**
 * 会员管理控制器
 * Class User
 * @package app\admin\controller\user
 */
class UserVip extends AuthController
{
    public function index()
    {
        $this->assign('system_vip_list', SystemVip::getSytemVipSelect());
        return $this->fetch();
    }

    public function getUserVipList()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['vip_id', ''],
            ['status', ''],
            ['is_forever', ''],
            ['title', ''],
        ]);
        return Json::successlayui(UserVipModel::getUserVipList($where));
    }

    public function delete($id = '')
    {
        if ($id == '') return Json::fail('缺少参数');
        $uservip = UserVipModel::get($id);
        if (!$uservip) return Json::fail('删除会员信息不存在');
        if ($uservip->is_del == 1) return Json::fail('改会员已删除');
        $uservip->is_del = 1;
        if ($uservip->save())
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }

    public function set_status($id = '', $status = '')
    {
        if ($id == '') return Json::fail('缺少参数');
        $uservip = UserVipModel::get($id);
        if (!$uservip) return Json::fail('会员信息不存在');
        $uservip->status = $status;
        if ($uservip->save())
            return Json::successful($status == 1 ? '锁定成功' : '解锁成功');
        else
            return Json::fail($status == 1 ? '锁定失败' : '解锁失败');
    }
}