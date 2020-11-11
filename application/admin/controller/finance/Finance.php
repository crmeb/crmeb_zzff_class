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

namespace app\admin\controller\finance;

use app\admin\controller\AuthController;
use app\admin\model\user\UserBill;
use service\JsonService as Json;
use app\admin\model\finance\FinanceModel;
use service\SystemConfigService;
use service\UtilService as Util;
use service\FormBuilder as Form;
use service\HookService;
use think\Url;
use app\admin\model\user\User;
use app\admin\model\user\UserExtract;

/**
 * 微信充值记录
 * Class UserRecharge
 * @package app\admin\controller\user
 */
class Finance extends AuthController
{

    /**
     * 显示资金记录
     */
    public function bill()
    {
        $category = $this->request->param('category','now_money');
        $bill_where_op = FinanceModel::bill_where_op($category);
        $list = UserBill::where('type', $bill_where_op['type']['op'], $bill_where_op['type']['condition'])
            ->where('category', $bill_where_op['category']['op'], $bill_where_op['category']['condition'])
            ->field(['title', 'type'])
            ->group('type')
            ->distinct(true)
            ->select()
            ->toArray();
        $this->assign('selectList', $list);
        $this->assign('category', $category);
        $this->assign('gold_name', $category == "now_money" ? "金额" : SystemConfigService::get("gold_name"));
        return $this->fetch();
    }

    /**
     * 显示资金记录ajax列表
     */
    public function billlist()
    {
        $where = Util::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', ''],
            ['limit', 20],
            ['page', 1],
            ['type', ''],
            ['category', 'now_money'],
        ]);
        return Json::successlayui(FinanceModel::getBillList($where));
    }

    /**
     *保存资金监控的excel表格
     */
    public function save_bell_export()
    {
        $where = Util::getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', ''],
            ['type', ''],
            ['category', 'gold_num'],
        ]);
        FinanceModel::SaveExport($where);
    }

    /**
     * 显示佣金记录
     */
    public function commission_list()
    {
        $this->assign('is_layui', true);
        return $this->fetch();
    }

    /**
     * 佣金记录异步获取
     */
    public function get_commission_list()
    {
        $get = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['nickname', ''],
            ['price_max', ''],
            ['price_min', ''],
            ['order', '']
        ]);
        return Json::successlayui(User::getCommissionList($get));
    }

    /**
     * 佣金详情
     */
    public function content_info($uid = '')
    {
        if ($uid == '') return $this->failed('缺少参数');
        $this->assign('userinfo', User::getUserinfo($uid));
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    /**
     * 佣金提现记录个人列表
     */
    public function get_extract_list($uid = '')
    {
        if ($uid == '') return Json::fail('缺少参数');
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', '']
        ]);
        return Json::successlayui(UserBill::getExtrctOneList($where, $uid));
    }

}

