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

namespace app\admin\model\user;

use service\PHPExcelService;
use service\SystemConfigService;
use service\UtilService;
use think\Db;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\User;
use app\admin\model\user\UserBill;
use app\admin\model\user\MemberCardBatch;
use app\admin\model\user\Group as GroupModel;
/**
 * 会员卡批次 model
 * Class User
 * @package app\admin\model\user
 */

class MemberCard extends ModelBasic
{
    use ModelTrait;


    public static function getCardOne(array $where)
    {
        if (empty($where)) {
            return false;
        }
        return Db::name("member_card")->where($where)->find();
    }


    /**根据批次id和数量生成卡
     * @param int $batch_id
     * @param int $total_num
     * @return bool
     */
    public static function addCard(int $batch_id, int $total_num)
    {
        if (!$batch_id || $batch_id == 0 || !$total_num || $total_num == 0) {
            return false;
        }
        try{
            $inster_card = array();
            for ($i = 0; $i < $total_num; $i++) {
                $inster_card['card_number'] = UtilService::makeRandomNumber("CR", $batch_id);
                $inster_card['card_password'] = UtilService::makeRandomNumber();
                $inster_card['card_batch_id'] = $batch_id;
                $inster_card['create_time'] = time();
                $res[] = $inster_card;
            }
            //数据切片批量插入，提高性能
            $chunk_inster_card = array_chunk($res, 100 ,true);
            foreach ($chunk_inster_card as $v) {
                Db::table('eb_member_card')->insertAll($v);
            }
            return true;
        }catch (\Exception $e){
            echo $e->getMessage();
        }


    }

    public function getCreateTimeAttr($time)
    {
        return $time;//返回create_time原始数据，不进行时间戳转换。
    }

    public static function getCardList(array $where){
        if (!is_array($where)) {
            return false;
        }
        $batch_where = array();
        if (isset($where['card_batch_id']) && $where['card_batch_id']){
            $batch_where['card_batch_id'] = $where['card_batch_id'];
        }
        if (isset($where['card_number']) && $where['card_number']) {
            $batch_where['card_number'] = array('like', '%'.$where['card_number']);
        }
        if (isset($where['is_use']) && $where['is_use'] != "") {
            if ($where['is_use'] == 1) {
                $batch_where['use_uid'] = array('>', 0);
            }else{
                $batch_where['use_uid'] = 0;
            }

        }
        if (isset($where['is_status']) && $where['is_status'] != "") {

            $batch_where['status'] = $where['is_status'];
        }
        if (isset($where['phone']) && $where['phone']) {
            $user_phone = User::where(['phone' => $where['phone']])->field("uid")->find();

            $data = $count = [];
            if (!$user_phone)  return compact('data','count');
            $batch_where['use_uid'] = $user_phone['uid'];
        }
        $model=new self();
        $model=$model->where($batch_where)->order('use_uid desc');
        if (isset($where['excel']) && $where['excel'] == 1) {
            $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
             self::SaveExcel($data);
        } else {
            $data = ($data = $model->page((int)$where['page'], (int)$where['limit'])->select()) && count($data) ? $data->toArray() : [];
            if (!empty($data)) {
                foreach ($data as $k => $v) {
                    $data[$k]['use_time'] = ($v['use_time'] != 0 || $v['use_time']) ? date('Y-m-d H:i:s', $v['use_time']) : "";
                    if ($v['use_uid'] &&  $v['use_uid'] > 0) {
                        $user_info = User::where(['uid' => $v['use_uid']])->field("account, nickname, phone")->find();
                        $data[$k]['username'] = (isset($user_info['nickname']) && $user_info['nickname']) ? $user_info['nickname'] : $user_info['account'];
                        $data[$k]['user_phone'] = (isset($user_info['phone']) && $user_info['phone']) ? $user_info['phone'] : "";
                    }else{
                        $data[$k]['username'] = "";
                        $data[$k]['user_phone'] = "";
                    }

                }
            }
            $count = self::where($batch_where)->count();
            return compact('data','count');
        }
    }
    /*
    * 保存并下载excel
    * $list array
    * return
    */
    public static function SaveExcel($list)
    {
        $export = [];
        foreach ($list as $index => $item) {
            $batch=MemberCardBatch::where('id',$item['card_batch_id'])->value('title');
            $export[] = [
                $item['id'], $item['card_number'],
                $item['card_password'],
                [$item['status'] == 1 ? '激活' : '冻结'],
                [$item['use_uid'] >0 ? '使用' : '未使用'],
                $item['card_batch_id'],
                $batch,
            ];
        }
        PHPExcelService::setExcelHeader(['编号', '卡号', '密码','激活','使用','批次编号','批次名称'])
            ->setExcelTile('会员卡导出', '会员卡信息' . time(), ' 生成时间：' . date('Y-m-d H:i:s', time()))
            ->setExcelContent($export)
            ->ExcelSave();
    }
    public static function cateDays($code){
        $cate=self::where('card_number',$code)->find();
        $use_day=MemberCardBatch::where('id',$cate['card_batch_id'])->value('use_day');
        return $use_day;
    }
}