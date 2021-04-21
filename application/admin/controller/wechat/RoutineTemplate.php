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

namespace app\admin\controller\wechat;

use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use service\JsonService as Json;
use service\WechatTemplateService;
use service\RoutineTemplateService;
use think\Cache;
use think\Request;
use think\Url;
use app\admin\model\wechat\RoutineTemplate as RoutineTemplateModel;
use app\admin\model\system\SystemConfig;

/**
 * 微信订阅消息控制器
 * Class WechatTemplate
 * @package app\admin\controller\wechat
 */
class RoutineTemplate extends AuthController
{

    protected $cacheTag = '_system_routine_wechat';

    public function index()
    {
        $where = parent::getMore([
            ['name', ''],
            ['status', '']
        ], $this->request);
        $this->assign('where', $where);
        $this->assign(RoutineTemplateModel::SystemPage($where));
        try {
            $industry = Cache::tag($this->cacheTag)->remember('_wechat_routine_industry', function () {
                $cache = RoutineTemplateService::getCategory();
                if (!$cache) return [];
                Cache::tag($this->cacheTag, ['_wechat_routine_industry']);
                return $cache;
            }, 0) ?: [];
        } catch (\Exception $e) {
            $industry = [];
        }
        !is_array($industry) && $industry = [];
        $this->assign('industry', $industry);
        return $this->fetch();
    }

    /**
     * 添加模板消息
     * @return mixed
     */
    public function create()
    {
        $f = array();
        $f[] = Form::input('tempkey', '模板编号');
        $f[] = Form::input('tempid', '模板ID');
        $f[] = Form::input('name', '模板名');
        $f[] = Form::input('content', '回复内容')->type('textarea');
        $f[] = Form::radio('status', '状态', 1)->options([['label' => '开启', 'value' => 1], ['label' => '关闭', 'value' => 0]]);
        $form = Form::make_post_form('添加模板消息', $f, Url::build('save'),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function save(Request $request)
    {
        $data = parent::postMore([
            'tempkey',
            'tempid',
            'name',
            'content',
            ['status', 0]
        ], $request);
        if ($data['tempkey'] == '') return Json::fail('请输入模板编号');
        if ($data['tempkey'] != '' && RoutineTemplateModel::be($data['tempkey'], 'tempkey'))
            return Json::fail('请输入模板编号已存在,请重新输入');
        if ($data['tempid'] == '') return Json::fail('请输入模板ID');
        if ($data['name'] == '') return Json::fail('请输入模板名');
        if ($data['content'] == '') return Json::fail('请输入回复内容');
        $data['add_time'] = time();
        RoutineTemplateModel::set($data);
        return Json::successful('添加模板消息成功!');
    }

    /**
     * 编辑模板消息
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function edit($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $product = RoutineTemplateModel::get($id);
        if (!$product) return Json::fail('数据不存在!');
        $f = array();
        $f[] = Form::input('tempkey', '模板编号', $product->getData('tempkey'))->disabled(1);
        $f[] = Form::input('name', '模板名', $product->getData('name'))->disabled(1);
        $f[] = Form::input('tempid', '模板ID', $product->getData('tempid'));
        $f[] = Form::radio('status', '状态', $product->getData('status'))->options([['label' => '开启', 'value' => 1], ['label' => '关闭', 'value' => 0]]);
        $form = Form::make_post_form('编辑模板消息', $f, Url::build('update', compact('id')),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function update(Request $request, $id)
    {
        $data = parent::postMore([
            'tempid',
            ['status', 0]
        ], $request);
        if ($data['tempid'] == '') return Json::fail('请输入模板ID');
        if (!$id) return $this->failed('数据不存在');
        $product = RoutineTemplateModel::get($id);
        if (!$product) return Json::fail('数据不存在!');
        RoutineTemplateModel::edit($data, $id);
        return Json::successful('修改成功!');
    }

    /**
     * 删除模板消息
     * @param $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        if (!$id) return Json::fail('数据不存在!');
        if (!RoutineTemplateModel::del($id))
            return Json::fail(RoutineTemplateModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }


}
