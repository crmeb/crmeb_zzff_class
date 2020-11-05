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
use app\admin\model\special\SpecialTask;
use service\JsonService;
use service\UtilService;
use think\Url;

/**
 * 任务控制器
 * Class Grade
 * @package app\admin\controller\special
 */
class Task extends AuthController
{
    /**
     * 任务列表展示
     * @return
     * */
    public function index($coures_id = 0)
    {
        $this->assign('coures_id', $coures_id);
        $this->assign('special_id', SpecialCourse::where('id', $coures_id)->value('special_id'));
        $this->assign('specialList', \app\admin\model\special\Special::PreWhere()->field(['id', 'title'])->select());
        return $this->fetch();
    }

    /**
     * 任务列表获取
     * @return json
     * */
    public function task_list()
    {
        $where = UtilService::getMore([
            ['page', 1],
            ['is_show', ''],
            ['limit', 20],
            ['title', ''],
            ['order', ''],
            ['special_id', 0],
        ]);
        return JsonService::successlayui(SpecialTask::getTaskList($where));

    }

    /**
     * 添加和修改任务
     * @param int $id 修改
     * @return
     * */
    public function add_task($id = 0)
    {
        $this->assign('id', $id);
        if ($id) {
            $task = SpecialTask::get($id);
            $task->image = get_key_attr($task->image);
            $task->link = get_key_attr($task->link);
            $this->assign('special_id', $task->special_id);
            $this->assign('task', $task->toArray());
        }
        $specialList = \app\admin\model\special\Special::PreWhere()->field(['id', 'title', 'is_live'])->select();
        $this->assign('specialList', $specialList);
        return $this->fetch();
    }

    /**
     * 添加和修改任务
     * @param int $id 修改
     * @return json
     * */
    public function save_task($id = 0)
    {
        $data = UtilService::postMore([
            ['title', ''],
            ['image', ''],
            ['link', ''],
            ['play_count', 0],
            ['special_id', 0],
            ['sor', 0],
            ['is_pay', 0],
            ['is_show', 1],
        ]);
        if ($data['special_id'] === 0) return JsonService::fail('请选择课程再尝试添加');
        if (!$data['title']) return JsonService::fail('请输入课程标题');
        if (!$data['image']) return JsonService::fail('请上传封面图');
        if (!$data['link']) return JsonService::fail('请上传或者添加视频');
        if ($id) {
            unset($data['is_show']);
            SpecialTask::update($data, ['id' => $id]);
            return JsonService::successful('修改成功');
        } else {
            $data['add_time'] = time();
            if (SpecialTask::set($data))
                return JsonService::successful('添加成功');
            else
                return JsonService::fail('添加失败');
        }
    }

    /**
     * 设置单个产品上架|下架
     * @param int $is_show 是否显示
     * @param int $id 修改的主键
     * @return json
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && JsonService::fail('缺少参数');
        $res = SpecialTask::where(['id' => $id])->update(['is_show' => (int)$is_show]);
        if ($res) {
            return JsonService::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        } else {
            return JsonService::fail($is_show == 1 ? '显示失败' : '隐藏失败');
        }
    }

    /**
     * 快速编辑
     * @param string $field 字段名
     * @param int $id 修改的主键
     * @param string value 修改后的值
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        $field == '' || $id == '' || $value == '' && JsonService::fail('缺少参数');
        if (SpecialTask::where(['id' => $id])->update([$field => $value]))
            return JsonService::successful('保存成功');
        else
            return JsonService::fail('保存失败');
    }

    /**
     * 删除指定任务并删除指定资源
     * @param int $id 修改的主键
     * @return json
     * */
    public function delete($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        if (SpecialTask::delTask($id))
            return JsonService::successful('删除成功');
        else
            return JsonService::fail(SpecialTask::getErrorInfo('删除失败'));
    }

    /**
     * 编辑详情
     * @return mixed
     */
    public function update_content($id = 0)
    {
        if (!$id) {
            return $this->failed('缺少id ');
        }
        $this->assign([
            'action' => Url::build('save_content', ['id' => $id]),
            'field' => 'content',
            'content' => htmlspecialchars_decode(SpecialTask::where('id', $id)->value('content'))
        ]);
        return $this->fetch();
    }

    /**
     * @param $id
     * @throws \think\exception\DbException
     */
    public function save_content($id)
    {
        $content = $this->request->post('content', '');
        $task = SpecialTask::get($id);
        if (!$task) {
            return JsonService::fail('修改得课程不存在');
        }
        $task->content = htmlspecialchars($content);
        if ($task->save()) {
            return JsonService::successful('保存成功');
        } else {
            return JsonService::fail('保存失败或者您没有修改什么');
        }
    }
}