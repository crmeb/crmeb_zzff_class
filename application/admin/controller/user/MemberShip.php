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

namespace app\admin\controller\user;

use app\admin\controller\AuthController;
use service\UtilService as Util;
use service\JsonService as Json;
use service\FormBuilder as Form;
use think\Url;
use app\admin\model\user\MemberShip as MembershipModel;

/**
 * 会员设置控制器
 * Class User
 * @package app\admin\controller\user
 */
class MemberShip extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function membership_vip_list()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['is_publish', ''],
            ['title', ''],
        ]);
        return Json::successlayui(MembershipModel::getSytemVipList($where));
    }

    public function add_vip($id = 0)
    {
        $membership =[];
        if ($id) {
            $membership = MembershipModel::get($id);
            if ($membership) $membership['sorts'] = $membership['sort'];
            if ($membership['is_free']) $membership['free_day'] = $membership['vip_day'];
        }
        $this->assign('id',$id);
        $this->assign('membership',json_encode($membership));
        return $this->fetch();
    }

    public function save_sytem_vip($id = 0)
    {
        $post = Util::postMore([
            ['title', ''],
            ['vip_day', 0],
            ['free_day', 0],
            ['original_price', 0],
            ['price', 0],
            ['sort', 0],
            ['is_permanent', 0],
            ['is_publish', 0],
            ['is_free', 0],
            ['add_time', time()],
        ]);
        if ($post['title'] == '') $this->failed('请输入会员标题');
        if ($post['is_permanent'] == 0 && $post['vip_day'] <= 0 && $post['is_free'] ==0) $this->failed('会员有有效期时,请设置会员有效期');
        if ($post['is_free'] == 1 && $post['free_day'] <= 0) $this->failed('免费会员有有效期时,请设置会员有效期');
        if (bcsub($post['original_price'],0,0) < 0) $this->failed('请输入会员原价');
        if (bcsub($post['price'],0,0) < 0) $this->failed('请输入会员原价');
        if($post['is_free'] == 1){
            $post['vip_day']=$post['free_day'];
            unset($post['free_day']);
        }
        MembershipModel::beginTrans();
        try {
            if ($id) {
                $vipinfo = MembershipModel::get($id);
                unset($post['add_time']);
                MembershipModel::update($post, ['id' => $id]);
                MembershipModel::commitTrans();
                return Json::successful('修改成功');
            } else {
                MembershipModel::set($post);
                MembershipModel::commitTrans();
                return Json::successful('添加成功');
            }
        } catch (\Exception $e) {
            MembershipModel::rollbackTrans();
            return Json::fail($e->getMessage());
        }
    }

    public function set_publish($is_publish = '', $id = '')
    {
        if ($is_publish == '' || $id == '') return Json::fail('缺少参数');
        if (MembershipModel::update(['is_publish' => $is_publish], ['id' => $id]))
            return Json::successful($is_publish == 1 ? '发布成功' : '隐藏成功');
        else
            return Json::fail($is_publish == 1 ? '发布失败' : '隐藏失败');
    }

    public function delete($id = '')
    {
        if ($id == '') return Json::fail('缺少参数');
        if (MembershipModel::update(['is_del' => 1], ['id' => $id]))
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }


}