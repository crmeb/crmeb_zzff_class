<?php

namespace app\admin\controller\widget;

use Api\AliyunOss;
use app\admin\model\system\SystemAttachment as SystemAttachmentModel;
use app\admin\model\system\SystemAttachmentCategory as Category;
use app\admin\controller\AuthController;
use service\JsonService;
use service\SystemConfigService;
use service\JsonService as Json;
use service\UtilService as Util;
use service\FormBuilder as Form;
use think\Url;

/**
 * TODO 附件控制器
 * Class Images
 * @package app\admin\controller\widget
 */
class Images extends AuthController
{

    /**
     * 初始化
     */
    protected function init()
    {
        return AliyunOss::instance([
            'AccessKey' => SystemConfigService::get('accessKeyId'),
            'AccessKeySecret' => SystemConfigService::get('accessKeySecret'),
            'OssEndpoint' => SystemConfigService::get('end_point'),
            'OssBucket' => SystemConfigService::get('OssBucket'),
            'uploadUrl' => SystemConfigService::get('uploadUrl'),
        ]);
    }

    /**
     * 附件列表
     * @return \think\response\Json
     */
    public function index()
    {
        $pid = request()->param('pid');
        if ($pid === NULL) {
            $pid = session('pid') ? session('pid') : 0;
        }
        session('pid', $pid);
        $this->assign('pid', $pid);
        $this->assign('maxLength', $this->request->get('max_count', 0));
        $this->assign('fodder', $this->request->param('fodder', $this->request->get('fodder','')));
        return $this->fetch('widget/images');
    }

