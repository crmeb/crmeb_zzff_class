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

namespace app\admin\controller\setting;

use app\admin\controller\AuthController;
use app\admin\model\system\SystemConfig as ConfigModel;
use service\JsonService as Json;
use service\FormBuilder as Form;
use service\CacheService;
use think\Url;
use service\CrmebPlatService;
use service\sms\storage\Sms;
use service\SystemConfigService;
use app\admin\model\system\SmsAccessToken;

/**
 * crmeb 平台
 * Class SystemPlat
 * @package app\admin\controller\setting
 */
class SystemPlat extends AuthController
{
    protected $account = NULL;

    protected $secret = NULL;
    /**
     * @var $crmebPlatHandle
     */
    protected $crmebPlatHandle;
    /**
     * @var $smsHandle
     */
    protected $smsHandle;
    /**
     * @var $expressHandle
     */
    protected $expressHandle;
    /**
     * @var $productHandle
     */
    protected $productHandle;

    protected $allowAction = ['index', 'verify', 'login', 'go_login', 'register', 'go_register', 'modify', 'go_modify', 'forget', 'go_forget', 'loginOut', 'meal', 'sms_temp'];

    /**
     * @var string
     */
    protected $cacheTokenPrefix = "_crmeb_plat";

    protected $cacheKey;

    protected function _initialize()
    {
        parent::_initialize();
        $this->account = SystemConfigService::get('sms_account');
        $this->secret = SystemConfigService::get('sms_token');
        $this->crmebPlatHandle = new CrmebPlatService();
        $this->smsHandle = new Sms();
        $this->cacheKey = md5($this->account . '_' . $this->secret . $this->cacheTokenPrefix);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if (!CacheService::get($this->cacheKey, '')) {
            return $this->redirect(Url::build('login').'?url=index');
        }
        list($out, $type) = parent::postMore([
            ['out', 0],
            ['type', 'sms']
        ], null, true);
        try {
            $info = $this->crmebPlatHandle->info();
            if (!isset($info['status']) || $info['status'] != 200) {
                $info = [];
            }else{
                $info =$info['data'];
            }
        } catch (\Throwable $e) {
            $info = [];
        }
        $this->assign('info', $info);
        $this->assign('type', $type);
        if ($out == 0 && $info) {
            return $this->fetch();
        } else {
            $this->assign('account', $this->account);
            $this->assign('password', $this->secret);
            return $this->fetch('login');
        }

    }

    /**
     * 获取短信验证码
     */
    public function verify()
    {
        list($phone) = parent::postMore([
            ['phone', '']
        ], null, true);
        if (!$phone) {
            return Json::fail('请输入手机号');
        }
        if (!check_phone($phone)) {
            return Json::fail('请输入正确的手机号');
        }
        $data=$this->crmebPlatHandle->code($phone);
        if (!isset($data['status']) || $data['status'] != 200) {
            return Json::fail($data['msg']);
        }else{
            return Json::successful('获取成功');
        }
    }

    /**
     * 登录页面
     * @return string
     * @throws \Exception
     */
    public function login($url='')
    {
        $this->assign('str', $url);
        $this->assign('account', $this->account);
        $this->assign('password', $this->secret);
        return $this->fetch();
    }

    /**
     * 退出登录
     * @return string
     * @throws \Exception
     */
    public function loginOut()
    {
        CacheService::rm($this->cacheKey);
        return Json::successful('退出成功', $this->crmebPlatHandle->loginOut());
    }


    /**
     * 登录逻辑
     */
    public function go_login()
    {
        $data = parent::postMore([
            ['account', ''],
            ['password', '']
        ]);
        if (!$data['account']) {
            return Json::fail('请输入账号');
        }
        if (!$data['password']) {
            return Json::fail('请输入秘钥');
        }
        $this->save_basics(['sms_account' => $data['account'], 'sms_token' => $data['password']]);
        $token = $this->crmebPlatHandle->login($data['account'], $data['password']);
        if($token){
            return Json::successful('登录成功', $token);
        }else{
            return Json::fail('登录失败,账号或密码有误！');
        }

    }

    /**
     * 注册页面
     * @return string
     * @throws \Exception
     */
    public function register()
    {
        return $this->fetch();
    }

