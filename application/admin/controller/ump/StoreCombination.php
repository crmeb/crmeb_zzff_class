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

namespace app\admin\controller\ump;

use app\admin\controller\AuthController;
use app\admin\model\special\Special;
use app\admin\model\ump\StorePinkFalse;
use service\FormBuilder as Form;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\store\StoreProduct as ProductModel;
use think\Url;
use app\admin\model\system\SystemAttachment;
use app\wap\model\store\StorePink;

/**
 * 拼团管理
 * Class StoreCombination
 * @package app\admin\controller\store
 */
class StoreCombination extends AuthController
{

    use CurdControllerTrait;
    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file', 'store/product/' . date('Ymd'));
        $thumbPath = Upload::thumb($res->dir);
        //产品图片上传记录
        $fileInfo = $res->fileInfo->getinfo();
        SystemAttachment::attachmentAdd($res->fileInfo->getSaveName(), $fileInfo['size'], $fileInfo['type'], $res->dir, $thumbPath, 2);
        if ($res->status == 200)
            return Json::successful('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
    }

    /**拼团列表
     * @return mixed
     */
    public function combina_list($cid = 0)
    {
        $special_type = $this->request->param('special_type');
        $this->assign('special_type', $special_type);
        $this->assign('cid', $cid);
        return $this->fetch();
    }

    /*
     * 获取拼团列表
     * @param array $where
     * @return json
     * */
    public function get_pink_list()
    {
        $where = Util::getMore([
            ['status', ''],
            ['data', ''],
            ['nickname', ''],
            ['page', 1],
            ['cid', 0],
            ['limit', 10],
        ], $this->request);
        return Json::successlayui(StorePink::getPinkList($where));
    }


    /*
     * 删除虚拟拼团
     * @param int $id 拼团id
     * @return json
     * */
    public function delete_pink($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        if (StorePink::be(['is_false' => 0, 'id' => $id])) return Json::fail('不是虚拟拼团无法删除');
        if (StorePink::where('id', $id)->delete())
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }

    /*
     * 下架拼团
     * @param int $id 拼团id
     * @return json
     * */
    public function down_pink($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $res = StorePink::downPink($id);
        if ($res === false)
            return Json::fail(StorePink::getErrorInfo());
        else
            return Json::successful('下架成功');
    }

    /*
     * 助力拼团
     * */
    public function helpe_pink($id = 0)
    {
        $this->assign('id', $id);
        return $this->fetch();
    }

    public function save_helpe_pink()
    {
        list($pink_id, $nickname, $avatar) = Util::postMore([
            ['pink_id', 0],
            ['nickname', ''],
            ['avatar', ''],
        ], $this->request, true);
        if (!$pink_id) return Json::fail('缺少助力团ID');
        if (!$nickname) return Json::fail('请输入助力用户用户名');
        if (!$avatar) return Json::fail('请上传助力用户头像');
        $res = StorePink::helpePink($pink_id, $nickname, $avatar);
        if ($res === false)
            return Json::fail(StorePink::getErrorInfo());
        else
            return Json::successful('助力成功');
    }

    /**拼团人列表
     * @return mixed
     */
    public function order_pink($id)
    {
        if (!$id) return $this->failed('数据不存在');
        $StorePink = StorePink::getPinkUserOne($id);
        if (!$StorePink) return $this->failed('数据不存在!');
        $list = StorePink::getPinkMember($id);
        $list[] = $StorePink;
        if ($id = StorePink::where(['k_id' => $id, 'is_false' => 1])->column('id')) {
            $falsePink = count($id) ? StorePinkFalse::where('pink_id', 'in', $id)->select()->toArray() : [];
            foreach ($falsePink as $item) {
                $item['uid'] = 0;
                $item['is_refund'] = 0;
                $item['is_false'] = 1;
                $item['price'] = $StorePink['price'];
                array_push($list, $item);
            }
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    /*
     * 创建虚拟拼团
     *
     * */
    public function create_pink_false()
    {
        $this->assign('list', Special::PreWhere()->field('id,title')->select());
        return $this->fetch();
    }

}
