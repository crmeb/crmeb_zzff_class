<?php

namespace app\admin\controller\system;

use app\admin\controller\AuthController;
use service\FormBuilder;
use app\admin\library\formBuilderDriver\Upload;
use app\admin\model\store\StoreOrder;
use app\admin\model\store\StoreProduct;
use app\admin\model\user\User;
use app\admin\model\user\UserEnter;
use app\merchant\model\merchant\MerchantAdmin;
use app\merchant\model\merchant\MerchantMenus;
use app\merchant\model\merchant\MerchantReconciliation;
use service\HookService;
use service\JsonService;
use service\SystemConfigService;
use service\UploadService;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UtilService;
use think\Request;
use think\Session;
use think\Url;
use app\admin\model\system\Merchant as MerchantModel;
use app\admin\model\system\MerchantAdmin as MerchantAdminModel;
use app\admin\model\store\StoreOrder as StoreOrderModel;
use app\admin\model\routine\RoutineTemplate;

/**
 * Class Merchant
 * @package app\admin\controller\system
 */
class Merchant extends AuthController
{
    /**
     * @return mixed
     */
    public function index()
    {
        $where = Util::getMore([
            ['mer_name', ''],
            ['mer_phone', ''],
            ['status', ''],
        ], $this->request);
        $this->assign('where', $where);
        $this->assign(MerchantModel::systemPage($where));
        return $this->fetch();
    }

    public function cooperation($id)
    {
        $this->assign(['title' => '添加管理员', 'action' => Url::build('set_cooperation', ['id' => $id]), 'rules' => $this->cooperation_rules($id)->getContent()]);
        return $this->fetch('public/common_form');
    }

    public function cooperation_rules($id)
    {
        if (!$id || !($merInfo = MerchantModel::get($id))) return $this->failed('商户不存在!');
        FormBuilder::text('mer_name', '商户名称', $merInfo['mer_name'])->disabled();
        FormBuilder::text('mer_name', '商户账号', MerchantAdmin::where('mer_id', $id)->where('level', 0)->value('account'))->disabled();
        FormBuilder::textarea('mark', '备注');
        FormBuilder::radio('is_edition', '商户类型：', [['label' => '合作版', 'value' => 2], ['label' => '专业版', 'value' => 1], ['label' => '免费版', 'value' => 0]], $merInfo['is_edition']);
        $time = time();
        if ($time < $merInfo['expire_time']) {
            $d = $merInfo['expire_time'] - $time;
            $d = ceil(bcdiv($d, 86400, 2));
        } else {
            $d = 0;
        }
        FormBuilder::number('expire_time', '到期时间(天)', $d)->min(0);
        return FormBuilder::builder();
    }

    public function set_cooperation($id = '')
    {
        if ($id == '') return Json::fail('设置失败!');
        $data = Util::postMore([
            ['mark', ''],
            ['is_edition', 0],
            ['expire_time', 0]
        ]);
        $data['expire_time'] = bcmul($data['expire_time'], 86400) + time();
        MerchantModel::edit($data, $id);
        return Json::successful('设置成功');
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $this->assign([
            'title' => '添加商户',
            'action' => Url::build('save'),
            'menus' => json(MerchantMenus::ruleList())->getContent()
        ]);
        return $this->fetch();
    }

