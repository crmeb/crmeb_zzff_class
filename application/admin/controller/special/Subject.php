<?php

namespace app\admin\controller\special;

use app\admin\model\special\SpecialSubject;
use app\admin\model\special\Grade;
use app\admin\model\special\Special;
use service\JsonService;
use think\Url;
use service\FormBuilder as Form;
use service\UtilService as Util;
use service\JsonService as Json;
use app\admin\controller\AuthController;

/**
 * 科目控制器
 * Class Grade
 * @package app\admin\controller\special
 */
class Subject extends AuthController
{
    public function index()
    {
        $this->assign('grade', Grade::getAll());
        return $this->fetch();
    }

    public function get_subject_list()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['cid', ''],
            ['name', ''],
        ]);
        return Json::successlayui(SpecialSubject::get_subject_list($where));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create($id = 0)
    {
        if ($id) $subject = SpecialSubject::get($id);
        $form = Form::create(Url::build('save', ['id' => $id]), [
            Form::select('grade_id', '一级分类', isset($subject) ? (string)$subject->grade_id : 0)->setOptions(function () {
                $list = Grade::getAll();
                $menus = [['value' => 0, 'label' => '顶级菜单']];
                foreach ($list as $menu) {
                    $menus[] = ['value' => $menu['id'], 'label' => $menu['name']];
                }
                return $menus;
            })->filterable(1),
            Form::input('name', '分类名称', isset($subject) ? $subject->name : ''),
            Form::frameImageOne('pic', '图标', Url::build('admin/widget.images/index', array('fodder' => 'pic')), isset($subject) ? $subject->pic : '')->icon('image')->width('70%')->height('500px'),
            Form::number('sort', '排序', isset($subject) ? $subject->sort : 0),
            Form::radio('is_show', '状态', isset($subject) ? $subject->is_show : 1)->options([['label' => '显示', 'value' => 1], ['label' => '隐藏', 'value' => 0]])
        ]);
        $form->setMethod('post')->setTitle($id ? '修改二级分类':'添加二级分类')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload();');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
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
            ['pic', ''],
            ['is_show', 1],
            ['grade_id', 0],
            ['sort', 0],
        ]);
        if (!$post['name']) return Json::fail('请输入分类名称');
        if (!$post['pic']) return Json::fail('请选择分类图标');
        if (!$post['grade_id']) return Json::fail('请选择一级分类');
        if ($id) {
            SpecialSubject::update($post, ['id' => $id]);
            return Json::successful('修改成功');
        } else {
            $post['add_time'] = time();
            if (SpecialSubject::set($post))
                return Json::successful('添加成功');
            else
                return Json::fail('添加失败');
        }
    }

    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        $field == '' || $id == '' || $value == '' && Json::fail('缺少参数');
        if (SpecialSubject::where(['id' => $id])->update([$field => $value]))
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**二级分是否显示快捷操作
     * @param string $is_show
     * @param string $id
     * @return mixed
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && JsonService::fail('缺少参数');
        $res = SpecialSubject::where(['id' => $id])->update(['is_show' => (int)$is_show]);
        if ($res) {
            return JsonService::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        } else {
            return JsonService::fail($is_show == 1 ? '显示失败' : '隐藏失败');
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
        if (Special::where('subject_id', $id)->where('is_del', 0)->count()) return Json::fail('暂无法删除,请先去除专题关联');
        if (SpecialSubject::del($id))
            return Json::successful('删除成功');
        else
            return Json::fail('删除成功');
    }
}