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

use app\admin\model\user\SignPoster as SignPosterModel;
use app\admin\controller\AuthController;
use service\UtilService as Util;
use service\JsonService as Json;
use service\FormBuilder as Form;
use think\Url;
use think\Request;
use service\UploadService as Upload;
/**
 * 会员管理控制器
 * Class User
 * @package app\admin\controller\user
 */
class SignPoster extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function getSignPosterList()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
        ]);
        return Json::successlayui(SignPosterModel::getSignPosterList($where));
    }
    /**
     * 添加签到海报
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function create()
    {
        $f = array();
        $f[] = Form::dateTime('sign_time', '签到时间');
        $f[] =Form::frameImageOne('poster', '签到海报', Url::build('admin/widget.images/index', array('fodder' => 'poster')))->icon('image')->width('100%')->height('500px');
        $f[] = Form::number('sort', '排序')->col(12);
        $form = Form::make_post_form('新增海报', $f, Url::build('save'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function save(Request $request)
    {
        $data = Util::postMore([
            ['sign_time', ''],
            ['poster', []],
            ['sort', 0],
        ], $request);
       if(!$data['sign_time']) return Json::fail('请选择时间');
       if(count($data['poster'])<1) return Json::fail('请上传海报');
        $data['add_time'] = time();
        $data['poster']=$data['poster'][0];
        $data['sign_time']=strtotime($data['sign_time']);
        $res=SignPosterModel::set($data);
        if ($res)
            return Json::successful('添加成功');
        else
            return Json::fail('添加失败');
    }
    /**
     * 编辑签到海报
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function edit($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $poster = SignPosterModel::get($id);
        if (!$poster) return Json::fail('数据不存在!');
        $f = array();
        $f[] = Form::dateTime('sign_time', '签到时间',date('Y-m-d H:i:s',$poster->getData('sign_time')));
        $f[] =Form::frameImageOne('poster', '签到海报', Url::build('admin/widget.images/index', array('fodder' => 'poster')),$poster->getData('poster'))->icon('image')->width('100%')->height('500px');
        $f[] = Form::number('sort', '排序',$poster->getData('sort'))->col(12);
        $form = Form::make_post_form('修改海报', $f, Url::build('update',compact('id')));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function update(Request $request,$id)
    {
        $data = Util::postMore([
            ['sign_time', ''],
            ['poster', []],
            ['sort', 0],
        ], $request);
       if(!$data['sign_time']) return Json::fail('请选择时间');
       if(count($data['poster'])<1) return Json::fail('请上传海报');
        $data['poster']=$data['poster'][0];
        $data['sign_time']=strtotime($data['sign_time']);
        $res=SignPosterModel::edit($data,$id);
        if ($res)
            return Json::successful('修改成功');
        else
            return Json::fail('修改失败');
    }

    public function delete($id = '')
    {
        if ($id == '') return Json::fail('缺少参数');
        $poster = SignPosterModel::get($id);
        if (!$poster) return Json::fail('数据不存在');
        if (SignPosterModel::del($id))
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }

}