    /**
     * @param Request $request
     * @return \think\response\Json
     */
    public function save(Request $request)
    {
        $data = Util::postMore([
            'account',
            ['uid', 0],
            'conf_pwd',
            'pwd',
            'mer_name',
            'real_name',
            'mer_phone',
            'mark',
            'mer_email',
            'mer_address',
            ['checked_menus', [], '', 'rules'],
            ['is_audit', 0]
        ], $request);
        if (!is_array($data['rules']) || !count($data['rules']))
            return Json::fail('请选择最少一个权限');
        $data['rules'] = implode(',', $data['rules']);
        if (!$data['account']) return Json::fail('请输入商户账号');
        if (MerchantAdminModel::where('account', trim($data['account']))->count()) return Json::fail('商户账号已存在,请使用别的商户账号注册');
        if (!$data['pwd']) return Json::fail('请输入商户登陆密码');
        if ($data['pwd'] != $data['conf_pwd']) return Json::fail('两次输入密码不想同');
        if (!$data['mer_name']) return Json::fail('请输入商户名称');
        if (!$data['uid']) return Json::fail('请输入绑定的用户ID');
        if (!User::be($data['uid'], 'uid')) return Json::fail('绑定的用户不存在');
        $data['pwd'] = trim(md5($data['pwd']));
        $data['reg_time'] = time();
        $data['reg_admin_id'] = $this->adminId;
        $admin = array();
        $admin['account'] = trim($data['account']);
        $admin['pwd'] = $data['pwd'];
        unset($data['conf_pwd']);
        unset($data['account']);
        unset($data['pwd']);
        MerchantModel::beginTrans();
        $res = MerchantModel::set($data);
        $res1 = false;
        if ($res) {
            $admin['mer_id'] = $res->id;
            $admin['real_name'] = $data['mer_name'];
            $admin['rules'] = $data['rules'];
            $admin['phone'] = $data['mer_phone'];
            $admin['email'] = $data['mer_email'];
            $admin['add_time'] = time();
            $admin['status'] = 1;
            $admin['level'] = 0;
            $res1 = MerchantAdminModel::set($admin);
        }
        $bool = false;
        if ($res1 && $res) $bool = true;
        MerchantModel::checkTrans($bool);
        if ($bool)
            return Json::successful('添加商户成功!');
        else
            return Json::successful('添加商户失败!');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $role = MerchantModel::get($id);
        $this->assign(['title' => '编辑商户', 'roles' => $role->toJson(), 'menus' => json(MerchantMenus::ruleList())->getContent(), 'action' => Url::build('update', array('id' => $id))]);
        return $this->fetch('edit');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \think\response\Json
     */
    public function update(Request $request, $id)
    {
        $data = Util::postMore([
            'mer_name',
            'real_name',
            'mer_phone',
            'mer_email',
            'mer_address',
            'bank',
            'bank_number',
            'bank_name',
            'bank_address',
            'mark',
            'cityName',
            ['cityid', []],
            ['checked_menus', [], '', 'rules'],
            ['is_audit', 0]
        ], $request);
        if (!$id) return Json::fail('数据错误');
        if (!is_array($data['rules']) || !count($data['rules']))
            return Json::fail('请选择最少一个权限');
        $data['rules'] = implode(',', $data['rules']);
        $merchant = MerchantModel::get($id);
        if (!$merchant) return Json::fail('数据错误');
        if (!$data['mer_name']) return Json::fail('请输入商户名称');
        if (!$data['cityName']) return Json::fail('请选择省市区');
        $data['cityid'] = json_encode($data['cityid']);
        $city = explode(',', $data['cityName']);
        if (isset($city[0])) $data['pro'] = $city[0];
        if (isset($city[1])) $data['city'] = $city[1];
        if (isset($city[2])) $data['area'] = $city[2];
        unset($data['cityName']);
        MerchantModel::beginTrans();
        $res1 = MerchantModel::edit($data, $id);
        $update = array();
        $update['rules'] = $data['rules'];
        $rules = MerchantAdmin::where('level', 0)->where('mer_id', $id)->value('rules');
        if ($update['rules'] == $rules) $res2 = true;
        else $res2 = false !== MerchantAdmin::where('level', 0)->where('mer_id', $id)->update($update);
        $res = false;
        if ($res1 && $res2) $res = true;
        MerchantModel::checkTrans($res);
        if ($res)
            return Json::successful('修改成功!');
        else
            return Json::fail('修改失败!');
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$id) return Json::fail('数据错误');
        $data['is_del'] = 1;
        $userEnter = UserEnter::get(['mer_id' => $id]);
        if (!$userEnter) {
            if (!MerchantModel::edit($data, $id)) return Json::fail(MerchantModel::getErrorInfo('删除失败,请稍候再试!'));
            else return Json::successful('删除成功!');
        } else {
            if (!UserEnter::edit(['mer_id' => '-' . $id], $userEnter['id']) && !MerchantModel::edit($data, $id)) return Json::fail(MerchantModel::getErrorInfo('删除失败,请稍候再试!'));
            else return Json::successful('删除成功!');
        }
    }

