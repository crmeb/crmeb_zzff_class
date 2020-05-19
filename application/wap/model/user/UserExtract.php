<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2018/3/3
 */

namespace app\wap\model\user;


use basic\ModelBasic;
use service\SystemConfigService;
use service\WechatTemplateService;
use think\Url;
use traits\ModelTrait;

class UserExtract extends ModelBasic
{
    use ModelTrait;

    //审核中
    const AUDIT_STATUS = 0;
    //未通过
    const FAIL_STATUS = -1;
    //已提现
    const SUCCESS_STATUS = 1;

    protected static $extractType = ['alipay', 'bank', 'weixin'];

    protected static $extractTypeMsg = ['alipay' => '支付宝', 'bank' => '银行卡', 'weixin' => '微信'];

    public static function getPartnerTradeNoId()
    {
        $count = (int)self::where('add_time', ['>=', strtotime(date("Y-m-d"))], ['<', strtotime(date("Y-m-d", strtotime('+1 day')))])->count();
        return 'tk' . date('YmdHis', time()) . (10000 + $count + 1);
    }

    protected static $status = array(
        -1 => '未通过',
        0 => '审核中',
        1 => '已提现'
    );

    /**
     * 用户自主提现记录提现记录,后台执行审核
     * @param array $userInfo 用户个人信息
     * @param array $data 提现详细信息
     * @return bool
     */
    public static function userExtract($userInfo, $data)
    {
        if (!in_array($data['extract_type'], self::$extractType))
            return self::setErrorInfo('提现方式不存在');
        $userInfo = User::get($userInfo['uid']);
        $extractPrice = $userInfo['brokerage_price'];
        if ($extractPrice < 0) return self::setErrorInfo('提现佣金不足' . $data['money']);
        if ($data['money'] > $extractPrice) return self::setErrorInfo('提现佣金不足' . $data['money']);
        if ($data['money'] <= 0) return self::setErrorInfo('提现佣金大于0');
        $balance = bcsub($userInfo['brokerage_price'], $data['money'], 2);
        if ($balance < 0) $balance = 0;
        $insertData = [
            'uid' => $userInfo['uid'],
            'extract_type' => $data['extract_type'],
            'extract_price' => $data['money'],
            'add_time' => time(),
            'balance' => $balance,
            'status' => self::AUDIT_STATUS
        ];
        if (isset($data['name']) && strlen(trim($data['name']))) $insertData['real_name'] = $data['name'];
        else $insertData['real_name'] = $userInfo['nickname'];
        if (isset($data['cardnum'])) $insertData['bank_code'] = $data['cardnum'];
        else $insertData['bank_code'] = '';
        if (isset($data['bankname'])) $insertData['bank_address'] = $data['bankname'];
        else $insertData['bank_address'] = '';
        if (isset($data['weixin'])) $insertData['wechat'] = $data['weixin'];
        else $insertData['wechat'] = $userInfo['nickname'];
        if ($data['extract_type'] == 'alipay') {
            if (!$data['alipay_code']) return self::setErrorInfo('请输入支付宝账号');
            $insertData['alipay_code'] = $data['alipay_code'];
            $mark = '使用支付宝提现' . $insertData['extract_price'] . '元';
        } else if ($data['extract_type'] == 'bank') {
            if (!$data['cardnum']) return self::setErrorInfo('请输入银行卡账号');
            if (!$data['bankname']) return self::setErrorInfo('请输入开户行信息');
            $mark = '使用银联卡' . $insertData['bank_code'] . '提现' . $insertData['extract_price'] . '元';
        } else if ($data['extract_type'] == 'weixin') {
            if (!$data['weixin']) return self::setErrorInfo('请输入微信账号');
            $mark = '使用微信提现' . $insertData['extract_price'] . '元';
        }
        self::beginTrans();
        try {
            $res1 = self::create($insertData);
            if (!$res1) return self::setErrorInfo('提现失败');
            $res2 = User::edit(['brokerage_price' => $balance], $userInfo['uid'], 'uid');
            $res3 = UserBill::expend('余额提现', $userInfo['uid'], 'now_money', 'extract', $data['money'], $res1['id'], $balance, $mark);
            $res = $res2 && $res3;
            if ($res) {
                try {
                    WechatTemplateService::sendTemplate(
                        WechatUser::uidToOpenid($userInfo['uid']),
                        WechatTemplateService::USER_BALANCE_CHANGE,
                        [
                            'first' => '你好,申请余额提现成功!',
                            'keyword1' => '余额提现',
                            'keyword2' => date('Y-m-d'),
                            'keyword3' => $data['extract_price'],
                            'remark' => '点击查看我的余额明细'
                        ],
                        Url::build('wap/My/balance', [], true, true)
                    );
                } catch (\Throwable $e) {
                }
                self::commitTrans();
                //发送模板消息
                return true;
            } else return self::setErrorInfo('提现失败!');
        } catch (\Exception $e) {
            self::rollbackTrans();
            return self::setErrorInfo('提现失败!');
        }
    }

    /**
     * 获得用户最后一次提现信息
     * @param $openid
     * @return mixed
     */
    public static function userLastInfo($uid)
    {
        return self::where(compact('uid'))->order('add_time DESC')->find();
    }

    /**
     * 获得用户提现总金额
     * @param $uid
     * @return mixed
     */
    public static function userExtractTotalPrice($uid)
    {
        return self::where('uid', $uid)->where('status', self::SUCCESS_STATUS)->value('SUM(extract_price)') ?: 0;
    }

}