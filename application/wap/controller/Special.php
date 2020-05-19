<?php

namespace app\wap\controller;

use app\admin\model\special\SpecialBarrage;
use app\wap\model\live\LiveStudio;
use app\wap\model\special\Grade;
use app\wap\model\special\Special as SpecialModel;
use app\wap\model\special\SpecialBuy;
use app\wap\model\special\SpecialContent;
use app\wap\model\special\SpecialCourse;
use app\wap\model\special\SpecialRecord;
use app\wap\model\special\SpecialRelation;
use app\wap\model\special\SpecialSource;
use app\wap\model\special\SpecialSubject;
use app\wap\model\special\SpecialTask;
use app\wap\model\store\StoreOrder;
use app\wap\model\store\StorePink;
use app\wap\model\user\User;
use service\CanvasService;
use service\JsonService;
use service\SystemConfigService;
use service\UtilService;
use think\Cookie;
use think\exception\HttpException;
use think\response\Json;
use think\Session;
use think\Url;

class Special extends AuthController
{
    /*
     * 白名单
     * */
    public static function WhiteList()
    {
        return [
            'details',
            'get_pink_info',
            'get_course_list',
            'play',
            'play_num',
            'grade_list',
            'set_barrage_index',
            'get_barrage_list',
            'special_cate',
            'get_grade_cate',
            'get_subject_cate',
            'get_special_list',
            'get_cloumn_task'
        ];
    }

    /**
     * 专题详情
     * @param $id int 专题id
     * @param $pinkId int 拼团id
     * @param $gift_uid int 赠送礼物用户
     * @param $gift_order_id string 礼物订单号
     * @return
     */
    public function details($id = 0, $pinkId = 0, $gift_uid = 0, $gift_order_id = null, $link_pay_uid = 0, $partake = 0, $gift = 0, $link_pay = 0/*, $activity = 0*/)
    {
        if (!$id) $this->failed('缺少参数,无法访问');
        if ($gift_uid && $gift_order_id) {
            if ($gift_uid == $this->uid) $this->failed('您不能领取自己的礼物');
            if (!User::get($gift_uid)) $this->failed('赠送礼物的用户存在');
            $order = StoreOrder::where(['is_del' => 0, 'order_id' => $gift_order_id])->find();
            if (!$order) $this->failed('赠送的礼物订单不存在');
            if ($order->total_num == $order->gift_count) $this->failed('礼物已被领取完');
        }
        $special = SpecialModel::getOneSpecial($this->uid, $id, $pinkId);
        if ($special === false) $this->failed(SpecialModel::getErrorInfo('无法访问'));
        $special_money = SpecialModel::where('id', $id)->field('money, pay_type')->find();
        if (in_array($special_money['money'], [0, 0.00]) || in_array($special_money['pay_type'], [PAY_NO_MONEY, PAY_PASSWORD])) {
            $isPay = 1;
        }else{
            $isPay = (!$this->uid || $this->uid == 0) ? false : SpecialBuy::PaySpecial($id, $this->uid);
        }
        $site_name = SystemConfigService::get('site_name');
        $seo_title = SystemConfigService::get('seo_title');
        $site_logo = SystemConfigService::get('home_logo');
        $isPink = false;
        if (!$isPay && $this->uid && !$pinkId) {
            $pinkId = StorePink::where(['cid' => $id, 'status' => '1', 'uid' => $this->uid])->order('add_time desc')->value('id');
            if ($pinkId) {
                $isPink = true;
            } else {
                $pinkId = 0;
            }
        }

        $liveInfo = [];
        if (isset($special['special'])) {
            $specialinfo = $special['special'];
            $specialinfo = is_string($specialinfo) ? json_decode($specialinfo, true) : $specialinfo;
            if ((float)$specialinfo['money'] < 0) {
                $isPink = true;
            }
            if ($specialinfo['type'] == SPECIAL_LIVE) {
                $liveInfo = LiveStudio::where('special_id', $specialinfo['id'])->find();
                if (!$liveInfo) return $this->failed('直播间尚未查到！');
                if ($liveInfo->is_del) return $this->failed('直播间已经删除！');
            }
        }

        if ($this->uid) SpecialRecord::record($id, $this->uid);
        $user_level = !$this->uid ? 0 : User::getUserInfo($this->uid);
        $this->assign($special);
        $this->assign('pinkId', $pinkId);
        $this->assign('is_member', isset($user_level['level']) ? $user_level['level'] : 0);
        //$this->assign('activity', $activity);
        $this->assign('isPink', $isPink);
        $this->assign('isPay', $isPay);
        $this->assign('liveInfo', json_encode($liveInfo));
        $this->assign('confing', compact('site_name', 'seo_title', 'site_logo'));
        $this->assign('orderId', $gift_order_id);
        $this->assign('partake', (int)$partake);
        $this->assign('link_pay', (int)$link_pay);
        $this->assign('gift', (int)$gift);
        $this->assign('link_pay_uid', $link_pay_uid);
        $this->assign('BarrageShowTime', SystemConfigService::get('barrage_show_time'));
        $this->assign('barrage_index', Cookie::get('barrage_index'));
        return $this->fetch();
    }



