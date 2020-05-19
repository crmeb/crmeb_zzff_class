<?php

namespace app\admin\controller\special;

use think\Url;
use service\FormBuilder as Form;
use service\UtilService as Util;
use service\JsonService as Json;
use app\admin\controller\AuthController;
use app\admin\model\special\Grade as GradeModel;

/**
 * 年级控制器
 * Class Grade
 * @package app\admin\controller\special
 */
class Grade extends AuthController
{
    public function index()
    {
        $this->assign('grade', GradeModel::getAll());
        return $this->fetch();
    }

    public function get_grade_list()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['cid', ''],
            ['name', ''],
        ]);
        return Json::successlayui(GradeModel::getAllList($where));
    }

    /**
     * 创建年纪
     * @param int $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function create($id = 0)
    {
        if ($id) $grade = GradeModel::get($id);
        $form = Form::create(Url::build('save', ['id' => $id]), [
            Form::input('name', '分类名称', isset($grade) ? $grade->name : ''),
            Form::number('sort', '排序', isset($grade) ? $grade->sort : 0),
        ]);
        $form->setMethod('post')->setTitle($id ? '修改分类' : '添加分类')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload();');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        $field == '' || $id == '' || $value == '' && Json::fail('缺少参数');
        if (GradeModel::where(['id' => $id])->update([$field => $value]))
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**
     * 新增或者修改
     *
     * @return json
     */
    public function save($id = 0)
    {
        $post = Util::postMore([
            ['name', ''],
            ['sort', 0],
        ]);
        if (!$post['name']) return Json::fail('请输入年级名称');
        if ($id) {
            GradeModel::update($post, ['id' => $id]);
            return Json::successful('修改成功');
        } else {
            $post['add_time'] = time();
            if (GradeModel::set($post))
                return Json::successful('添加成功');
            else
                return Json::fail('添加失败');
        }
    }

    /**
     * 删除
     *
     * @return json
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        if (GradeModel::del($id))
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }
}