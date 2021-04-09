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

use app\admin\model\special\RecommendBanner;
use app\admin\model\wechat\WechatQrcode;
use app\wap\model\activity\EventRegistration;
use app\wap\model\live\LiveStudio;
use app\wap\model\user\SmsCode;
use app\wap\model\recommend\Recommend;
use app\wap\model\special\SpecialSubject;
use app\wap\model\user\PhoneUser;
use app\wap\model\user\User;
use app\wap\model\wap\Search;
use service\GroupDataService;
use service\JsonService;
use service\QrcodeService;
use service\SystemConfigService;
use service\UploadService as Upload;
use service\UtilService;
use think\Config;
use think\Db;
use think\Session;
use think\Cookie;


class Index extends AuthController
{

    /*
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'agree',
            'index',
            'get_recommend',
            'get_content_recommend',
            'get_host_search',
            'go_search',
            'login',
            'user_login',
            'search',
            'get_unifiend_list',
            'get_recommend_info',
            'more_list',
            'get_more_list',
            'unified_list',
            'qcode_login',
            'get_search_history',
        ];
    }

    /**
     * @return mixed
     */
    public function agree()
    {
        $this->assign('title', SystemConfigService::get('site_name') . '支付用户协议');
        $this->assign('content', get_config_content('user_agreement'));
        return $this->fetch();
    }