    /**
     * 礼物领取
     *
     * */
    public function receive_gift($orderId = '')
    {
        if (!$orderId) return JsonService::fail('缺少参数');
        if (StoreOrder::createReceiveGift($orderId, $this->uid) == false)
            return JsonService::fail(StoreOrder::getErrorInfo('领取失败'));
        else
            return JsonService::successful('领取成功');
    }


    /**
     * 专题收藏
     * @param $id int 专题id
     * @return json
     */
    public function collect($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        if (SpecialRelation::SetCollect($this->uid, $id))
            return JsonService::successful('');
        else
            return JsonService::fail();
    }

    /**
     * 获取某个专题的任务视频列表
     * @return json
     * */
    public function get_course_list()
    {
        list($page, $limit, $special_id) = UtilService::getMore([
            ['page', 1],
            ['limit', 10],
            ['special_id', 0],
        ], null, true);
        $task_list = SpecialCourse::getSpecialSourceList($special_id, $limit, $page);
        if(!$task_list['list'])  return JsonService::successful([]);
        foreach ($task_list['list'] as $k => $v) {
            $task_list['list'][$k]['type_name'] = SPECIAL_TYPE[$v['type']];
        }
        return JsonService::successful($task_list);
    }

    /**
     * 获取专栏套餐
     */
    public function get_cloumn_task()
    {
        list($page, $limit, $special_id, $source_id) = UtilService::getMore([
            ['page', 1],
            ['limit', 10],
            ['special_id', 0],
            ['source_id', 0],
        ], null, true);
        $task_list = SpecialCourse::get_cloumn_task($special_id, $source_id, $limit, $page);
        if(!$task_list['list'])  return JsonService::successful([]);
        foreach ($task_list['list'] as $k => $v) {
            $task_list['list'][$k]['type_name'] = SPECIAL_TYPE[$v['type']];
        }
        return JsonService::successful($task_list);
    }

    /**
     * 播放数量增加
     * @param int $task_id 任务id
     * @return json
     * */
    public function play_num($task_id = 0)
    {
        $special_id = $this->request->param('special_id',0);
        if ($task_id == 0 || $special_id == 0) return JsonService::fail('缺少参数');
        try{
            $add_task_play_count = SpecialTask::bcInc($task_id, 'play_count', 1);
            if ($add_task_play_count) {
                $special_source = SpecialSource::getSpecialSource((int)$special_id, [$task_id]);
                if ($special_source) {
                    SpecialSource::where(['special_id' => $special_id, 'source_id' => $task_id])->setInc('play_count',1);
                }
                return JsonService::successful();
            }else {
                return JsonService::fail();
            }
        }catch (\Exception $e) {
            return JsonService::fail();
        }

    }

    /**
     * 播放任务
     * @param int $task_id 任务id
     * @return string
     * */
    public function play($task_id = 0)
    {
        if (!$task_id) $this->failed('无法访问');
        Session::set('video_token_' . $task_id, md5(time() . $task_id));
        $tash = SpecialTask::get($task_id);
        if (!$tash) $this->failed('您查看的资源不存在');
        if ($tash->is_show == 0) $this->failed('您查看的资源已下架');
        $this->assign('link', Trust($tash->link));
        $this->assign('task_id', $task_id);
        return $this->fetch();
    }