    /**
     * 注册逻辑
     */
    public function go_register()
    {
        $data = parent::postMore([
            ['account', ''],
            ['phone', ''],
            ['password', ''],
            ['verify_code', ''],
        ]);
        if (!$data['account']) {
            return Json::fail('请输入账号');
        }
        if (!$data['phone']) {
            return Json::fail('请输入手机号');
        }
        if (!check_phone($data['phone'])) {
            return Json::fail('请输入正确的手机号');
        }
        if (!$data['password']) {
            return Json::fail('请设置秘钥');
        }
        if (strlen($data['password']) < 6 || strlen($data['password']) > 32) {
            return Json::fail('密码长度6~32位');
        }
        if (!$data['verify_code']) {
            return Json::fail('请先获取短信验证码');
        }
        $result =$this->crmebPlatHandle->register($data['account'], $data['phone'], $data['password'], $data['verify_code']);
        if (!isset($result['status']) || $result['status'] != 200) {
            return Json::fail($result['msg']);
        }else{
            $result =$result['data'];
        }
        $this->save_basics(['sms_account' => $data['account'], 'sms_token' => $data['password']]);
        return Json::successful('注册成功', $result);
    }

    /**
     * 修改秘钥页面
     * @return string
     * @throws \Exception
     */
    public function modify()
    {
        $this->assign('account', $this->account);
        return $this->fetch();
    }

    /**
     * 修改秘钥逻辑
     */
    public function go_modify()
    {
        $data = parent::postMore([
            ['account', ''],
            ['phone', ''],
            ['password', ''],
            ['verify_code', ''],
        ]);
        if (!$data['account']) {
            return Json::fail('请输入账号');
        }
        if (!$data['phone']) {
            return Json::fail('请输入手机号');
        }
        if (!check_phone($data['phone'])) {
            return Json::fail('请输入正确的手机号');
        }
        if (!$data['password']) {
            return Json::fail('请设置秘钥');
        }
        if (strlen($data['password']) < 6 || strlen($data['password']) > 32) {
            return Json::fail('密码长度6~32位');
        }
        if (!$data['verify_code']) {
            return Json::fail('请先获取短信验证码');
        }
        $result = $this->crmebPlatHandle->modify($data['account'], $data['phone'], $data['password'], $data['verify_code']);
        if (!isset($result['status']) || $result['status'] != 200) {
            return Json::fail($result['msg']);
        }else{
            $result =$result['data'];
        }
        $this->save_basics(['sms_account' => $data['account'], 'sms_token' => $data['password']]);
        return Json::successful('修改成功', $result);
    }

    /**
     * 找回账号
     * @return string
     * @throws \Exception
     */
    public function forget()
    {
        return $this->fetch();
    }

    /**
     * 找回账号逻辑
     */
    public function go_fotget()
    {
        $data = $where = parent::postMore([
            ['phone', ''],
            ['verify_code', ''],
        ]);
        if (!isset($data['phone']) || $data['phone']) {
            return Json::fail('请输入手机号');
        }
        if (!check_phone($data['phone'])) {
            return Json::fail('请输入正确的手机号');
        }
        if (!isset($data['verify_code']) || $data['verify_code']) {
            return Json::fail('请先获取短信验证码');
        }
        $result = $this->crmebPlatHandle->forget($data['phone'], $data['verify_code']);
        if (!isset($result['status']) || $result['status'] != 200) {
            return Json::fail($result['msg']);
        }else{
            $result =$result['data'];
        }
        return Json::successful('修改成功', $result);
    }

