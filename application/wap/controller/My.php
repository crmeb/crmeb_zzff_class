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


namespace app\wap\controller;


use Api\Express;
use app\admin\model\system\SystemAdmin;
use app\admin\model\system\SystemConfig;
use app\wap\model\activity\EventSignUp;
use app\wap\model\special\SpecialRecord;
use app\wap\model\special\SpecialRelation;
use app\wap\model\user\SmsCode;
use app\wap\model\special\Grade;
use app\wap\model\store\StoreOrderCartInfo;
use app\wap\model\store\StorePink;
use app\wap\model\store\StoreProduct;
use app\wap\model\store\StoreProductRelation;
use app\wap\model\store\StoreOrder;
use app\wap\model\user\PhoneUser;
use app\wap\model\user\User;
use app\wap\model\user\UserBill;
use app\wap\model\user\UserExtract;
use app\wap\model\user\UserAddress;
use app\wap\model\user\UserSign;
use service\CacheService;
use service\JsonService;
use service\SystemConfigService;
use service\UtilService;
use think\Cookie;
use think\Request;
use think\Session;
use think\Url;

class My extends AuthController
{

    /*
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'index',
            'about_us'
        ];
    }
    /**
     * 退出手机号码登录
     */
    public function logout()
    {
        Cookie::delete('__login_phone');
        Session::delete('__login_phone_num' . $this->uid, 'wap');
        Session::delete('loginUid', 'wap');
        return JsonService::successful('已退出登录');
    }

    public function my_gift()
    {
        return $this->fetch();
    }
    public function sign_list()
    {

        return $this->fetch();
    }
    public function sign_order($type=1,$order_id='')
    {
        $order=EventSignUp::where('order_id',$order_id)->where('paid',1)->find();
        if(!$order) return $this->redirect(Url::build('sign_list'));
        if($type==2){
            $res=SystemAdmin::testUserLevel($this->userInfo);
            if(!$res) return $this->redirect(Url::build('wap/my/index'));
        }
        $this->assign(['type'=>$type,'order_id'=>$order_id,'status'=>$order['status']]);
        return $this->fetch('order_verify');
    }

    public function get_my_gift_list()
    {
        return $this->fetch();
    }

    public function user_info()
    {

        return $this->fetch();
    }

    public function validate_code()
    {
        list($phone, $code,) = UtilService::getMore([
            ['phone', ''],
            ['code', ''],
        ], $this->request, true);
        if (!$phone) return JsonService::fail('请输入手机号码');
        if (!$code) return JsonService::fail('请输入验证码');
        if (!SmsCode::CheckCode($phone, $code)) return Json::fail('验证码验证失败');
        SmsCode::setCodeInvalid($phone, $code);
        return JsonService::successful('验证成功');
    }

    public function get_grade()
    {
        return JsonService::successful(Grade::getPickerData());
    }

    public function save_user_info()
    {
        $data = UtilService::postMore([
            ['avatar', ''],
            ['nickname', ''],
            ['grade_id', 0]
        ], $this->request);
        if (!$data['nickname']) return JsonService::fail('用户昵称不能为空');
        if (User::update($data, ['uid' => $this->uid]))
            return JsonService::successful('保存成功');
        else
            return JsonService::fail('保存失败');
    }