    /**获取图片列表
     *
     */
    public function get_image_list()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 18],
            ['pid', 0]
        ]);
        return Json::successful(SystemAttachmentModel::getImageList($where));
    }

    /**获取分类
     * @param string $name
     */
    public function get_image_cate($name = '')
    {
        return Json::successful(Category::getAll($name));
    }

    /**
     * 图片管理上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $pid = input('pid') != NULL ? input('pid') : session('pid');
        try {
            $aliyunOss = $this->init();
            $res = $aliyunOss->upload('file');
            if ($res) {
                SystemAttachmentModel::attachmentAdd($res['key'], 0, 'image/jpg', $res['url'], $res['url'], $pid, 1, time());
                return JsonService::successful(['url' => $res]);
            } else {
                return JsonService::fail($aliyunOss->getErrorInfo()['msg']);
            }
        } catch (\Exception $e) {
            return JsonService::fail('上传失败:' . $e->getMessage());
        }
    }

    /**
     * ajax 提交删除
     */
    public function delete()
    {
        $post = $this->request->post();
        if (empty($post['imageid']))
            Json::fail('还没选择要删除的图片呢？');
        foreach ($post['imageid'] as $v) {
            if ($v) self::deleteimganddata($v);
        }
        Json::successful('删除成功');
    }

    /**删除图片和数据记录
     * @param $att_id
     */
    public function deleteimganddata($att_id)
    {
        $attinfo = SystemAttachmentModel::get($att_id);
        if ($attinfo) {
            try {
                $this->init()->delOssFile($attinfo->name);
            } catch (\Throwable $e) {
            }
            $attinfo->delete();
        }
    }

    /**
     * 移动图片分类显示
     */
    public function moveimg($imgaes)
    {

        $formbuider = [];
        $formbuider[] = Form::hidden('imgaes', $imgaes);
        $formbuider[] = Form::select('pid', '选择分类')->setOptions(function () {
            $list = Category::getCateList();
            $options = [['value' => 0, 'label' => '所有分类']];
            foreach ($list as $id => $cateName) {
                $options[] = ['label' => $cateName['html'] . $cateName['name'], 'value' => $cateName['id']];
            }
            return $options;
        })->filterable(1);
        $form = Form::make_post_form('编辑分类', $formbuider, Url::build('moveImgCecate'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 移动图片分类操作
     */
    public function moveImgCecate()
    {
        $data = Util::postMore([
            'pid',
            'imgaes'
        ]);
        if ($data['imgaes'] == '') return Json::fail('请选择图片');
        if (!$data['pid']) return Json::fail('请选择分类');
        $res = SystemAttachmentModel::where('att_id', 'in', $data['imgaes'])->update(['pid' => $data['pid']]);
        if ($res)
            Json::successful('移动成功');
        else
            Json::fail('移动失败！');
    }

    /**
     * ajax 添加分类
     */
    public function addcate($id = 0)
    {
        $formbuider = [];
        $formbuider[] = Form::select('pid', '上级分类', (string)$id)->setOptions(function () {
            $list = Category::getCateList(0);
            $options = [['value' => 0, 'label' => '所有分类']];
            foreach ($list as $id => $cateName) {
                $options[] = ['label' => $cateName['html'] . $cateName['name'], 'value' => $cateName['id']];
            }
            return $options;
        })->filterable(1);
        $formbuider[] = Form::input('name', '分类名称');
        $jsContent = <<<SCRIPT
parent.SuccessCateg();
parent.layer.close(parent.layer.getFrameIndex(window.name));
SCRIPT;
        $form = Form::make_post_form('添加分类', $formbuider, Url::build('saveCate'), $jsContent);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 添加分类
     */
    public function saveCate()
    {
        $post = $this->request->post();
        $data['pid'] = $post['pid'];
        $data['name'] = $post['name'];
        if (empty($post['name']))
            Json::fail('分类名称不能为空！');
        $res = Category::create($data);
        if ($res)
            Json::successful('添加成功');
        else
            Json::fail('添加失败！');

    }

    /**
     * 编辑分类
     */
    public function editcate($id)
    {
        $Category = Category::get($id);
        if (!$Category) return Json::fail('数据不存在!');
        $formbuider = [];
        $formbuider[] = Form::hidden('id', $id);
        $formbuider[] = Form::select('pid', '上级分类', (string)$Category->getData('pid'))->setOptions(function () use ($id) {
            $list = Category::getCateList();
            $options = [['value' => 0, 'label' => '所有分类']];
            foreach ($list as $id => $cateName) {
                $options[] = ['label' => $cateName['html'] . $cateName['name'], 'value' => $cateName['id']];
            }
            return $options;
        })->filterable(1);
        $formbuider[] = Form::input('name', '分类名称', $Category->getData('name'));
        $jsContent = <<<SCRIPT
parent.SuccessCateg();
parent.layer.close(parent.layer.getFrameIndex(window.name));
SCRIPT;
        $form = Form::make_post_form('编辑分类', $formbuider, Url::build('updateCate'), $jsContent);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 更新分类
     * @param $id
     */
    public function updateCate($id)
    {
        $data = Util::postMore([
            'pid',
            'name'
        ]);
        if ($data['pid'] == '') return Json::fail('请选择父类');
        if (!$data['name']) return Json::fail('请输入分类名称');
        Category::edit($data, $id);
        return Json::successful('分类编辑成功!');
    }

    /**
     * 删除分类
     */
    public function deletecate($id)
    {
        $chdcount = Category::where('pid', $id)->count();
        if ($chdcount) return Json::fail('有子栏目不能删除');
        $chdcount = SystemAttachmentModel::where('pid', $id)->count();
        if ($chdcount) return Json::fail('栏目内有图片不能删除');
        if (Category::del($id)) {
            SystemAttachmentModel::where(['pid' => $id])->update(['pid' => 0]);
            return Json::successful('删除成功!');
        } else
            return Json::fail('删除失败');
    }

    /**
     * 获取签名
     */
    public function get_signature()
    {
        return JsonService::successful($this->init()->getSignature());
    }

    /**
     * 删除阿里云oss
     * @param $key
     */
    public function del_oss_key($key = '', $url = '')
    {
        if (!$key && !$url) {
            return JsonService::fail('删除失败');
        }
        if ($url) {
            $key = SystemAttachmentModel::where(['att_dir' => $url])->value('name');
        }
        $res = $this->init()->delOssFile($key);
        if ($res) {
            return JsonService::successful('删除成功');
        } else {
            return JsonService::fail('删除失败');
        }
    }

}