    /**
     * 主页
     * @return mixed
     */
    public function index()
    {
        $site_name = SystemConfigService::get('site_name');
        $seo_title = SystemConfigService::get('seo_title');
        $site_logo = SystemConfigService::get('home_logo');
        $this->assign('confing', compact('site_name', 'seo_title', 'site_logo'));
        $live_one_id=Session::get('live_one_id');
        $this->assign([
            'banner' => json_encode(GroupDataService::getData('store_home_banner') ?: []),
            'title' => SystemConfigService::get('site_name'),
            'activity' => json_encode(GroupDataService::getData('home_activity')),
            'liveList' => json_encode(LiveStudio::getLiveList(10)),
            'liveOne' => json_encode(LiveStudio::getLiveOne($live_one_id)),
        ]);
        return $this->fetch();
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload($name = 'file', $link = 'master')
    {
        $res = Upload::image($name, $link);
        $thumbPath = Upload::thumb($res->dir);
        if ($res->status == 200)
            return JsonService::successful('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => Upload::pathToUrl($thumbPath)]);
        else
            return JsonService::fail($res->error);
    }

    /**
     *
     * @param int $qcode_id
     * @throws \think\exception\DbException
     */
    public function qcode_login($qcode_id = 0)
    {
        $qcodeInfo = WechatQrcode::get($qcode_id);
        if ($qcodeInfo) {
            if ($qcodeInfo->scan_id) {
                Session::set('loginUid', $qcodeInfo->scan_id, 'wap');
                Cookie::set('__login_phone', 1);
                Session::set('__login_phone_num' . $qcodeInfo->scan_id, User::where('uid', $qcodeInfo->scan_id)->value('phone'));
                return JsonService::successful();
            }
        }
        return JsonService::fail('');
    }

    /**
     * 手机号码登录
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login()
    {
        list($phone, $code) = UtilService::postMore([
            ['phone', ''], ['code', '']
        ], $this->request, true);
        if (!$phone) return JsonService::fail('请输入手机号码');
        $userphone = User::where(['uid' => $this->uid])->value('phone');
        if ($userphone) {
            if ($userphone != $phone) return JsonService::fail('当前手机号码尚未绑定此用户');
        }
        if (!$code) return JsonService::fail('请输入验证码');
        if (!SmsCode::CheckCode($phone, $code)) return JsonService::fail('验证码验证失败');
        SmsCode::setCodeInvalid($phone, $code);
        if (!$userphone) {
            //检查手机号码不存在时
            $phoneUser = PhoneUser::where(['phone' => $phone])->find();
            //H5页面有注册过
            if ($phoneUser) {
                //检测当前用户是否是H5用户
                if (User::where('uid', $phoneUser['uid'])->value('is_h5user')) {
                    $res = User::setUserRelationInfos($phone, $phoneUser['uid'], $this->uid);
                    if ($res === false) return JsonService::fail(User::getErrorInfo());
                }
            }
            if (!isset($res)) User::update(['phone' => $phone], ['uid' => $this->uid]);
        }
        Cookie::set('__login_phone', 1);
        Session::set('__login_phone_num' . $this->uid, $phone, 'wap');
        return JsonService::successful('登录成功');
    }

    /*
     * 获取手机号码登录状态
     * */
    public function user_login()
    {
        if ($this->phone)
            return JsonService::successful('');
        else
            return JsonService::fail('请先登录,在进行购买!');
    }

    public function login_user()
    {
        if ($this->uid)
            return JsonService::successful('');
        else
            return JsonService::fail('请登录!');
    }

    /**
     * 获取主页导航图标
     */
    public function get_recommend()
    {
        return JsonService::successful(Recommend::getRecommend());
    }

    /**
     * 获取主页推荐列表
     * @param int $page
     * @param int $limit
     */
    public function get_content_recommend($page = 1, $limit = 10)
    {
        return JsonService::successful(Recommend::getContentRecommend((int)$page, (int)$limit, $this->uid));
    }

    /**
     * 获取热搜词
     */
    public function get_host_search()
    {
        return JsonService::successful(Search::getHostSearch());
    }
    /**
     * 查找搜索历史内容
     * */
    public function get_search_history($search = '', $limit = 0)
    {
        $uid=$this->userInfo['uid'] ? $this->userInfo['uid'] : 0;
       if($uid) {
           $list=Search::userSearchHistory($uid);
       }else{
           $list=[];
       }
        return JsonService::successful($list);
    }
    /**
     * 查找搜索内容
     * */
    public function go_search($search = '', $limit = 0)
    {
        $uid=$this->userInfo['uid'] ? $this->userInfo['uid'] : 0;
        return JsonService::successful(Search::getSearchContent($search, $limit,$uid));
    }


    /**
     * 搜索页面
     * */
    public function search()
    {
        return $this->fetch();
    }

    /**
     * 搜索页面查看更多
     * */
    public function more_list($type = 0, $search = '')
    {
        if ($search == '') $this->failed('没有查找相关数据,点我返回上一页');
        $this->assign(compact('type', 'search'));
        return $this->fetch();
    }

    /**
     * 分页获取搜索更多内容
     * */
    public function get_more_list()
    {
        $where = UtilService::getMore([
            ['type', 0],
            ['search', ''],
            ['page', 1],
            ['limit', 10],
        ]);
        return JsonService::successful(Search::getMoerList($where));
    }

    public function get_recommend_info($recommend_id = 0)
    {
        return JsonService::successful(Recommend::get($recommend_id));
    }

    /**
     * 主页标签详情
     * @param int $type
     * @param string $title
     * @param int $recommend_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function unified_list($type = 0, $title = '', $recommend_id = 0)
    {
        if (!$recommend_id) $this->failed('您查看的页面走丢了');
        $recommend = Recommend::get($recommend_id);
        if (!$recommend) $this->failed('您查看的栏目不存在');
        if ($recommend->is_show == 0) $this->failed('您查看的栏目不存在');
        $banner = RecommendBanner::valiWhere()->where('recommend_id', $recommend_id)->select();
        $Recommendlist = SpecialSubject::where('is_show', 1)->where('grade_id', $recommend['grade_id'])->field(['name as title', 'id'])->order('sort desc')->select();
        if ($recommend->typesetting == 4) {
            $recommend->typesetting = 3;
        }
        $this->assign([
            'type' => (int)$type,
            'title' => $title,
            'grade_id' => (int)$recommend->grade_id,
            'image' => $recommend->image,
            'recommend_id' => (int)$recommend_id,
            'typesetting' => (int)$recommend->typesetting,
            'banner' => json_encode($banner),
            'Recommendlist' => json_encode($Recommendlist),
        ]);
        return $this->fetch();
    }

    /*
     * 标签详情列表获取
     * */
    public function get_unifiend_list()
    {
        $where = UtilService::getMore([
            ['page', 1],
            ['limit', 10],
            ['recommend_id', 0],
            ['type', 0],
            ['typesetting', 0],
            ['subject_id', 0],
        ]);
        return JsonService::successful(Search::getUnifiendList($where));
    }

    public function date_empty()
    {
        \think\Session::clear();
        \think\Cookie::clear();
    }
}
