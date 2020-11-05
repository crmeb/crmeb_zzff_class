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

namespace app\admin\controller\special;

use app\admin\controller\AuthController;
use app\admin\model\special\SpecialCourse;
use service\JsonService;
use service\UtilService;
use service\FormBuilder as Form;
use app\admin\model\special\Special;
use think\response\Json;
use think\Url;

/**
 * 课程控制器
 * Class Grade
 * @package app\admin\controller\special
 */
class Course extends AuthController
{
    /**
     * 课程列表
     * @param int $special_id
     * @return mixed
     */
    public function index($special_id = 0)
    {
        $this->assign('special_id', $special_id);
        return $this->fetch();
    }

    /**
     * 添加课程
     * @param int $special_id
     * @param int $id
     * @return mixed|void
     * @throws \think\exception\DbException
     */
    public function add_course($special_id = 0, $id = 0)
    {
        if (!$special_id) return $this->failed('缺少参数');
        $special = Special::get($special_id);
        if (!$special) return $this->failed('并没有查到相关专题');
        if ($special->is_del) return $this->failed('此专题已被删除');
        if ($id) $course = SpecialCourse::get($id);
        $form = [
            Form::input('title', '专题名称', $special->title)->disabled(true),
            Form::input('course_name', '课程名称', isset($course) ? $course->course_name : ''),
            Form::number('sort', '排序', isset($course) ? $course->sort : ''),
            Form::radio('is_show', '状态', isset($course) ? $course->is_show : 1)->options([['label' => '显示', 'value' => 1], ['label' => '隐藏', 'value' => 0]])
        ];
        $form = Form::make_post_form('添加课程', $form, Url::build('save_course', ['special_id' => $special_id, 'id' => $id]), 3);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存课程
     * @param int $special_id
     * @param int $id
     */
    public function save_course($special_id = 0, $id = 0)
    {
        if (!$special_id) return JsonService::fail('缺少参数');
        $post = UtilService::postMore([
            ['course_name', ''],
            ['sort', 0],
            ['is_show', 1],
        ]);
        if (!$post['course_name']) return JsonService::fail('请输入课程名称');
        $post['special_id'] = $special_id;
        if ($id) {
            SpecialCourse::update($post, ['id' => $id]);
            return JsonService::successful('修改成功');
        } else {
            $post['add_time'] = time();
            if (SpecialCourse::set($post))
                return JsonService::successful('添加成功');
            else
                return JsonService::fail('添加失败');
        }
    }

    /**
     * 专题列表
     */
    public function course_list()
    {
        $where = UtilService::getMore([
            ['special_id', 0],
            ['is_show', ''],
            ['course_name', ''],
            ['page', 1],
            ['limit', 20],
        ]);
        return JsonService::successlayui(SpecialCourse::getCourseList($where));
    }

    /**
     * 删除课程
     * @param int $id
     */
    public function delete($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        if (SpecialCourse::DelCourse($id))
            return JsonService::successful('删除成功');
        else
            return JsonService::fail(SpecialCourse::getErrorInfo('删除失败'));
    }

    /**
     * 设置单个产品上架|下架
     *
     * @return json
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && JsonService::fail('缺少参数');
        $res = SpecialCourse::where(['id' => $id])->update(['is_show' => (int)$is_show]);
        if ($res) {
            return JsonService::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        } else {
            return JsonService::fail($is_show == 1 ? '显示失败' : '隐藏失败');
        }
    }

    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        $field == '' || $id == '' || $value == '' && JsonService::fail('缺少参数');
        if (SpecialCourse::where(['id' => $id])->update([$field => $value]))
            return JsonService::successful('保存成功');
        else
            return JsonService::fail('保存失败');
    }
}