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


namespace app\admin\model\system;


use traits\ModelTrait;
use basic\ModelBasic;
use behavior\system\SystemBehavior;
use service\HookService;
use think\Session;
/**
 * Class SystemAdmin
 * @package app\admin\model\system
 */
class SystemAdmin extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    public static function setAddTimeAttr($value)
    {
        return time();
    }

    public static function setRolesAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }


    /**
     * 用户登陆
     * @param string $account 账号
     * @param string $pwd 密码
     * @param string $verify 验证码
     * @return bool 登陆成功失败
     */
    public static function login($account,$pwd)
    {
        $adminInfo = self::get(compact('account'));
        if(!$adminInfo) return self::setErrorInfo('登陆的账号不存在!');
        if($adminInfo['pwd'] != md5($pwd)) return self::setErrorInfo('账号或密码错误，请重新输入');
        if(!$adminInfo['status']) return self::setErrorInfo('该账号已被关闭!');
        self::setLoginInfo($adminInfo->toarray());
        HookService::afterListen('system_admin_login',$adminInfo,null,false,SystemBehavior::class);
        return true;
    }

    /**
     *  保存当前登陆用户信息
     */
    public static function setLoginInfo($adminInfo)
    {
        //补充角色信息
        $roleSign = SystemRole::where(['id' => $adminInfo['roles']])->value('sign');
        $adminInfo['role_sign'] = $roleSign ? $roleSign : "";
        Session::set('adminId',$adminInfo['id']);
        Session::set('adminInfo',$adminInfo);
    }

    /**
     * 清空当前登陆用户信息
     */
    public static function clearLoginInfo()
    {
        Session::delete('adminInfo');
        Session::delete('adminId');
        Session::clear();
    }

    /**
     * 检查用户登陆状态
     * @return bool
     */
    public static function hasActiveAdmin()
    {
        return Session::has('adminId') && Session::has('adminInfo');
    }

    /**
     * 获得登陆用户信息
     * @return mixed
     */
    public static function activeAdminInfoOrFail()
    {
        $adminInfo = Session::get('adminInfo');
        if(!$adminInfo)  exception('请登陆');
        if(!$adminInfo['status']) exception('该账号已被关闭!');
        return $adminInfo;
    }

    /**
     * 获得登陆用户Id 如果没有直接抛出错误
     * @return mixed
     */
    public static function activeAdminIdOrFail()
    {
        $adminId = Session::get('adminId');
        if(!$adminId) exception('访问用户为登陆登陆!');
        return $adminId;
    }

    /**
     * @return array
     */
    public static function activeAdminAuthOrFail()
    {
        $adminInfo = self::activeAdminInfoOrFail();
        return $adminInfo->level === 0 ? SystemRole::getAllAuth() : SystemRole::rolesByAuth($adminInfo->roles);
    }

    /**
     * 获得有效管理员信息
     * @param $id
     * @return static
     */
    public static function getValidAdminInfoOrFail($id)
    {
        $adminInfo = self::get($id);
        if(!$adminInfo) exception('用户不能存在!');
        if(!$adminInfo['status']) exception('该账号已被关闭!');
        return $adminInfo;
    }

    /**
     * @param $field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getOrdAdmin($field = 'real_name,id',$level = 0){
        return self::where('level','>=',$level)->field($field)->select();
    }

    public static function getTopAdmin($field = 'real_name,id')
    {
        return self::where('level',0)->field($field)->select();
    }

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where){
        $model = new self;
        if($where['name'] != ''){
            $model = $model->where('account','LIKE',"%$where[name]%");
            $model = $model->where('real_name','LIKE',"%$where[name]%");
        }
        if($where['roles'] != '')
            $model = $model->where("CONCAT(',',roles,',')  LIKE '%,$where[roles],%'");
        $model = $model->where('level','=',$where['level'])->where('is_del',0);
        return self::page($model,function($admin,$key){
            $admin->roles = SystemRole::where('id','IN',$admin->roles)->column('role_name');
        },$where);
    }

    /**根据身份获取后台账号列表
     * @param $sign 角色身份标识
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public static function getRoleAdmin($sign = false) {
        $where['a.status'] = 1;
        if ((is_array($sign) && !$sign) || !$sign){
            $role_sign = SystemRole::where('status',1)
                ->field('sign')
                ->select()
                ->toArray();
            $sign = array_column($role_sign, 'sign');
        }
        if (!is_array($sign) && $sign) {
            $where['r.sign'] = $sign;
        }
        return self::where($where)
            ->whereIn('sign',$sign)
             ->alias('a')
            ->join('__SYSTEM_ROLE__ r', 'a.roles = r.id', 'LEFT')
            ->field('a.id as admin_id, a.account as admin_name, a.roles as role_id, a.level as admin_level, a.status as admin_status')
            ->select()
            ->toArray();
    }

    /**
     * 检测用户权限身份
     */
    public static function testUserLevel($user){
        $role=SystemRole::where('sign','verification')->where('status',1)->find();
        if(!$role) return false;
        $level=self::where('roles',$role['id'])->where('phone',$user['phone'])->find();
        if($level) return true;
        else return false;
    }
}