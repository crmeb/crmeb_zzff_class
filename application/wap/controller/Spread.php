<?php

namespace app\wap\controller;


use app\wap\model\user\SmsCode;
use app\wap\model\special\Grade;
use app\wap\model\store\StoreOrder;
use app\wap\model\store\StorePink;
use app\wap\model\user\User;
use app\wap\model\user\UserBill;
use app\wap\model\user\UserExtract;
use app\wap\model\user\WechatUser;
use service\CanvasService;
use service\GroupDataService;
use service\JsonService;
use service\SystemConfigService;
use service\UtilService;
use service\WechatService;
use think\Db;
use think\response\Json;
use think\Session;
use app\wap\model\special\Special;
use think\Url;

class Spread extends AuthController
{
    public function spread()
    {
        $data['number'] = UserBill::whereTime('add_time', 'today')->where('uid', $this->uid)->where('category', 'now_money')
            ->where('type', 'in', ['rake_back', 'rake_back_one', 'brokerage','rake_back_two'])->sum('number');
        $orderIds = StoreOrder::whereTime('add_time', 'today')->where('refund_status', 0)->where('link_pay_uid', 'in', $this->uid)->where('paid', 1)->field('order_id,pink_id')->select();
        $orderids1 = StoreOrder::whereTime('add_time', 'today')->where('refund_status', 0)->where('spread_uid', 'in', $this->uid)->where('paid', 1)->field('order_id,pink_id')->select();
        $orderids = array_merge(count($orderIds) ? $orderIds->toArray() : [], count($orderids1) ? $orderids1->toArray() : []);
        $order_count = 0;
        foreach ($orderids as $item) {
            if ($item['pink_id']) {
                $res = StorePink::where(['id' => $item['pink_id'], 'is_refund' => 0, 'status' => 3, 'order_id' => $item['order_id']])->count();
                if ($res) $order_count++;
            } else {
                $order_count++;
            }
        }
        $data['order_count'] = $order_count;
        $data['spread_count'] = User::whereTime('spread_time', 'today')->where('is_promoter', 1)->where('spread_uid', $this->uid)->count();
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 提现页面
     * @return mixed
     */
    public function withdraw()
    {
        if (Session::has('form__token__')) Session::delete('form__token__');
        $token = md5(time() . $this->uid . $this->request->ip());
        Session::set('form__token__', $token);
        $now_moeny = User::where('uid', $this->uid)->value('now_money');
        $this->assign('number', floatval($now_moeny));
        $this->assign('token', $token);
        $this->assign('extract_min_money', (int)SystemConfigService::get('extract_min_money'));
        $this->assign('extract_bank', json_encode(GroupDataService::getData('extract_bank') ?: []));
        $this->assign('brokerage_price', $this->userInfo['brokerage_price']);
        $this->assign('extract_min_money', (float)SystemConfigService::get('extract_min_money'));
        return $this->fetch();
    }

    /**
     * 保存提现信息发起企业付款到个人
     * @praem $number int 提现金额
     * @return json
     * */
    public function save_withdraw($token = '')
    {

        if (!$token) return JsonService::fail('token不能为空');
        if (!Session::has('form__token__')) return JsonService::fail('请刷新页面后重试!');
        if (Session::get('form__token__') != $token) return JsonService::fail('token验证失败,非法操作');
        Session::delete('form__token__');
        $extractInfo = UtilService::postMore([
            ['alipay_code', ''],
            ['extract_type', ''],
            ['money', 0],
            ['name', ''],
            ['bankname', ''],
            ['cardnum', ''],
            ['weixin', ''],
        ]);

        if (UserExtract::userExtract($this->userInfo, $extractInfo)) {
            return JsonService::successful('申请成功');
        } else {
            return JsonService::fail(UserExtract::getErrorInfo());
        }
    }


    /*
     * 专题推广
     *
     * */
    public function special()
    {

        return $this->fetch();
    }

    /*
     * 获取年级列表
     *
     * */
    public function get_grade_list()
    {
        return JsonService::successful(Grade::getPickerData());
    }

    /*
     * 获取每个年级下的专题并分页
     * */
    public function getSpecialSpread()
    {
        $where = UtilService::getMore([
            ['limit', 10],
            ['page', 1],
            ['grade_id', 0],
        ]);
        return JsonService::successful(Special::getSpecialSpread($where, $this->uid));
    }

    /*
     * 专题推广二维码
     * */
    public function poster_special($special_id = 0)
    {
        if (!$special_id) $this->failed('缺少专题id无法查看海报');
        $special = Special::getSpecialInfo($special_id);
        if ($special === false) $this->failed(Special::getErrorInfo());
        if (!$special['poster_image']) $this->failed('您查看的海报不存在');
        $url = SystemConfigService::get('site_url') . Url::build('special/details') . '?id=' . $special['id'] . '&link_pay_uid=' . $this->uid . '&link_pay=1&spread_uid=' . $this->uid . '#link_pay';
        try {
            $filename = CanvasService::foundCode($special['id'], $url, $special['poster_image']);
        } catch (\Exception $e) {
            return $this->failed($e->getMessage());
        }
        $this->assign([
            'title' => $special['title'],
            'url' => $url,
            'special' => $special,
            'filename' => $filename,
            'promoter_guide' => SystemConfigService::get('promoter_guide'),
        ]);
        return $this->fetch();
    }

    /*
     * 我的推广人
     *
     * */
    public function my_promoter()
    {
        $uids = User::where('spread_uid', $this->uid)->group('uid')->column('uid');
        $data['one_spread_count'] = count($uids);
        if ($data['one_spread_count']) {
            $data['order_count'] = UserBill::where(['u.paid' => 1, 'u.is_del' => 0])->where('a.category', 'now_money')->where('a.type', 'in', ['brokerage'])->group('u.id')->where('a.uid', 'in', $uids)->join("__STORE_ORDER__ u", 'u.id=a.link_id')->alias('a')->count();
        } else {
            $data['order_count'] = 0;
        }
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 佣金明细
     * @return mixed
     */
    public function commission()
    {
        $uids = User::where('spread_uid', $this->uid)->column('uid');
        $data['spread_two'] = 0;
        $data['spread_one'] = 0;
        if ($uids) {
            $uids1 = User::where('spread_uid', 'in', $uids)->group('uid')->column('uid');
            $data['spread_one'] = UserBill::where(['a.uid' => $this->uid, 'a.type' => 'brokerage', 'a.category' => 'now_money'])->alias('a')
                ->join('store_order o', 'o.id = a.link_id')
                ->whereIn('o.uid', $uids)
                ->where('a.link_id', 'neq', 0)->sum('a.number');
            if ($uids1) {
                $data['spread_two'] = UserBill::where(['a.uid' => $this->uid, 'a.type' => 'brokerage', 'a.category' => 'now_money'])->alias('a')
                    ->join('store_order o', 'o.id = a.link_id')
                    ->whereIn('o.uid', $uids1)
                    ->where('a.link_id', 'neq', 0)->sum('a.number');
            }
        }
        $data['sum_spread'] = bcadd($data['spread_one'], $data['spread_two'], 2);
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 推广明细
     *  @param $type int 明细类型
     *  @return html
     * */
    public function spread_detail($type = 0)
    {
        $this->assign('type', $type);
        return $this->fetch();
    }

    public function get_spread_list()
    {
        $where = UtilService::getMore([
            ['page', 1],
            ['limit', 10],
            ['type', 0],
            ['data', ''],
        ]);
        return JsonService::successful(UserBill::getSpreadList($where, $this->uid));
    }

    /*
     * 推广海报
     *
     * */
    public function poster_spread()
    {
        $spread_poster_url = SystemConfigService::get('spread_poster_url');
        if (!$spread_poster_url) return $this->failed('海报不存在');
        $url = SystemConfigService::get('site_url') . Url::build('Spread/become_promoter', ['s_spread_uid' => $this->uid]);
        try {
            $filename = CanvasService::foundCode($this->uid, $url, $spread_poster_url,'user_');
        } catch (\Exception $e) {
            return $this->failed($e->getMessage());
        }
        $this->assign([
            'url' => $url,
            'filename' => $filename,
            'image' => SystemConfigService::get('site_url') . '/' . $filename,
            'site_name' => SystemConfigService::get('site_name'),
            'promoter_guide' => SystemConfigService::get('promoter_guide'),
        ]);
        return $this->fetch();
    }

    /*
     * 绑定推广人
     * @param int $pread_uid
     * @return json
     * */
    public function save_promoter($s_spread_uid = 0)
    {
        if (!$s_spread_uid) return JsonService::fail('缺少推广人UID');
        list($phone, $code) = UtilService::postMore([
            ['phone', ''],
            ['code', ''],
        ], $this->request, true);
        if (!$phone || !$code) return $this->failed('请输入登录账号');
        if (!$code) return $this->failed('请输入验证码');
        if (!SmsCode::CheckCode($phone, $code)) return JsonService::fail('验证码验证失败');
        SmsCode::setCodeInvalid($phone, $code);
        if ($this->userInfo['spread_uid'] == $s_spread_uid && $this->userInfo['is_promoter']) return JsonService::fail('您已绑定此推广人,请勿重复绑定');
        if ($this->userInfo['spread_uid'] && $this->userInfo['is_promoter']) return JsonService::fail('您已有推广人,无法绑定!');
        if ($this->userInfo['is_promoter']) return JsonService::fail('您已经成为推广人,无法绑定!');
        if ($this->userInfo['is_promoter'] && $this->userInfo['is_senior']) return JsonService::fail('您已成为高级推广人,无法绑定');
        $data = ['phone' => $phone];
        $data = User::manageSpread($s_spread_uid, $data, true);
        if ($data === false) return JsonService::fail(User::getErrorInfo());
        if (User::where('uid', $this->uid)->update($data))
            return JsonService::successful('恭喜您,加入成功!');
        else
            return JsonService::fail('很抱歉加入失败!');
    }

    /*
     * 新增推广人注册
     *
     * */
    public function become_promoter($s_spread_uid = 0)
    {
        if (!$s_spread_uid) $this->failed('缺少推广人uid');
        $this->assign('spread_uid', $s_spread_uid);
        $this->assign('promoter_content', SystemConfigService::get('promoter_content'));
        return $this->fetch();
    }

    /*
     * 推广人列表获取
     *
     * */
    public function spread_list()
    {
        $where = UtilService::getMore([
            ['page', 1],
            ['limit', 10],
            ['search', ''],
            ['now_money_order', ''],
            ['special_order', ''],
        ]);
        return JsonService::successful(User::GetSpreadList($where, $this->uid));
    }

    /*
     * 移除当前用下的推广人
     * @param int $uid 需要移除的用户id
     * */
    public function remove_spread($uid = 0)
    {
        if (!$uid) return JsonService::fail('缺少用户id');
        $res = User::where('uid', $uid)->update(['spread_uid' => 0, 'valid_time' => 0]);
        if ($res)
            return JsonService::successful('移除成功');
        else
            return JsonService::fail('移除失败');
    }
}