    public function go_video($task_id = 0)
    {
        if (Cookie::has('video_token_count_' . $task_id)) {
            Cookie::set('video_token_count_' . $task_id, Cookie::get('video_token_count_' . $task_id) + 1);
        } else {
            Cookie::set('video_token_count_' . $task_id, 1);
        }
        if (Session::has('video_token_' . $task_id)) {
            $tash = SpecialTask::get($task_id);
            if (Cookie::get('video_token_count_' . $task_id) >= 2) {
                Session::delete('video_token_' . $task_id);
            }
            exit(file_get_contents($tash->link));
        } else {
            throw new HttpException(404, '您查看的链接不存在');
        }
    }

    /**
     * 拼团支付完成后页面
     * @param null $orderId
     * @return mixed|void
     */
    public function pink($orderId = null)
    {
        if (is_null($orderId)) $this->failed('缺少参数');
        $info = StoreOrder::getOrderSpecialInfo($orderId, $this->uid);
        if ($info === false) return $this->failed(StoreOrder::getErrorInfo());
        $site_url = SystemConfigService::get('site_url') . Url::build('special/details') . '?id=' . $info['special']['id'] . '&pinkId=' . $info['pinkT']['id'] . '&partake=1&spread_uid=' . $this->uid . '#partake';
        $this->assign('special_id', $info['special_id']);
        $this->assign('site_url', $site_url);
        $this->assign('info', json_encode($info));
        return $this->fetch();
    }