    /**
     * 修改状态
     * @param $id
     * @return \think\response\Json
     */
    public function modify($id, $status)
    {
        if (!$id) return Json::fail('数据错误');
        $data['status'] = $status;
        if (!MerchantModel::edit($data, $id)) return Json::fail(MerchantModel::getErrorInfo('修改失败,请稍候再试!'));
        else return Json::successful('修改成功!');
    }

    public function login($id)
    {
        $merchantInfo = \app\admin\model\system\Merchant::where('id', $id)->where('is_del', 0)->find();
        if (!$merchantInfo) return $this->failed('登陆商户不存在!');
        $adminInfo = MerchantAdmin::where('level', 0)->where('mer_id', $merchantInfo->id)->find();
        if (!$adminInfo) return $this->failed('登陆商户不存在!');
        MerchantAdmin::setLoginInfo($adminInfo->toArray());
        MerchantAdmin::setMerchantInfo($merchantInfo->toArray());
        return $this->redirect(Url::build('/merchant/index'));
    }

    public function default_pwd($id)
    {
        if (!$id) return $this->failed('商户不存在!');
        $res = MerchantAdmin::where('mer_id', $id)->where('level', 0)->save(['pwd' => md5('11111111')]);
        if ($res !== false)
            return JsonService::successful('重置密码成功!');
        else
            return JsonService::fail('重置密码失败!');
    }

    public function other_info($id = '')
    {
        if (!$id || !($merInfo = MerchantModel::get($id))) return $this->failed('商户不存在!');
        FormBuilder::text('mer_name', '商户名称', $merInfo['mer_name'])->disabled();
        FormBuilder::text('mer_name', '商户账号', MerchantAdmin::where('mer_id', $id)->where('level', 0)->value('account'))->disabled();
        FormBuilder::text('mer_keyword', '商户搜索关键字', $merInfo['mer_keyword'])->placeholder('多个用逗号隔开');
        FormBuilder::number('sort', '排序', $merInfo['sort'])->min(0);
        FormBuilder::radio('is_best', '是否首页推荐：', [['label' => '开启', 'value' => 1], ['label' => '关闭', 'value' => 0]], $merInfo->getData('is_best'));
        return $this->fetch('public/common_form',
            [
                'title' => '编辑商户资料',
                'action' => Url::build('save_other_info', compact('id')),
                'rules' => FormBuilder::builder()->getContent()
            ]);
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = UploadService::image('file', 'merchant/info');
        $thumbPath = UploadService::thumb($res->dir);
        if ($res->status == 200)
            return Json::successful('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => UploadService::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
    }

    public function save_other_info(Request $request, $id = '')
    {
        if (!$id || !($merInfo = MerchantModel::get($id))) return JsonService::fail('商户不存在!');
        list($merInfo['mer_keyword'], $merInfo['is_best'], $merInfo['sort']) =
            UtilService::postMore([
//                ['mer_avatar',['']],
//                ['mer_banner',['']],
                ['mer_keyword', ''],
//                'postage_min',
//                'pay_postage',
//                'estate',
//                'service_phone',
//                'mer_info',
                ['is_best', 0],
                ['sort', 0]
            ], $request, true);
//        if(count($merInfo['mer_banner']) > 0) $merInfo['mer_banner'] = $merInfo['mer_banner'][0];
//        else $merInfo['mer_banner'] = '';
//        if(count($merInfo['mer_avatar']) > 0) $merInfo['mer_avatar'] = $merInfo['mer_avatar'][0];
//        else $merInfo['mer_avatar'] = '';
        if ($merInfo->save())
            return JsonService::successful('修改成功!');
        else
            return JsonService::fail('修改失败!');
    }

    public function reconciliation($id = 0)
    {
        $where = Util::getMore([
            ['real_name', ''],
            ['is_mer_check', 0],
            ['data', ''],
            ['export', 0]
        ], $this->request);
        $where['mer_id'] = $id;
//        $where['status'] = 5;
//        $where['status'] = 3; //honor 2018.2.11 已支付  已收货  待评价
        $this->assign('where', $where);
        $this->assign('mer_id', $id);
        $this->assign(StoreOrderModel::setReconciliation($where));
        $this->assign('price', StoreOrderModel::setOrderPriceReconciliation($where));
        return $this->fetch();
    }

    public function reconciliation_grant(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->post('id');
            $mer_id = $request->post('mer_id');
            $data = array();
            $data['is_mer_check'] = 1;
            if (!$id) return JsonService::fail('修改失败');
            if (!$mer_id) return JsonService::fail('修改失败');
            if (!$this->adminId) return JsonService::fail('修改失败');
            StoreOrder::beginTrans();
            $res = false;
            $res1 = StoreOrder::where('id', 'IN', $id)->update($data);
            $res2 = MerchantReconciliation::setReconciliation($mer_id, $this->adminId, $id);
            if ($res1 == true && $res2 == true) $res = true;
            StoreOrder::checkTrans($res);
            if ($res) return JsonService::successful('修改成功，等待商户确定');
            else return JsonService::fail('修改失败');
        }
    }

