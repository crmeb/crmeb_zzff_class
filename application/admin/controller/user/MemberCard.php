<?php

namespace app\admin\controller\user;

use app\admin\model\user\MemberCardBatch;
use app\admin\model\user\MemberCard as MemberCardMode;
use app\admin\model\user\UserVip as UserVipModel;
use app\admin\controller\AuthController;
use app\admin\model\user\SystemVip;
use service\JsonService;
use service\UtilService as Util;
use service\JsonService as Json;
use service\FormBuilder as Form;
use service\UtilService;
use think\Db;
use think\Url;

/**
 * 会员卡管理控制器
 * Class User
 * @package app\admin\controller\user
 */
class MemberCard extends AuthController
{
    public function batch_index()
    {
        $this->assign([
            'activity_type' => 1,
        ]);
        return $this->fetch();
    }

    public function batch_list()
    {
        $where = UtilService::getMore([
            ['title', ""],
            ['page', 1],
            ['limit', 20],
        ]);
        $batch_list = MemberCardBatch::getBatchList($where);
        return Json::successlayui($batch_list);
    }

    public function add_batch()
    {
        $id = $this->request->param('id',0);
        if ($id){
            $batch_data = MemberCardBatch::getBatchOne($id);
            $this->assign('batch', json_encode($batch_data));
        }
        $this->assign([
            'id' => $id,
        ]);
        return $this->fetch();
    }

    public function save_batch()
    {
        $id = $this->request->param('id',0);
        $data = UtilService::postMore([
            ['title', ''],
            ['use_day', 1],
            ['total_num', 1],
            ['status', 0],
        ]);
        if (!isset($data['use_day']) || $data['use_day'] <= 0 || !is_numeric( $data['use_day'])) return Json::fail('体验时间未填写或不合法');
        if (!isset($data['total_num']) || $data['total_num'] <= 0 || !is_numeric( $data['total_num'])) return Json::fail('制卡未填写或不合法');
        if ($data['total_num'] > 6000) return Json::fail('单次制卡数量最高不得超过6000张');

        try{
            MemberCardBatch::beginTrans();
            if ($id) {
                $data['update_time'] = time();
            }else{
                $data['create_time'] = time();
                $batch_id = MemberCardBatch::addBatch($data);
                $batch_card = MemberCardMode::addCard($batch_id, $data['total_num']);
                if($batch_id && $batch_card){
                    $qrcodeUrl=MemberCardBatch::qrcodes_url($batch_id,5);
                    MemberCardBatch::where('id',$batch_id)->update(['qrcode'=>$qrcodeUrl]);
                }
            }
            MemberCardBatch::commitTrans();
            return JsonService::successful('添加成功');
        }catch (\Exception $e) {
            MemberCardBatch::rollbackTrans();
            return JsonService::fail('添加失败');
        }

    }

    /**
     * 快速编辑
     * @param string $field 字段名
     * @param int $id 修改的主键
     * @param string value 修改后的值
     * @return json
     */
    public function set_value($field = '', $id, $value = '', $model_type)
    {

        if ($field == "use_day" && $id) {
            if (!$value || !is_numeric($value) || $value <= 0) return JsonService::fail('非法数值');
            $get_one = MemberCardMode::getCardOne(['card_batch_id' => $id, 'use_uid' => ['>',0]]);
            if ($get_one){
                return JsonService::fail('此批次卡片已经在使用当中，无法进行此非法操作');
            }
        }
        return  set_field_value([$field => $value], ['id' => $id], $value, $model_type);

    }

    public function card_index()
    {
        $data = UtilService::getMore([
            ['activity_type', 2],
            ['card_batch_id', 0],

        ]);
        $batch_list = MemberCardBatch::getBatchAll([]);
        $this->assign([
            'activity_type' => $data['activity_type'],
            'card_batch_id' => $data['card_batch_id'],
            'batch_list' => $batch_list ? $batch_list->toArray() : [],
        ]);
        return $this->fetch();
    }
    public function card_list()
    {
        $card_batch_id = $this->request->param('card_batch_id',0);
        $excel = $this->request->param('excel',0);
        $where = UtilService::getMore([
            ['card_number', ""],
            ['phone', ""],
            ['card_batch_id', $card_batch_id],
            ['is_use',""],
            ['is_status',""],
            ['page', 1],
            ['limit', 20],
            ['excel', $excel],
        ]);
        $card_list = MemberCardMode::getCardList($where);
        return Json::successlayui($card_list);
    }











    public function getUserVipList()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['vip_id', ''],
            ['status', ''],
            ['is_forever', ''],
            ['title', ''],
        ]);
        return Json::successlayui(UserVipModel::getUserVipList($where));
    }

    public function delete($id = '')
    {
        if ($id == '') return Json::fail('缺少参数');
        $uservip = UserVipModel::get($id);
        if (!$uservip) return Json::fail('删除会员信息不存在');
        if ($uservip->is_del == 1) return Json::fail('改会员已删除');
        $uservip->is_del = 1;
        if ($uservip->save())
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }

    public function set_status($id = '', $status = '')
    {
        if ($id == '') return Json::fail('缺少参数');
        $uservip = UserVipModel::get($id);
        if (!$uservip) return Json::fail('会员信息不存在');
        $uservip->status = $status;
        if ($uservip->save())
            return Json::successful($status == 1 ? '锁定成功' : '解锁成功');
        else
            return Json::fail($status == 1 ? '锁定失败' : '解锁失败');
    }
}