    /**
     * 创建支付订单
     * @param int $special_id 专题id
     * @param int $pay_type 购买类型 1=礼物,2=普通购买,3=开团或者拼团
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create_order()
    {
        list($special_id, $pay_type_num, $payType, $pinkId, $total_num, $link_pay_uid) = UtilService::getMore([
            ['special_id', 0],
            ['pay_type_num', -1],
            ['payType', 'weixin'],
            ['pinkId', 0],
            ['total_num', 1],
            ['link_pay_uid', 0]
        ], $this->request, true);
        if (!$special_id) return JsonService::fail('缺少购买参数');
        if ($pay_type_num == -1) return JsonService::fail('选择购买方式');
        if ($pinkId) {
            $orderId = StoreOrder::getStoreIdPink($pinkId);
            if (StorePink::getIsPinkUid($pinkId)) return JsonService::status('ORDER_EXIST', '订单生成失败，你已经在该团内不能再参加了', ['orderId' => $orderId]);
            if (StoreOrder::getIsOrderPink($pinkId)) return JsonService::status('ORDER_EXIST', '订单生成失败，你已经参加该团了，请先支付订单', ['orderId' => $orderId]);
            if (StorePink::getPinkStatusIng($pinkId)) return JsonService::status('ORDER_EXIST', '拼团已完成或者已过期无法参团', ['orderId' => $orderId]);
            if (StorePink::be(['uid' => $this->uid, 'type' => 1, 'cid' => $special_id, 'status' => 1])) return JsonService::status('ORDER_EXIST', '您已参见本专题的拼团,请结束后再进行参团');
            if (SpecialBuy::be(['uid' => $this->uid, 'special_id' => $special_id, 'is_del' => 0])) return JsonService::status('ORDER_EXIST', '您已购买此专题,不能在进行参团!');
            //处理拼团完成
            try {
                if ($pink = StorePink::get($pinkId)) {
                    list($pinkAll, $pinkT, $count, $idAll, $uidAll) = StorePink::getPinkMemberAndPinkK($pink);
                    if ($pinkT['status'] == 1) {
                        if (!$count || $count < 0) {
                            StorePink::PinkComplete($uidAll, $idAll, $pinkT['uid'], $pinkT);
                            return JsonService::status('ORDER_EXIST', '当前拼团已完成，无法参团');
                        } else
                            StorePink::PinkFail($pinkT['uid'], $idAll, $pinkAll, $pinkT, $count, 0, $uidAll);
                    } else if ($pinkT['status'] == 2) {
                        return JsonService::status('ORDER_EXIST', '当前拼团已完成，无法参团');
                    } else if ($pinkT['status'] == 3) {
                        return JsonService::status('ORDER_EXIST', '拼团失败，无法参团');
                    }
                }
            } catch (\Exception $e) {

            }
        }
        $special = SpecialModel::PreWhere()->find($special_id);
        if (!$special) return JsonService::status('ORDER_ERROR', '购买的专题不存在');
        $order = StoreOrder::createSpecialOrder($special, $pinkId, $pay_type_num, $this->uid, $payType, $link_pay_uid, $total_num);
        $orderId = $order['order_id'];
        $info = compact('orderId');
        if ($orderId) {
            $orderInfo = StoreOrder::where('order_id', $orderId)->find();
            if (!$orderInfo || !isset($orderInfo['paid'])) return JsonService::status('pay_error', '支付订单不存在!');
            if ($orderInfo['paid']) return JsonService::status('pay_error', '支付已支付!');
            if (bcsub((float)$orderInfo['pay_price'], 0, 2) <= 0) {
                if (StoreOrder::jsPayPrice($orderId, $this->userInfo['uid']))
                    return JsonService::status('success', '微信支付成功', $info);
                else
                    return JsonService::status('pay_error', StoreOrder::getErrorInfo());
            }
            switch ($payType) {
                case 'weixin':
                    try {
                        $jsConfig = StoreOrder::jsSpecialPay($orderId);
                    } catch (\Exception $e) {
                        return JsonService::status('pay_error', $e->getMessage(), $info);
                    }
                    $info['jsConfig'] = $jsConfig;
                    return JsonService::status('wechat_pay', '订单创建成功', $info);
                    break;
                case 'yue':
                    if (StoreOrder::yuePay($orderId, $this->userInfo['uid']))
                        return JsonService::status('success', '余额支付成功', $info);
                    else
                        return JsonService::status('pay_error', StoreOrder::getErrorInfo());
                    break;
                case 'zhifubao':
                    $info['pay_price'] = $orderInfo['pay_price'];
                    $info['orderName'] = '专题购买';
                    return JsonService::status('zhifubao_pay', ['info' => base64_encode(json_encode($info))]);
                    break;
            }
        } else {
            return JsonService::fail(StoreOrder::getErrorInfo('订单生成失败!'));
        }
    }

    /**
     * 购买完成后送礼物页面
     * @param string $orderId 订单id
     * @return strign
     * */
    public function gift_special($orderId = null)
    {
        if (is_null($orderId)) $this->failed('缺少订单号,无法进行赠送');
        $special = StoreOrder::getOrderIdToSpecial($orderId);
        if ($special === false) $this->failed(StoreOrder::getErrorInfo());
        $this->assign('special', $special);
        $this->assign('special_gift_banner', SystemConfigService::get('special_gift_banner'));
        $this->assign('site_url', SystemConfigService::get('site_url') . Url::build('special/details') . '?id=' . $special['id'] . '&gift_uid=' . $this->uid . '&gift_order_id=' . $orderId . '&gift=1&spread_uid=' . $this->uid);
        $this->assign('orderId', $orderId);
        return $this->fetch();
    }

    /**
     * 查看领取记录
     * @param $orderId string 订单id
     * @return html
     * */
    public function gift_receive($orderId = null)
    {
        if (is_null($orderId)) $this->failed('缺少订单号,无法查看领取记录');
        $special = StoreOrder::getOrderIdGiftReceive($orderId);
        if ($special === false) $this->failed(StoreOrder::getErrorInfo());
        $this->assign($special);
        $this->assign('special_gift_banner', SystemConfigService::get('special_gift_banner'));
        return $this->fetch();
    }

    /**
     * 购买失败删除订单
     * @param string $orderId 订单id
     * @return json
     * */
    public function del_order($orderId = '')
    {
        if (StoreOrder::where('order_id', $orderId)->update(['is_del' => 1]))
            return JsonService::successful();
        else
            return JsonService::fail();
    }

    public function grade_list($type = 0)
    {
        $this->assign(compact('type'));
        return $this->fetch();
    }