    /**
     * 保存手机号码
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function save_phone()
    {
        if ($this->request->isAjax()) {
            list($phone, $code, $type) = UtilService::getMore([
                ['phone', ''],
                ['code', ''],
                ['type', 0],
            ], $this->request, true);
            if (!$phone) return JsonService::fail('请输入手机号码');
            if (!$code) return JsonService::fail('请输入验证码');
            if (!SmsCode::CheckCode($phone, $code)) return JsonService::fail('验证码验证失败');
            SmsCode::setCodeInvalid($phone, $code);
            $user=User::where(['phone' => $phone, 'is_h5user' => 0])->find();
            if($type && $user){
                if($user['uid']==$this->uid){
                    return JsonService::fail('不能绑定相同手机号');
                }else if($user['uid']!=$this->uid){
                    return JsonService::fail('当前手机号码已绑定微信用户');
                }
            }else if($type==0 && $user){
                if($user) return JsonService::fail('当前手机号码已绑定微信用户');
            }
            //查找H5手机号码账户
            $phoneUser = PhoneUser::where(['phone' => $phone])->find();
            //H5页面有注册过
            if ($phoneUser && $phoneUser['uid']!=$this->uid) {
                //检测当前用户是否是H5用户
                if (User::where('uid', $phoneUser['uid'])->value('is_h5user')) {
                    $res = User::setUserRelationInfos($phone, $phoneUser['uid'], $this->uid);
                    if ($res === false) return JsonService::fail(User::getErrorInfo());
                }
            }else if($phoneUser && $phoneUser['uid']==$this->uid){
                return JsonService::fail('不能绑定相同手机号');
            }
            if (!isset($res)) User::update(['phone' => $phone], ['uid' => $this->uid]);
            return JsonService::successful('绑定成功');
        } else {
            $this->assign('user_phone', $this->userInfo['phone']);
            return $this->fetch();
        }
    }

    /**
     * 个人中心
     * @return mixed
     * @throws \think\Exception
     */
    public function index()
    {
        $this->assign([
            'gold_name'=>SystemConfigService::get('gold_name'),
            'collectionNumber' => SpecialRelation::where('uid', $this->uid)->count(),
            'recordNumber' => SpecialRecord::where('uid', $this->uid)->count(),
            'overdue_time'=>date('Y-m-d',$this->userInfo['overdue_time'])
        ]);
        return $this->fetch();
    }

    public function collect()
    {
        return $this->fetch();
    }

    public function address()
    {
        $this->assign([
            'address' => UserAddress::getUserValidAddressList($this->userInfo['uid'], 'id,real_name,phone,province,city,district,detail,is_default')
        ]);
        return $this->fetch();
    }

    public function recharge()
    {
        return $this->fetch();
    }

    public function edit_address($addressId = '')
    {
        if ($addressId && is_numeric($addressId) && UserAddress::be(['is_del' => 0, 'id' => $addressId, 'uid' => $this->userInfo['uid']])) {
            $addressInfo = UserAddress::find($addressId)->toArray();
        } else {
            $addressInfo = [];
        }
        $this->assign(compact('addressInfo'));
        return $this->fetch();
    }

    public function order($uni = '')
    {
        if (!$uni || !$order = StoreOrder::getUserOrderDetail($this->userInfo['uid'], $uni)) return $this->redirect(Url::build('order_list'));
        $this->assign([
            'order' => StoreOrder::tidyOrder($order, true)
        ]);
        return $this->fetch();
    }

    public function orderPinkOld($uni = '')
    {
        if (!$uni || !$order = StoreOrder::getUserOrderDetail($this->userInfo['uid'], $uni)) return $this->redirect(Url::build('order_list'));
        $this->assign([
            'order' => StoreOrder::tidyOrder($order, true)
        ]);
        return $this->fetch('order');
    }

    public function get_order_list($type = -1, $page = 1, $limit = 10)
    {
        return JsonService::successful(StoreOrder::getSpecialOrderList((int)$type, (int)$page, (int)$limit, $this->uid));
    }

    public function order_list()
    {
        return $this->fetch();
    }

    public function order_reply($unique = '')
    {
        if (!$unique || !StoreOrderCartInfo::be(['unique' => $unique]) || !($cartInfo = StoreOrderCartInfo::where('unique', $unique)->find())) return $this->failed('评价产品不存在!');
        $this->assign(['cartInfo' => $cartInfo]);
        return $this->fetch();
    }

    public function balance()
    {
        $this->assign([
            'userMinRecharge' => SystemConfigService::get('store_user_min_recharge')
        ]);
        return $this->fetch();
    }

    public function integral()
    {
        return $this->fetch();
    }

