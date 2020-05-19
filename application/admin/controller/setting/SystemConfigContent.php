<?php

namespace app\admin\controller\setting;

use app\admin\controller\AuthController;
use app\admin\model\system\SystemConfigContent as SystemConfigContentModel;
use service\JsonService;

/**
 * 配置文章
 * Class SystemConfigContent
 * @package app\admin\controller\setting
 */
class SystemConfigContent extends AuthController
{
    /**
     * 展示数据
     * @param int $id
     * @return mixed|void
     */
    public function index($id = 0)
    {
        if (!$id) {
            return $this->failed('缺少参数');
        }
        $this->assign([
            'id' => $id,
            'content' => SystemConfigContentModel::getValue($id, 'id'),
            'title' => SystemConfigContentModel::getValue($id, 'id', 'title'),
        ]);
        return $this->fetch();
    }

    /**
     * 保存数据
     * @param int $id
     * @throws \think\exception\DbException
     */
    public function save($id = 0)
    {
        if (!$id) {
            return $this->failed('缺少参数');
        }
        $content = $this->request->post('content', '');
        $info = SystemConfigContentModel::get($id);
        if (!$info) {
            return JsonService::fail('您保存的配置不存在');
        }
        if (!$content) {
            return JsonService::fail('内容不能为空');
        }
        $info->content = htmlspecialchars($content);
        if ($info->save()) {
            return JsonService::successful('保存成功');
        } else {
            return JsonService::fail('保存失败');
        }
    }
}