    public function reset_pwd($id)
    {
        if (!$id)
            return JsonService::fail('参数错误失败!');
        if (MerchantAdmin::where('mer_id', $id)->where('level', 0)->update(['pwd' => md5(1234567)]))
            return JsonService::successful('修改成功!');
        else
            return JsonService::fail('修改失败!');
    }

    public function verify($id)
    {
        if (!$id) return JsonService::fail('参数错误失败!');
        $merinfo = MerchantModel::get($id);
        if (!$merinfo) return JsonService::fail('没有查到此商户');
        MerchantModel::beginTrans();
        try {
            $merinfo->status = 1;
            $merinfo->estate = 1;
            $rules = MerchantMenus::where(['is_show' => 1])->column('id');
            $rules = implode(',', $rules);
            $uid = $merinfo->uid;
            $data = [
                'mer_id' => $merinfo->id,
                'account' => $merinfo->mer_phone,
                'pwd' => $merinfo->password,
                'real_name' => $merinfo->real_name,
                'rules' => $rules,
                'level' => 0,
                'phone' => $merinfo->mer_phone,
                'add_time' => time(),
            ];
            MerchantModel::commitTrans();
            if ($merinfo->save()) {
                MerchantAdmin::set($data);
                RoutineTemplate::verifyTemplate($uid, [
                    'keyword1' => '审核通过',
                    'keyword2' => '后台管理员',
                    'keyword3' => SystemConfigService::get('site_phone'),
                    'keyword4' => date('Y-m-d H:i:s', time()),
                    'keyword5' => '您的商户已审核通过,请登录:' . SystemConfigService::get('site_url') . '/merchant商户后台进行填写详细信息,才可展示!',
                    'keyword6' => User::where('uid', $uid)->value('nickname'),
                    'keyword7' => '已通过',
                ]);
                return JsonService::successful('审核成功');
            } else
                return JsonService::fail('保存信息失败');
        } catch (\Exception $e) {
            MerchantModel::rollbackTrans();
            return JsonService::fail($e->getMessage());
        }
    }

    public function verify_no($id)
    {
        if (!$id) return JsonService::fail('参数错误失败!');
        $merinfo = MerchantModel::get($id);
        if (!$merinfo) return JsonService::fail('没有查到此商户');
        $merinfo->status = -2;
        $uid = $merinfo->uid;
        $post = Util::postMore([
            ['remark', ''],
        ]);
        if ($post['remark'] == '') return JsonService::fail('请输入未通过原因!');
        try {
            RoutineTemplate::verifyTemplate($uid, [
                'keyword1' => '审核未通过',
                'keyword2' => '后台管理员',
                'keyword3' => SystemConfigService::get('site_phone'),
                'keyword4' => date('Y-m-d H:i:s', time()),
                'keyword5' => '无',
                'keyword6' => User::where('uid', $uid)->value('nickname'),
                'keyword7' => $post['remark'],
            ]);
            if ($merinfo->save())
                return JsonService::successful('操作完成');
            else
                return JsonService::fail('保存失败');
        } catch (\Exception $e) {
            return JsonService::fail($e->getMessage());
        }
    }

}
