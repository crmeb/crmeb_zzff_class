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
use app\admin\model\user\SystemVip as SystemVipModel;
use app\admin\model\user\SystemVipGift;
use app\admin\model\user\UserVip;

/**
 * 会员设置控制器
 * Class User
 * @package app\admin\controller\user
 */
class SystemVip extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function system_vip_list()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['is_show', ''],
            ['is_forever', ''],
            ['start_time', ''],
            ['end_time', ''],
            ['title', ''],
        ]);
        return Json::successlayui(SystemVipModel::getSytemVipList($where));
    }

    public function add_sytem_vip($id = '')
    {
        $f = array();
        if ($id) {
            $systemVip = SystemVipModel::get($id);
        }
        $f[] = Form::input('title', '会员标题', isset($systemVip) ? $systemVip->title : '');
        $f[] = Form::input('abstract', '会员简介', isset($systemVip) ? $systemVip->abstract : '');
        if (isset($systemVip)) {
            //已发布的会员无法修改会员时效和会员金额
            if ($systemVip->is_publish == 1) {
//                $f[] = Form::input('valid_date','有效时间',$systemVip->valid_date)->disabled(true)->col(12);
                $f[] = Form::input('money', '购买金额', $systemVip->money)->disabled(true)->col(12);
            } else {
//                $f[] = Form::input('valid_date','有效时间',$systemVip->valid_date)->placeholder('永久会员可不填写时限')->col(12);
                $f[] = Form::input('money', '购买金额', $systemVip->money)->col(12);
            }
            $f[] = Form::frameImageOne('image', '会员封面', get_image_Url('image'), $systemVip->image)->icon('images');
        } else {
            $f[] = Form::frameImageOne('image', '会员封面', get_image_Url('image'))->icon('images');
//            $f[] = Form::input('valid_date','有效时间')->placeholder('永久会员可不填写时限')->col(12);
            $f[] = Form::input('money', '购买金额')->col(12);
        }
        $f[] = Form::number('grade', '会员等级', isset($systemVip) ? $systemVip->grade : 0)->precision(0)->col(12);
        $f[] = Form::textarea('content', '会员介绍', isset($systemVip) ? $systemVip->content : '');
//        $f[] = Form::input('discount','会员享受折扣',isset($systemVip) ? $systemVip->discount : '')
//            ->placeholder('商品折扣以100%比计算,例:10为10%,0.1为0.1%');
        if (!isset($systemVip)) $f[] = Form::radio('is_publish', '是否立即发布', 0)->options([
            ['label' => '暂不发布', 'value' => 0],
            ['label' => '立即发布', 'value' => 1],
        ]);
        $f[] = Form::radio('is_forever', '是否永久有效', isset($systemVip) ? $systemVip->is_forever : 0)->options([
//            ['label'=>'非永久','value'=>0],
            ['label' => '永久', 'value' => 1],
        ]);
//        $f[] = Form::radio('is_show','是否显示',isset($systemVip) ? $systemVip->is_show : 1)->options([
//            ['label'=>'开启','value'=>1],
//            ['label'=>'关闭','value'=>0],
//        ]);
        $form = Form::make_post_form($f, Url::build('save_sytem_vip', ['id' => $id]));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function save_sytem_vip($id = '')
    {
        $post = Util::postMore([
            ['title', ''],
            ['content', ''],
            ['abstract', ''],
            ['image', ''],
            ['valid_date', 0],
            ['money', 0],
            ['grade', 0],
            ['discount', 0],
            ['is_publish', 0],
            ['is_forever', 0],
            ['is_show', 0],
            ['add_time', time()],
        ]);
        if ($post['title'] == '') $this->failed('请输入会员标题');
        if ($post['is_forever'] == 0 && $post['valid_date'] <= 0) $this->failed('会员有有效期时,请设置会员有效期');
        if ($post['money'] <= 0) $this->failed('请输入会员购买金额');
        SystemVipModel::beginTrans();
        try {
            if ($id) {
                $vipinfo = SystemVipModel::get($id);
                unset($post['is_publish'], $post['add_time']);
                SystemVipModel::update($post, ['id' => $id]);
                SystemVipModel::commitTrans();
                if ($vipinfo->grade != $post['grade']) UserVip::where(['vip_id' => $id])->update(['grade' => $post['grade']]);
                return Json::successful('修改成功');
            } else {
                SystemVipModel::set($post);
                SystemVipModel::commitTrans();
                return Json::successful('添加成功');
            }
        } catch (\Exception $e) {
            SystemVipModel::rollbackTrans();
            return Json::fail($e->getMessage());
        }
    }

    public function set_show($is_show = '', $id = '')
    {
        if ($is_show == '' || $id == '') return Json::fail('缺少参数');
        if (SystemVipModel::update(['is_show' => $is_show], ['id' => $id]))
            return Json::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        else
            return Json::fail($is_show == 1 ? '显示失败' : '隐藏失败');
    }

    public function publish($id = '')
    {
        if ($id == '') return Json::fail('缺少参数');
        if (SystemVipModel::update(['is_publish' => 1], ['id' => $id]))
            return Json::successful('发布成功');
        else
            return Json::fail('发布成功');
    }

    public function delete($id = '')
    {
        if ($id == '') return Json::fail('缺少参数');
        if (SystemVipModel::update(['is_del' => 1], ['id' => $id]))
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }

    public function add_sytem_vip_gift($id = '')
    {
        if (!$id) return $this->failed('缺少会员id');
        $where = Util::getMore([
            ['gift_name', ''],
            ['vip_id', $id],
        ]);
        $this->assign('vip_id', $id);
        $this->assign('where', $where);
        $this->assign(SystemVipGift::getAll($where));
        return $this->fetch();
    }

    public function add_gift($vip_id = '', $id = 0)
    {
        if ($id) $gift = SystemVipGift::get($id);
        $f[] = Form::input('gift_name', '礼品名称', isset($gift) ? $gift->gift_name : '');
        $f[] = Form::number('gift_count', '礼品个数', isset($gift) ? $gift->gift_count : 0);
        $f[] = Form::number('money', '礼品金额', isset($gift) ? $gift->money : 0);
        $f[] = Form::number('sort', '排序', isset($gift) ? $gift->sort : 0);
        $f[] = Form::frameImageOne('gift_cover', '礼品封面', get_image_Url('gift_cover'), isset($gift) ? $gift->gift_cover : '')->icon('images');
        $form = Form::make_post_form($f, Url::build('save_sytem_vip_gift', ['vip_id' => $vip_id, 'id' => $id]), 1);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function save_sytem_vip_gift($vip_id = '', $id = 0)
    {
        $post = Util::postMore([
            ['gift_name', ''],
            ['gift_count', 0],
            ['money', 0],
            ['sort', 0],
            ['gift_cover', ''],
            ['add_time', time()],
            ['vip_id', $vip_id],
        ]);
        if (!$post['gift_name']) return Json::fail('请输入礼品名');
        if (!$post['gift_count']) return Json::fail('请输入礼品数量');
        if (!$post['money']) return Json::fail('请输入礼品金额');
        if (!$post['gift_cover']) return Json::fail('请上传礼品封面图');
        try {
            if ($id) {
                if (SystemVipGift::update($post, ['id' => $id]))
                    return Json::successful('修改成功');
                else
                    return Json::fail('修改失败');
            } else {
                if (SystemVipGift::set($post))
                    return Json::successful('添加成功');
                else
                    return Json::fail('添加失败');
            }
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

    public function delete_gift($id = '')
    {
        if (!$id) return Json::fail('缺少参数');
        if (SystemVipGift::update(['is_del' => 1], ['id' => $id]))
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }
}