    /**
     * 获取我购买的课程
     * @param int $type 课程类型
     * @param int $page 分页
     * @param int $limit 一页显示多少条
     * @return json
     * */
    public function get_grade_list($type = 0, $page = 1, $limit = 10, $search = '')
    {
        return JsonService::successful(SpecialModel::getGradeList($type, (int)$page, (int)$limit, $this->uid, $search));
    }

    /**
     * 拼团成功朋友圈海报展示
     * @param $special_id int 专题id
     * @return html
     * */
    public function poster_show($special_id = 0, $pinkId = 0, $is_help = 0)
    {
        if (!$special_id || !$pinkId) $this->failed('您查看的朋友去圈海报不存在');
        $special = SpecialModel::getSpecialInfo($special_id);
        if ($special === false) $this->failed(SpecialModel::getErrorInfo());
        if (!$special['poster_image']) $this->failed('您查看的海报不存在');
        $site_url = SystemConfigService::get('site_url') . Url::build('special/details') . '?id=' . $special['id'] . '&pinkId=' . $pinkId . '&partake=1' . ($is_help ? '&spread_uid=' . $this->uid : '');
        try {
            $filename = CanvasService::startPosterSpeclialIng($special_id, $special['poster_image'], $site_url);
        } catch (\Exception $e) {
            return $this->failed($e->getMessage());
        }
        $this->assign('filename', $filename);
        $this->assign('special', $special);
        $this->assign('is_help', $is_help);
        $this->assign('site_url', $site_url);
        return $this->fetch();
    }


    /**
     * 获取专题弹幕
     * @param int $special_id 专题id
     * @return json
     * */
    public function get_barrage_list($special_id = 0)
    {
        if (SystemConfigService::get('open_barrage')) {
            $barrage = SpecialBarrage::where('is_show', 1)->order('sort desc,id desc')->field(['nickname', 'avatar', 'action'])->select();
            $barrage = count($barrage) ? $barrage->toArray() : [];
            foreach ($barrage as &$item) {
                $item['status_name'] = $item['action'] == 1 ? '1秒前发起了拼团' : '1秒前成功参团';
                unset($item['action']);
            }
            $pinkList = StoreOrder::where(['o.cart_id' => $special_id, 'p.is_refund' => 0, 'o.refund_status' => 0, 'o.paid' => 1, 'p.is_false' => 0])
                ->join("__STORE_PINK__ p", 'p.order_id=o.order_id')
                ->join('__USER__ u', 'u.uid=o.uid')
                ->field(['u.nickname', 'u.avatar', 'p.status', 'p.k_id'])
                ->group('o.order_id')
                ->order('o.add_time desc')
                ->alias('o')
                ->select();
            $pinkList = count($pinkList) ? $pinkList->toArray() : [];
            foreach ($pinkList as &$item) {
                if ($item['status'] == 2 && $item['k_id'] == 0) {
                    $item['status_name'] = '1秒前拼团成功';
                } else if ($item['status'] == 1 && $item['k_id'] == 0)
                    $item['status_name'] = '1秒前发起了拼团';
                else if ($item['status'] == 2 && $item['k_id'] != 0)
                    $item['status_name'] = '1秒前拼团成功';
                else if ($item['status'] == 1 && $item['k_id'] != 0)
                    $item['status_name'] = '1秒前发起了拼团';
                else if ($item['status'] == 3)
                    $item['status_name'] = '1秒前参团成功';
                unset($item['status'], $item['k_id']);
            }
            $barrageList = array_merge($pinkList, $barrage);
            shuffle($barrageList);
        } else $barrageList = [];
        return JsonService::successful($barrageList);
    }

    /**
     * 获取滚动index
     * @param int $index
     */
    public function set_barrage_index($index = 0)
    {
        return JsonService::successful(Cookie::set('barrage_index', $index));
    }

    /**
     * 专题分类
     * @return mixed
     */
    public function special_cate($cate_id = 0)
    {
        $this->assign([
            'homeLogo' => SystemConfigService::get('home_logo'),
            'cate_id' => (int)$cate_id
        ]);
        return $this->fetch();
    }