    public function spread_list()
    {
        $statu = (int)SystemConfig::getValue('store_brokerage_statu');
        if ($statu == 1) {
            if (!User::be(['uid' => $this->userInfo['uid'], 'is_promoter' => 1]))
                return $this->failed('没有权限访问!');
        }
        $this->assign([
            'total' => User::where('spread_uid', $this->userInfo['uid'])->count()
        ]);
        return $this->fetch();
    }

    public function notice()
    {

        return $this->fetch();
    }

    public function express($uni = '')
    {
        if (!$uni || !($order = StoreOrder::getUserOrderDetail($this->userInfo['uid'], $uni))) return $this->failed('查询订单不存在!');
        if ($order['delivery_type'] != 'express' || !$order['delivery_id']) return $this->failed('该订单不存在快递单号!');
        $cacheName = $uni . $order['delivery_id'];
        $result = CacheService::get($cacheName, null);
        if ($result === null) {
            $result = Express::query($order['delivery_id']);
            if (is_array($result) &&
                isset($result['result']) &&
                isset($result['result']['deliverystatus']) &&
                $result['result']['deliverystatus'] >= 3)
                $cacheTime = 0;
            else
                $cacheTime = 1800;
            CacheService::set($cacheName, $result, $cacheTime);
        }
        $this->assign([
            'order' => $order,
            'express' => $result
        ]);
        return $this->fetch();
    }


    public function user_pro()
    {
        $statu = (int)SystemConfig::getValue('store_brokerage_statu');
        if ($statu == 1) {
            if (!User::be(['uid' => $this->userInfo['uid'], 'is_promoter' => 1]))
                return $this->failed('没有权限访问!');
        }
        $userBill = new UserBill();
        $number = $userBill->where('uid', $this->userInfo['uid'])
            ->where('add_time', 'BETWEEN', [strtotime('today -1 day'), strtotime('today')])
            ->where('category', 'now_money')
            ->where('type', 'brokerage')
            ->value('SUM(number)') ?: 0;
        $allNumber = $userBill
            ->where('uid', $this->userInfo['uid'])
            ->where('category', 'now_money')
            ->where('type', 'brokerage')
            ->value('SUM(number)') ?: 0;
        $extractNumber = UserExtract::userExtractTotalPrice($this->userInfo['uid']);
        $this->assign([
            'number' => $number,
            'allnumber' => $allNumber,
            'extractNumber' => $extractNumber
        ]);
        return $this->fetch();
    }


    public function commission()
    {
        $uid = (int)Request::instance()->get('uid', 0);
        if (!$uid) return $this->failed('用户不存在!');
        $this->assign(['uid' => $uid]);
        return $this->fetch();
    }

    public function extract()
    {
        $minExtractPrice = floatval(SystemConfigService::get('user_extract_min_price')) ?: 0;
        $extractInfo = UserExtract::userLastInfo($this->userInfo['uid']) ?: [
            'extract_type' => 'bank',
            'real_name' => '',
            'bank_code' => '',
            'bank_address' => '',
            'alipay_code' => ''
        ];
        $this->assign(compact('minExtractPrice', 'extractInfo'));
        return $this->fetch();
    }

    /**
     * 售后服务  退款订单
     * @return mixed
     */
    public function order_customer()
    {
        return $this->fetch();
    }

    /**
     * 关于我们
     * @return mixed
     */
    public function about_us()
    {
        $this->assign([
            'content' => get_config_content('about_us'),
            'title' => '关于我们'
        ]);
        return $this->fetch('index/agree');
    }

    public function getUserGoldBill()
    {
        $user_info = $this->userInfo;
        list($page, $limit) = UtilService::getMore([
            ['page', 1],
            ['limit', 20],
        ], $this->request, true);
        $where['uid'] = $user_info['uid'];
        $where['category'] = "gold_num";
        return JsonService::successful(UserBill::getUserGoldBill($where, $page, $limit));
    }


    /**
     * 余额明细
     */
    public function bill_detail(){

        return $this->fetch();
    }
}