    /**
     * 获取消费记录
     */
    public function record()
    {
        list($type, $page, $limit) = parent::getMore([
            ['type', 'sms'],
            ['page', 1],
            ['limit', 20]
        ], null, true);
        $result = $this->crmebPlatHandle->record($type, $page, $limit);
        if (!isset($result['status']) || $result['status'] != 200) {
            return Json::fail($result['msg']);
        }else{
            $result =$result['data'];
        }
        return Json::successlayui($result['count'],$result['data']);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function meal()
    {
        if (!CacheService::get($this->cacheKey, '')) {
            return $this->redirect(Url::build('login').'?url=meal');
        }
        try {
            $info = $this->crmebPlatHandle->info();
            if (!isset($info['status']) || $info['status'] != 200) {
                $info = [];
            }else{
                $info =$info['data'];
            }
        } catch (\Throwable $e) {
            $info = [];
        }
        $this->assign('info', $info);
        return $this->fetch();
    }

    /**
     * 获取套餐列表
     */
    public function get_meal()
    {
        list($type) = parent::getMore([
            ['type', 'sms']
        ], null, true);
        $result=$this->crmebPlatHandle->meal($type);
        if (!isset($result['status']) || $result['status'] != 200) {
            return Json::fail($result['msg']);
        }else{
            $result =$result['data'];
        }
        return Json::successful($result);
    }

    /**
     * 获取支付二维码
     * @return string
     * @throws \Exception
     */
    public function pay()
    {
        list($meal_id, $price, $num, $type, $pay_type) = parent::postMore([
            ['meal_id', 0],
            ['price', ''],
            ['num', 0],
            ['type', ''],
            ['pay_type', 'weixin']
        ], null, true);
        if (!$meal_id) {
            return Json::fail('请选择套餐');
        }
        try {
            $info = $this->crmebPlatHandle->info();
            if (!isset($info['status']) || $info['status'] != 200) {
                $info = [];
            }else{
                $info =$info['data'];
            }
        } catch (\Throwable $e) {
            $info = [];
        }
        if(!$info) {
            return Json::fail('用户信息不存在！');
        }
        $payContent=$this->crmebPlatHandle->pay($type, $meal_id, $price, $num, $pay_type);
        if (!isset($payContent['status']) || $payContent['status'] != 200) {
            $payContent = [];
        }else{
            $payContent =$payContent['data'];
        }
        if (isset($info['sms']['open']) && $info['sms']['open'] == 1){
            $payContent['code_show']=true;
        }else{
            $payContent['code_show']=false;
        }
        return Json::successful($payContent);
    }


    /**
     * 保存一号通配置
     */
    public function save_basics($data)
    {
        if ($data) {
            CacheService::clear();
            foreach ($data as $k => $v) {
                ConfigModel::edit(['value' => json_encode($v)], $k, 'menu_name');
            }
        }
        return true;
    }

    /**
     * 开通短信服务页面
     * @return string
     * @throws \Exception
     */
    public function sms_open()
    {
        try {
            $info = $this->crmebPlatHandle->info();
            if (!isset($info['status']) || $info['status'] != 200) {
                $info = [];
            }else{
                $info =$info['data'];
            }
        } catch (\Throwable $e) {
            $info = [];
        }
        $this->assign('info', $info);
        return $this->fetch();
    }

    /**
     * 处理开通短信服务
     */
    public function go_sms_open()
    {
        list($sign) = parent::postMore([
            ['sign', '']
        ], null, true);
        if (!$sign) {
            return Json::fail('请输入短信签名');
        }
        try{
           $sign= $this->smsHandle->setSign($sign)->open();
            if (!isset($sign['status']) || $sign['status'] != 200) {
                return Json::fail($sign['msg']);
            }else{
                return Json::successful('开通成功，可以在短信账户中查看');
            }
        }catch (\Throwable $e){
            return Json::fail('开通失败或服务已开通');
        }
    }

    /**
     * 短信账户信息
     */
    public function sms_info()
    {
        return Json::successful($this->smsHandle->info());
    }

    /**
     * 修改签名页面
     * @return string
     * @throws \Exception
     */
    public function sms_modify()
    {
        $this->assign('account', $this->account);
        return $this->fetch();
    }

    /**
     * 处理修改签名
     */
    public function go_sms_modify()
    {
        list($sign,$phone,$verify_code) = parent::postMore([
            ['sign', ''],
            ['phone', ''],
            ['verify_code', ''],
        ], null, true);
        if (!$sign) {
            return Json::fail('请输入短信签名');
        }
        if (!isset($phone) || !$phone) {
            return Json::fail('请输入手机号');
        }
        if (!check_phone($phone)) {
            return Json::fail('请输入正确的手机号');
        }
        if (!isset($verify_code) || !$verify_code) {
            return Json::fail('请先获取短信验证码');
        }
        try{
           $result= $this->smsHandle->modify($sign,$phone,$verify_code);
           if (!isset($result['status']) || $result['status'] != 200) {
                return Json::fail($result['msg'] ? $result['msg'] : '发生异常，请稍后重试');
            }else{
               return Json::successful($result['msg']);
           }
        }catch (\Throwable $e){
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 短信模版页面
     */
    public function sms_temp()
    {
        if (!CacheService::get($this->cacheKey, '')) {
            return $this->redirect(Url::build('login').'?url=sms_temp');
        }
        list($type) = parent::getMore([
            ['type', 'temps'],
        ], null, true);
        $this->assign('type', $type);
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return string
     * @throws \FormBuilder\exception\FormBuilderException
     */
    public function create()
    {
        $field = [
            Form::input('title', '模板名称'),
            Form::textarea('text', '模板内容示例', '您的验证码是：{$code}，有效期为{$time}分钟。如非本人操作，可不用理会。（模板中的{$code}和{$time}需要替换成对应的变量，请开发者知晓。修改此项无效！）')->readonly(true),
            Form::input('content', '模板内容')->type('textarea'),
            Form::radio('type', '模板类型', 1)->options([['label' => '验证码', 'value' => 1], ['label' => '通知', 'value' => 2], ['label' => '推广', 'value' => 3]])
        ];
        $form = Form::make_post_form('申请短信模板', $field, Url::build('go_sms_temps_apply'), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 短信模版
     */
    public function get_sms_temps()
    {
        list($page, $limit, $temp_type) = parent::getMore([
            ['page', 1],
            ['limit', 20],
            ['temp_type', ''],
        ], null, true);
        $data=$this->smsHandle->temps($page, $limit, $temp_type);
        if (!isset($data['status']) || $data['status'] != 200) {
            return Json::fail($data['msg']);
        }else{
            $sms_platform_selection=SystemConfigService::get('sms_platform_selection');
            $smsTemplateCode=SystemConfigService::get('smsTemplateCode');
            if($sms_platform_selection==2){
                foreach ($data['data']['data'] as &$value){
                    if($value['temp_id']==$smsTemplateCode){
                        $value['is_use']=1;
                    }else{
                        $value['is_use']=0;
                    }
                }
            }
            return Json::successlayui($data['data']);
        }
    }

    /**
     * 使用短信模板
     */
    public function sms_temp_use()
    {
        list($temp_id) = parent::getMore([
            ['temp_id', 0],
        ], null, true);
        if($sms_platform_selection=SystemConfigService::get('sms_platform_selection')!=1){
            $info = $this->crmebPlatHandle->info();
            if (!isset($info['status']) || $info['status'] != 200) {
                $info = [];
            }else{
                $info =$info['data'];
            }
            $res1=SystemConfigService::setOneValue('smsTemplateCode',$temp_id);
            $res2=SystemConfigService::setOneValue('smsSignName',$info['sms']['sign']);
            $res= $res1 && $res2;
            if($res){
                return Json::successful('设置成功');
            }else{
                return Json::fail('设置失败');
            }
        }else{
            return Json::fail('请选择把短信平台切换成crmeb短信平台');
        }
    }
    /**
     * 短信模版申请记录
     */
    public function get_sms_appls()
    {
        list($temp_type, $page, $limit) = parent::getMore([
            ['temp_type', ''],
            ['page', 1],
            ['limit', 20]
        ], null, true);
        $data=$this->smsHandle->applys($temp_type, $page, $limit);
        if (!isset($data['status']) || $data['status'] != 200) {
            return Json::fail($data['msg']);
        }else{
            return Json::successlayui($data['data']);
        }
    }

    /**
     * 短信发送记录
     */
    public function sms_record()
    {
        list($record_id) = parent::getMore([
            ['record_id', 0],
        ], null, true);
        return Json::successful($this->smsHandle->record($record_id));
    }

    /**
     * 模版申请页面
     * @return string
     * @throws \Exception
     */
    public function sms_temps_apply()
    {
        return $this->fetch();
    }

    /**
     * 处理申请模版
     */
    public function go_sms_temps_apply()
    {
        list($type, $title, $content) = parent::postMore([
            ['type', 1],
            ['title', ''],
            ['content', '']
        ], null, true);
        if (!$type) {
            return Json::fail('请选择模版类型');
        }
        if (!$title) {
            return Json::fail('请输入模板名称');
        }
        if (!$content) {
            return Json::fail('请输入模版内容');
        }
        $data=$this->smsHandle->apply($title, $content, $type);
        if (!isset($data['status']) || $data['status'] != 200) {
            return Json::fail($data['msg']);
        }else{
            return Json::successful('申请成功');
        }
    }
}