    /**
     * 获取一级分类
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_grade_cate()
    {
        return JsonService::successful(Grade::order('sort desc,id desc')->select());
    }

    /**
     * 获取二级分类
     * @param int $grade_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_subject_cate($grade_id = 0)
    {
        if (!$grade_id) {
            return JsonService::fail('缺少一级分类id');
        }
        return JsonService::successful(SpecialSubject::where(['is_show' => 1, 'grade_id' => $grade_id])->order('sort desc,id desc')->select());
    }

    /**
     * 获取专题
     * @param int $subject_id
     * @param string $search
     * @param int $page
     * @param int $limit
     */
    public function get_special_list($subject_id = 0, $search = '', $page = 1, $limit = 10, $type = 0)
    {
        if (!$subject_id && $type == 0) {
            return JsonService::fail('缺少二级分类id');
        }
        $uid = $this->uid;
        return JsonService::successful(SpecialModel::getSpecialList(compact('subject_id', 'search', 'page', 'limit', 'type', 'uid')));
    }

    /**
     * 学习记录
     * @return mixed
     */
    public function record()
    {
        $this->assign([
            'homeLogo' => SystemConfigService::get('home_logo'),
        ]);
        return $this->fetch();
    }

    /**
     * 是否可以播放
     * @param int $task_id 任务id
     * @return string
     * */
    public function get_task_link($task_id = 0)
    {
        $special_id = $this->request->param('special_id',0);
        if (!$special_id) return JsonService::fail('无法访问');
        if (!$task_id) return JsonService::fail('无法访问');
        $special_source = SpecialSource::getSpecialSource($special_id,[$task_id]);
        if ($tash = $special_source ? $special_source->toArray() : [])
        //$tash = SpecialTask::get($task_id);
        if (!$tash) return JsonService::fail('您查看的视频已经下架');
       // $task = SpecialTask::getSpecialTaskOne($task_id);
        //if ($tash->is_show == 0) return JsonService::fail('您查看的视频已下架');
       // if (!$task->link && $task->live_id != SPECIAL_LIVE) return JsonService::fail('该课程暂未更新，具体更新时间请咨询客服~');
        return JsonService::successful();
    }

    /**
     * 课程详情
     * @param $id
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function task_info($id = 0, $specialId = 0)
    {
        $play_content = $this->request->param('content',1);//1:显示详情，2显示内容
        if (!$id) {
            return $this->failed('缺少课程id,无法查看');
        }
        //$taskInfo = SpecialTask::defaultWhere()->where('special_id', $specialId)->where('id', $id)->find();
        $taskInfo = SpecialTask::defaultWhere()->where('id', $id)->find();
        $special = SpecialModel::PreWhere()->where('id', $specialId)->find();

        if (!$special) {
            return $this->failed('您查看得专题不存在');
        }
        if (!$taskInfo) {
            return $this->failed('课程信息不存在无法观看');
        }
        if ($taskInfo['is_show'] == 0) {
            return $this->failed('该课程已经下架');
        }
        $isPay = SpecialBuy::PaySpecial($specialId, $this->uid);
        if ($isPay === false && $special->money <= 0 && !$special->is_pink) {
            $isPay = true;
        }

        $special_content = SpecialContent::where('special_id',$specialId)->value("content");
        if ($play_content == 2) {
            switch($special['type']){
                case SPECIAL_IMAGE_TEXT:
                    $content = htmlspecialchars_decode($taskInfo->content ? $taskInfo->content : "");
                    break;
                case SPECIAL_VIDEO:
                case SPECIAL_AUDIO:
                    $content = htmlspecialchars_decode($taskInfo->detail ? $taskInfo->detail : $special_content);
                    break;
            }
        }else{
            $content = htmlspecialchars_decode($taskInfo->detail ? $taskInfo->detail : $special_content);
        }
        $user_level = !$this->uid ? 0 : User::getUserInfo($this->uid);
        $taskInfo->content =  $content;
        $this->assign('taskInfo', json_encode($taskInfo->toArray()));
        $this->assign('is_member', isset($user_level['level']) ? $user_level['level'] : 0);
        $this->assign('specialId', (int)$specialId);
        $this->assign('specialInfo', json_encode($special->toArray()));
        $this->assign('isPay', (int)$isPay);
        return $this->fetch();
    }


}