<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
//

namespace app\wap\model\store;

use app\wap\model\special\Special;
use app\wap\model\special\SpecialBuy;
use app\wap\model\user\User;
use app\wap\model\user\WechatUser;
use basic\ModelBasic;
use service\WechatTemplateService;
use think\Url;
use traits\ModelTrait;

/**
 * 拼团Model
 * Class StorePink
 * @package app\wap\model\store
 */
class StorePink extends ModelBasic
{
    use ModelTrait;

    public static function getPinkIngCount($k_id)
    {
        return self::where('a')->where('k_id', $k_id)->count() + 1;
    }

    public static function getPinkStatusIng($pinkId)
    {
        return self::where('id', $pinkId)->value('status') == 1 ? false : true;
    }

    /**
     * 助力拼团
     * @param int $painId 拼团id
     * @return boolen
     * */
    public static function helpePink($pinId, $nickname, $avatar)
    {
        $pink = self::where('id', $pinId)->find();
        if (!$pink) return self::setErrorInfo('拼团信息暂未查到');
        if ($pink->status != 1) return self::setErrorInfo('拼团暂时无法助力');
        self::beginTrans();
        try {
            //获取参团人和团长和拼团总人数
            list($pinkAll, $pinkT, $count) = self::getPinkMemberAndPinkK($pink);
            //拼团是否完成
            if (!$count || $count < 0) return self::setErrorInfo('拼团已完成无法助力');
            //拼团超时
            if ($pinkT['stop_time'] < time()) return self::setErrorInfo('拼团超时无法助力');
            $data = [
                'uid' => 0,
                'type' => 1,
                'order_id' => '',
                'order_id_key' => 0,
                'total_num' => 1,
                'total_price' => $pinkT['total_price'],
                'cid' => $pinkT['cid'],
                'pid' => $pinkT['pid'],
                'price' => $pinkT['price'],
                'add_time' => time(),
                'stop_time' => $pinkT['stop_time'],
                'k_id' => $pinkT['id'],
                'is_tpl' => 0,
                'is_refund' => 0,
                'is_false' => 1,
                'status' => 1,
            ];
            $pink_false = self::set($data);
            if (!$pink_false) return self::setErrorInfo('写入助力拼团失败', true);
            $res = self::getDb('store_pink_false')->insert(['pink_id' => $pink_false['id'], 'nickname' => $nickname, 'avatar' => $avatar, 'add_time' => time()]);
            if (!$res) return self::setErrorInfo('写入助力拼团虚拟用户失败', true);
            //助力拼团加1人判断是否拼团完成;
            if (!($count - 1)) {
                $idAll = [];
                $uidAll = [];
                foreach ($pinkAll as $k => $v) {
                    $idAll[$k] = $v['id'];
                    if ($v['uid']) $uidAll[$k] = $v['uid'];
                }
                $idAll[] = $pinkT['id'];
                $uidAll[] = $pinkT['uid'];
                if (self::setPinkStatus($idAll)) {
                    $orderAll = self::where('id', 'in', $idAll)->column('order_id');
                    if (count($orderAll)) StoreOrder::PinkRake($orderAll);//拼团完成反佣金
                    self::setPinkStopTime($idAll);
                    if (self::isTpl($uidAll, $pinkT['id'])) self::orderPinkAfter($uidAll, $pinkT['id']);
                }
            }
            self::commitTrans();
            return true;
        } catch (\Exception $e) {
            self::rollbackTrans();
            return self::setErrorInfo($e->getMessage());
        }
    }

    /**
     * 获取参团人和团长和拼团总人数
     * @param array $pink
     * @return array
     * */
    public static function getPinkMemberAndPinkK($pink)
    {
        //查找拼团团员和团长
        if ($pink['k_id']) {
            $pinkAll = self::getPinkMember($pink['k_id']);
            $pinkT = self::getPinkUserOne($pink['k_id']);
        } else {
            $pinkAll = self::getPinkMember($pink['id']);
            $pinkT = $pink;
        }
        //获取虚拟拼团团员
        $pinkAll = self::getPinkTFalseList($pinkAll, $pinkT['id'], $pinkT['cid']);
        $count = count($pinkAll) + 1;
        $count = (int)$pinkT['people'] - $count;
        $idAll = [];
        $uidAll = [];
        //收集拼团用户id和拼团id
        foreach ($pinkAll as $k => $v) {
            $idAll[$k] = $v['id'];
            $uidAll[$k] = $v['uid'];
        }
        $idAll[] = $pinkT['id'];
        $uidAll[] = $pinkT['uid'];
        return [$pinkAll, $pinkT, $count, $idAll, $uidAll];
    }

    /**
     * 获取某个团的虚拟拼团人物和团
     * @param array 团员
     * @param int $pinkTId 团长id
     * @param int $cid 专题id
     * @return array
     * */
    public static function getPinkTFalseList($pinkAll, $pinkTId, $cid)
    {
        if (!is_array($pinkAll)) $pinkAll = [];
        $falseList = self::where(['a.order_id' => '', 'a.cid' => $cid, 'a.k_id' => $pinkTId, 'a.is_false' => 1, 'a.is_refund' => 0])
            ->alias('a')->join('__STORE_PINK_FALSE__ f', 'a.id=f.pink_id')->field(['a.*', 'f.nickname', 'f.avatar'])->select();
        $falseList = count($falseList) ? $falseList->toArray() : [];
        return array_merge($pinkAll, $falseList);
    }

    /**
     *  拼团完成更改数据写入内容
     * @param array $uidAll 当前拼团uid
     * @param array $idAll 当前拼团pink_id
     * @param array $pinkT 团长信息
     * @return int
     * */
    public static function PinkComplete($uidAll, $idAll, $uid, $pinkT)
    {
        $pinkBool = 6;
        if (self::setPinkStatus($idAll)) {
            self::setPinkStopTime($idAll);
            $orderAll = self::where('id', 'in', $idAll)->column('order_id');
            if (count($orderAll)) StoreOrder::PinkRake($orderAll);//拼团完成反佣金
            if (in_array($uid, $uidAll)) {
                if (self::isTpl($uidAll, $pinkT['id'])) self::orderPinkAfter($uidAll, $pinkT['id']);
                $pinkBool = 1;
            } else  $pinkBool = 3;
        }
        return $pinkBool;
    }

    /**
     * 拼团失败 退款
     * @param array $pinkAll 拼团数据,不包括团长
     * @param array $pinkT 团长数据
     * @param int $count 差几人 0为拼团成功
     * @param int $pinkBool
     * @param array $uidAll 用户uid避免虚拟用户头像重复
     * @param boolen $isRunErr 是否返回错误信息
     * @param boolen $isIds 是否返回记录所有拼团id
     * @return int| boolen
     * */
    public static function PinkFail($uid, $idAll, $pinkAll, $pinkT, $count, $pinkBool, $uidAll, $isRunErr = false, $isIds = false, $fakeUrl = '/public/system/images/fake.png')
    {
        self::startTrans();
        $pinkIds = [];
        try {
            if ($pinkT['stop_time'] < time()) {//拼团时间超时  退款
                $special = Special::PreWhere()->where(['id' => $pinkT['cid']])->find();
                //检查专题是否有虚拟成团
                if ($special && $special->is_fake_pink && $special->fake_pink_number && $count) {
                    //  fake_pink_number/100 =补齐比例  $pinkT['people']*补齐比例=补齐人数 人数小数点全部舍去
                    $fake = bcdiv($special->fake_pink_number, 100, 2);
                    $num = bcmul($pinkT['people'], $fake, 0);
                    if ($num > $count) $num = $count;
                    if (($count - (int)$num) <= 0) {
                        //获取虚拟用户头像
                        $userAvatar = User::where('status', 1)->where('uid', 'not in', $uidAll)->limit(0, $num)->column('avatar');
                        if (count($userAvatar) < $num) {
                            $usercount = $num - count($userAvatar);
                            for ($i = 0; $i < $usercount; $i++) {
                                array_push($userAvatar, $fakeUrl);
                            }
                        }
                        //写入虚拟拼团
                        foreach ($userAvatar as $item) {
                            $data = [
                                'uid' => 0,
                                'type' => 1,
                                'order_id' => '',
                                'order_id_key' => 0,
                                'total_num' => 1,
                                'total_price' => $pinkT['total_price'],
                                'cid' => $pinkT['cid'],
                                'pid' => $pinkT['pid'],
                                'price' => $pinkT['price'],
                                'add_time' => time(),
                                'stop_time' => $pinkT['stop_time'],
                                'k_id' => $pinkT['id'],
                                'is_tpl' => 0,
                                'is_refund' => 0,
                                'is_false' => 1,
                                'status' => 1,
                            ];
                            $pink_false = self::set($data);
                            if (!$pink_false) return self::setErrorInfo('写入助力拼团失败', true);
                            $res = self::getDb('store_pink_false')->insert(['pink_id' => $pink_false['id'], 'nickname' => '虚拟用户', 'avatar' => $item, 'add_time' => time()]);
                            if (!$res) return self::setErrorInfo('写入助力拼团虚拟用户失败', true);
                        }
                        //拼团完成处理
                        $pinkBool = self::PinkComplete($uidAll, $idAll, $uid, $pinkT);
                        if ($pinkBool === false) return false;
                        self::commit();
                        return $pinkBool;
                    }
                }
                //团员退款
                foreach ($pinkAll as $v) {
                    if (StoreOrder::orderApplyRefund(StoreOrder::getPinkOrderId($v['order_id_key']), $v['uid'], '拼团时间超时') && self::isTpl($v['uid'], $pinkT['id'])) {
                        self::orderPinkAfterNo($v['uid'], $v['k_id']);
                        if ($isIds) array_push($pinkIds, $v['id']);
                        $pinkBool = 2;
                    } else {
                        if ($isRunErr) return self::setErrorInfo(StoreOrder::getErrorInfo(), true);
                    }
                }
                //团长退款
                if (StoreOrder::orderApplyRefund(StoreOrder::getPinkOrderId($pinkT['order_id_key']), $pinkT['uid'], '拼团时间超时') && self::isTpl($pinkT['uid'], $pinkT['id'])) {
                    self::orderPinkAfterNo($pinkT['uid'], $pinkT['id']);
                    if ($isIds) array_push($pinkIds, $pinkT['id']);
                    $pinkBool = 2;
                } else {
                    if ($isRunErr) return self::setErrorInfo(StoreOrder::getErrorInfo(), true);
                }
                if (!$pinkBool) $pinkBool = 3;
            }
            self::commit();
            if ($isIds) return $pinkIds;
            return $pinkBool;
        } catch (\Exception $e) {
            self::rollback();
            return self::setErrorInfo($e->getMessage());
        }
    }

    /**
     * 拼团下架
     * @param int $painId 拼团id
     * @return boolen
     * */
    public static function downPink($pinId)
    {
        $pink = self::where('id', $pinId)->find();
        if (!$pink) return self::setErrorInfo('拼团信息暂未查到');
        if ($pink->status != 1) return self::setErrorInfo('拼团暂时无法下架');
        self::beginTrans();
        try {
            //获取参团人和团长和拼团总人数
            list($pinkAll, $pinkT, $count, $idAll, $uidAll) = StorePink::getPinkMemberAndPinkK($pink);
            if (!$count) return self::setErrorInfo('拼团已完成无法下架');
            //拼团失败处理退款
            $pinkIds = self::PinkFail($pink['uid'], $idAll, $pinkAll, $pinkT, $count, 1, [], true, true);
            if ($pinkIds === false) return false;
            //更新当前拼团过期时间为当前
            self::where('id', 'in', $pinkIds)->update(['stop_time' => time()]);
            self::commitTrans();
            return true;
        } catch (\Exception $e) {
            return self::setErrorInfo($e->getMessage(), true);
        }

    }

    /**
     * 获取一条拼团数据
     * @param $id
     * @return mixed
     */
    public static function getPinkUserOne($id)
    {
        $model = new self();
        $model = $model->alias('p');
        $model = $model->field('p.*,u.nickname,u.avatar');
        $model = $model->where('id', $id);
        $model = $model->join('__USER__ u', 'u.uid = p.uid');
        $list = $model->find();
        if ($list) return $list->toArray();
        else return [];
    }

    /**
     * 获取拼团的团员
     * @param $id
     * @return mixed
     */
    public static function getPinkMember($id, $retrn_array = false)
    {
        $model = new self();
        $model = $model->alias('p');
        $model = $model->field('p.*,u.nickname,u.avatar');
        $model = $model->where('p.k_id', $id);
        $model = $model->where('p.is_refund', 0);
        $model = $model->join('__USER__ u', 'u.uid = p.uid');
        $model = $model->order('p.id asc');
        if ($retrn_array) return $model->count();
        $list = $model->select();
        if ($list) return $list->toArray();
        else return [];
    }

    /**
     * 设置结束时间
     * @param $idAll
     * @return $this
     */
    public static function setPinkStopTime($idAll)
    {
        $model = new self();
        $model = $model->where('id', 'IN', $idAll);
        self::setSpecialBuy($idAll);
        return $model->update(['stop_time' => time(), 'status' => 2]);
    }

    public static function setSpecialBuy($idAll)
    {
        $allOrderId = self::where('id', 'in', $idAll)->column('order_id');
        foreach ($allOrderId as $order_id) {
            if ($order = StoreOrder::where(['order_id' => $order_id, 'paid' => 1])->find()) {
                SpecialBuy::setAllBuySpecial($order->order_id, $order->uid, $order->cart_id, 1);
            }
        }
    }

    /**
     * 获取正在拼团的数据  团长
     * @return mixed
     */
    public static function getPinkAll($cid, $pinkId = 0, $limit = 0)
    {
        $model = new self();
        $model = $model->alias('p');
        $model = $model->field('p.*,u.nickname,u.avatar');
        $model = $model->where('p.stop_time', 'GT', time());
        $model = $model->where('p.cid', $cid);
        $model = $model->where('p.k_id', 0);
        $model = $model->where('p.is_refund', 0);
        $model = $model->where('p.status', 1);
        $model = $model->order('p.add_time desc');
        $model = $model->join('__USER__ u', 'u.uid = p.uid');
        if ($limit) $model = $model->limit($limit);
        if ($pinkId) $model = $model->where('p.id', 'neq', $pinkId);
        $list = $model->select();
        if ($list) return $list->toArray();
        else return [];
    }

    public static function setPinkIng($pink, $uid)
    {
        list($pinkAll, $pinkT, $count, $idAll, $uidAll) = StorePink::getPinkMemberAndPinkK($pink);
        if ($pinkT['status'] != 2) {
            if (!$count) {//组团完成
                self::PinkComplete($uidAll, $idAll, $uid, $pinkT);
            } else {
                //组团时间到退款
                self::PinkFail($uid, $idAll, $pinkAll, $pinkT, $count, 0, $uidAll);
            }
        }
    }

    /**
     * 参加拼团的人  商品id
     * @return mixed
     */
    public static function getPinkAttend($cid, $type = 1)
    {
        $model = new self();
        $model = $model->alias('p');
        $model = $model->field('u.avatar');
        $model = $model->where(['p.cid' => $cid, 'p.type' => $type, 'p.is_refund' => 0]);
        $model = $model->order('p.add_time desc');
        $model = $model->join('__USER__ u', 'u.uid = p.uid');
        $list = $model->distinct(true)->select();
        if ($list) return $list->toArray();
        else return [];
    }

    public static function getPinkAttendFalse($cid, $type = 1, $limit = 20)
    {
        $userList = self::where(['a.cid' => $cid, 'a.type' => $type, 'a.is_refund' => 0])
            ->distinct(true)->order('a.add_time desc')->alias('a')
            ->join('__STORE_PINK_FALSE__ s', 's.pink_id=a.id')
            ->field('s.avatar')->limit($limit)->select();
        return count($userList) ? $userList->toArray() : [];
    }

    /**
     * 获取还差几人
     */
    public static function getPinkPeople($kid, $people)
    {
        $model = new self();
        $model = $model->where('k_id', $kid)->where('is_refund', 0);
        $count = bcadd($model->count(), 1, 0);
        return bcsub($people, $count, 0);
    }

    /**
     * 判断订单是否在当前的拼团中
     * @param $orderId
     * @param $kid
     * @return bool
     */
    public static function getOrderIdAndPink($orderId, $kid)
    {
        $model = new self();
        $pink = $model->where('k_id', $kid)->whereOr('id', $kid)->column('order_id');
        if (in_array($orderId, $pink)) return true;
        else return false;
    }

    /**
     * 判断用户是否在团内
     * @param $id
     * @return int|string
     */
    public static function getIsPinkUid($id)
    {
        $uid = User::getActiveUid();
        $pinkT = self::where('id', $id)->where('uid', $uid)->where('is_refund', 0)->count();
        $pink = self::whereOr('k_id', $id)->where('uid', $uid)->where('is_refund', 0)->count();
        if ($pinkT) return true;
        if ($pink) return true;
        else return false;
    }


    /**
     * 判断是否发送模板消息 0 未发送 1已发送
     * @param $uidAll
     * @return int|string
     */
    public static function isTpl($uidAll, $pid)
    {
        if (is_array($uidAll)) {
            $countK = self::where('uid', 'IN', implode(',', $uidAll))->where('is_tpl', 0)->where('id', $pid)->count();
            $count = self::where('uid', 'IN', implode(',', $uidAll))->where('is_tpl', 0)->where('k_id', $pid)->count();
        } else {
            $countK = self::where('uid', $uidAll)->where('is_tpl', 0)->where('id', $pid)->count();
            $count = self::where('uid', $uidAll)->where('is_tpl', 0)->where('k_id', $pid)->count();
        }
        return bcadd($countK, $count, 0);
    }

    /**
     * 拼团成功提示模板消息
     * @param $uidAll
     * @param $pid
     */
    public static function orderPinkAfter($uidAll, $pid)
    {
        foreach ($uidAll as $v) {
            try {
                if ($openid = WechatUser::uidToOpenid($v)) {
                    $cart_id = self::alias('p')->where('p.id', $pid)->whereOr('p.k_id', $pid)->where('p.uid', $v)->join('__STORE_ORDER__ a', 'a.order_id=p.order_id')->value('a.cart_id');
                    WechatTemplateService::sendTemplate($openid, WechatTemplateService::ORDER_USER_GROUPS_SUCCESS, [
                        'first' => '亲，您的拼团已经完成了',
                        'keyword1' => self::where('id', $pid)->whereOr('k_id', $pid)->where('uid', $v)->value('order_id'),
                        'keyword2' => Special::PreWhere()->where('id', $cart_id)->value('title'),
                        'remark' => '点击查看订单详情'
                    ], Url::build('wap/special/order_pink', ['pink_id' => $pid], true, true));
                }
            } catch (\Exception $e) {
                break;
            }
        }
        self::where('uid', 'IN', implode(',', $uidAll))->where('id', $pid)->whereOr('k_id', $pid)->update(['is_tpl' => 1]);
    }

    /**
     * 拼团失败发送的模板消息
     * @param $uid
     * @param $pid
     */
    public static function orderPinkAfterNo($uid, $pid)
    {
        $openid = WechatUser::uidToOpenid($uid);
        WechatTemplateService::sendTemplate($openid, WechatTemplateService::ORDER_USER_GROUPS_LOSE, [
            'first' => '亲，您的拼团失败',
            'keyword1' => self::alias('p')->where('p.id', $pid)->whereOr('p.k_id', $pid)->where('p.uid', $uid)->join('__SPECIAL__ c', 'c.id=p.cid')->value('c.title'),
            'keyword2' => self::where('id', $pid)->whereOr('k_id', $pid)->where('uid', $uid)->value('price'),
            'keyword3' => self::alias('p')->where('p.id', $pid)->whereOr('p.k_id', $pid)->where('p.uid', $uid)->join('__STORE_ORDER__ c', 'c.order_id=p.order_id')->value('c.pay_price'),
            'remark' => '点击查看订单详情'
        ], Url::build('My/order_pink', ['pink_id' => $pid], true, true));
        self::where('id', $pid)->update(['status' => 3]);
        self::where('k_id', $pid)->update(['status' => 3]);
    }

    /**
     * 获取当前拼团数据返回订单编号
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getCurrentPink($id)
    {
        $uid = User::getActiveUid();//获取当前登录人的uid
        $pink = self::where('id', $id)->where('uid', $uid)->find();
        if (!$pink) $pink = self::where('k_id', $id)->where('uid', $uid)->find();
        return StoreOrder::where('id', $pink['order_id_key'])->value('order_id');
    }

    /**
     * 设置where条件
     * @param $where
     * @param string $alias
     * @return $this|StorePink
     */
    public static function setWhere($where, $alias = '')
    {
        self::setPinkStop((int)$where['page']);
        $model = new self;
        if ($alias) {
            $model = $model->alias($alias);
            $alias .= '.';
        }
        if ($where['data'] !== '-' && !empty($where['data'])) {
            list($startTime, $endTime) = explode('-', $where['data']);
            $model = $model->where($alias . 'add_time', '>', strtotime($startTime));
            $model = $model->where($alias . 'add_time', '<', strtotime($endTime));
        }
        if ($where['status']) $model = $model->where($alias . 'status', $where['status']);
        if ($where['cid']) $model = $model->where($alias . 'cid', $where['cid']);
        if ($where['nickname']) $model = $model->where($alias . 'order_id|' . $alias . 'order_id_key|' . $alias . 'uid', 'LIKE', "%$where[nickname]%");
        $model = $model->where($alias . 'k_id', 0);
        return $model;
    }

    /**
     * @param $where
     * @return array
     * @throws \think\Exception
     */
    public static function getPinkList($where)
    {
        $data = self::setWhere($where, 'a')->field(['a.*', 'u.nickname'])->join('__USER__ u', 'u.uid=a.uid', 'LEFT')->order('a.id desc')
            ->page((int)$where['page'], (int)$where['limit'])->select();

        foreach ($data as &$item) {
            if ($item['status'] == 1 && $item['stop_time'] < time()) {
                $pinkall = self::where(['k_id' => $item['id']])->column('id');
                array_push($pinkall, $item['id']);
                if (!($item['people'] - count($pinkall))) {
                    self::where('id', 'in', $pinkall)->update(['status' => 2]);
                    $item['status'] = 2;
                } else {
                    self::where('id', 'in', $pinkall)->update(['status' => 3]);
                    $item['status'] = 3;
                }
            }
            $item['count_people'] = bcadd(self::where('k_id', $item['id'])->count(), 1, 0);
            $item['title'] = Special::where('id', $item['cid'])->value('title');
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['stop_time'] = date('Y-m-d H:i:s', $item['stop_time']);
            $item['people_true'] = self::where(['k_id' => $item['id'], 'is_false' => 0])->count() + 1;
        }
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    /**
     * 处理拼团结束
     * @param int $page
     */
    public static function setPinkStop($page = 1)
    {
        $list = self::where(['k_id' => 0, 'status' => 1])->where('stop_time', '<', time())->page($page, 10)->select();
        foreach ($list as $item) {
            //获取参团人和团长和拼团总人数
            list($pinkAll, $pinkT, $count, $idAll) = StorePink::getPinkMemberAndPinkK($item);
            //拼团失败处理退款
            self::PinkFail($item['uid'], $idAll, $pinkAll, $pinkT, $count, 1, [], true, true);
        }
    }

    public static function systemPage($where)
    {
        $model = new self;
        $model = $model->alias('p');
        $model = $model->field('p.*');
        if ($where['data'] !== '') {
            list($startTime, $endTime) = explode(' - ', $where['data']);
            $model = $model->where('add_time', '>', strtotime($startTime));
            $model = $model->where('add_time', '<', strtotime($endTime));
        }
        if ($where['status']) $model = $model->where('status', $where['status']);
        $model = $model->where('k_id', 0);
        $model = $model->order('id desc');
        return self::page($model, function ($item) use ($where) {
            $item['count_people'] = bcadd(self::where('k_id', $item['id'])->count(), 1, 0);
            $item['title'] = Special::where('id', $item['cid'])->value('title');
        }, $where);
    }

    public static function isPinkBe($data, $id)
    {
        $data['id'] = $id;
        $count = self::where($data)->count();
        if ($count) return $count;
        $data['k_id'] = $id;
        $count = self::where($data)->count();
        if ($count) return $count;
        else return 0;
    }

    public static function isPinkStatus($pinkId)
    {
        if (!$pinkId) return false;
        $stopTime = self::where('id', $pinkId)->value('stop_time');
        if ($stopTime < time()) return true; //拼团结束
        else return false;//拼团未结束
    }

    /**
     * 判断拼团结束 后的状态
     * @param $pinkId
     * @return bool
     */
    public static function isSetPinkOver($pinkId)
    {
        $people = self::where('id', $pinkId)->value('people');
        $stopTime = self::where('id', $pinkId)->value('stop_time');
        if ($stopTime < time()) {
            $countNum = self::getPinkPeople($pinkId, $people);
            if ($countNum) return false;//拼团失败
            else return true;//拼团成功
        } else return true;
    }

    /**
     * 拼团退款
     * @param $id
     * @return bool
     */
    public static function setRefundPink($oid)
    {
        $res = true;
        $order = StoreOrder::where('id', $oid)->find();
        if ($order['pink_id']) $id = $order['pink_id'];
        else return $res;
        $count = self::where('id', $id)->where('uid', $order['uid'])->find();//正在拼团 团长
        $countY = self::where('k_id', $id)->where('uid', $order['uid'])->find();//正在拼团 团员
        if (!$count && !$countY) return $res;
        if ($count) {//团长
            //判断团内是否还有其他人  如果有  团长为第二个进团的人
            $kCount = self::where('k_id', $id)->order('add_time asc')->find();
            if ($kCount) {
                $res11 = self::where('k_id', $id)->update(['k_id' => $kCount['id']]);
                $res12 = self::where('id', $kCount['id'])->update(['stop_time' => $count['add_time'] + 86400, 'k_id' => 0]);
                $res1 = $res11 && $res12;
                $res2 = self::where('id', $id)->update(['stop_time' => time() - 1, 'k_id' => 0, 'is_refund' => $kCount['id'], 'status' => 3]);
            } else {
                $res1 = true;
                $res2 = self::where('id', $id)->update(['stop_time' => time() - 1, 'k_id' => 0, 'is_refund' => $id, 'status' => 3]);
            }
            //修改结束时间为前一秒  团长ID为0
            $res = $res1 && $res2;
        } else if ($countY) {//团员
            $res = self::where('id', $countY['id'])->update(['stop_time' => time() - 1, 'k_id' => 0, 'is_refund' => $id, 'status' => 3]);
        }
        return $res;

    }


    /**
     * 拼团人数完成时，判断全部人都是未退款状态
     * @param $pinkIds
     * @return bool
     */
    public static function setPinkStatus($pinkIds)
    {
        $orderPink = self::where('id', 'IN', $pinkIds)->where('is_refund', 1)->count();
        if (!$orderPink) return true;
        else return false;
    }


    /**
     * 创建拼团
     * @param $order
     * @return mixed
     */
    public static function createPink($order)
    {
        $order = StoreOrder::tidyOrder($order, true)->toArray();
        //开团的用户走else
        if ($order['pink_id']) {//拼团存在
            $res = false;
            $pink['uid'] = $order['uid'];//用户id
            if (self::isPinkBe($pink, $order['pink_id'])) return false;
            $pink['type'] = 1;
            $pink['order_id'] = $order['order_id'];//订单id  生成
            $pink['order_id_key'] = $order['id'];//订单id  数据库id
            $pink['total_num'] = $order['total_num'];//购买个数
            $pink['total_price'] = $order['pay_price'];//总金额
            $pink['k_id'] = $order['pink_id'];//拼团id
            $special = Special::PreWhere()->where('id', $order['cart_id'])->find();
            if (!$special) return false;
            $pink['cid'] = $order['combination_id'];//拼团产品id
            $pink['pid'] = $order['cart_id'];//产品id
            $pink['people'] = $special['pink_number'];//几人拼团
            $pink['price'] = $special['is_fake_pink'] ? $special['pink_money'] : $special['money'];//单价
            $pink['stop_time'] = 0;//结束时间
            $pink['add_time'] = time();//开团时间
            $res = $res1 = self::set($pink)->toArray();
            $openid = WechatUser::uidToOpenid($order['uid']) ? WechatUser::uidToOpenid($order['uid']) : "";
            if ($openid) {
                WechatTemplateService::sendTemplate($openid, WechatTemplateService::ORDER_USER_PING_Ok, [
                    'first' => '恭喜您拼团成功，点击查看拼团详情~',
                    'keyword1' => $order['order_id'],
                    'keyword2' => self::alias('p')->where('p.id', $res1['id'])->where('p.uid', $res1['uid'])->join('__SPECIAL__ c', 'c.id=p.cid')->value('c.title'),
                    'remark' => '分享至朋友圈或好友群，成团速度更快哦~'
                ], Url::build('wap/special/order_pink', ['pink_id' => $res1['id']], true, true));
            }
            if ($people = self::getPinkPeople($order['pink_id'], $special['pink_number'])) {

                $userInfo = User::where('uid', $order['uid'])->find();
                if (!$userInfo->is_promoter && $userInfo->spread_uid) {
                    $spreadOpenid = WechatUser::uidToOpenid($userInfo->spread_uid);
                    WechatTemplateService::sendTemplate($spreadOpenid, WechatTemplateService::PINK_ORDER_REMIND, [
                        'first' => '您的好友【' . $userInfo->nickname . '】通过您的邀请开始了【' . $special['title'] . '】的拼团！',
                        'keyword1' => $special['title'],
                        'keyword2' => '差' . $people . '人',
                        'remark' => '点我立即协助【' . $userInfo->nickname . '】拼团,拼团成功即可获得佣金！'
                    ], Url::build('wap/special/order_pink', ['pink_id' => $res1['id'], 'is_help' => 1], true, true));
                }
            }
            //处理拼团完成
            list($pinkAll, $pinkT, $count, $idAll, $uidAll) = self::getPinkMemberAndPinkK($pink);
            if ($pinkT['status'] == 1) {
                if (!$count || $count < 0)//组团完成
                    self::PinkComplete($uidAll, $idAll, $pink['uid'], $pinkT);
                else
                    self::PinkFail($pink['uid'], $idAll, $pinkAll, $pinkT, $count, 0, $uidAll);
            } else if ($pinkT['status'] == 2) {
                //如果是专栏，记录专栏下所有专题购买。
               /* $special = Special::get($order['cart_id']);
                if ($special['type'] == SPECIAL_COLUMN) {
                    $special_source = SpecialSource::getSpecialSource($special['id']);
                    if ($special_source){
                        foreach($special_source as $k => $v) {
                            SpecialBuy::setBuySpecial($order['order_id'], $order['uid'], $v['source_id']);
                        }
                    }
                }*/
                SpecialBuy::setAllBuySpecial($order['order_id'], $order['uid'], $order['cart_id'], 1);
                self::where('id', $res1['id'])->update(['is_tpl' => 1, 'stop_time' => time(), 'status' => 2]);
            }
            if ($res) return true;
            else return false;
        } else {
            $res = false;
            $pink['type'] = 1;
            $pink['uid'] = $order['uid'];//用户id
            $pink['order_id'] = $order['order_id'];//订单id  生成
            $pink['order_id_key'] = $order['id'];//订单id  数据库id
            $pink['total_num'] = $order['total_num'];//购买个数
            $pink['total_price'] = $order['pay_price'];//总金额
            $pink['k_id'] = 0;//拼团id
            $pink['cid'] = $order['combination_id'];//拼团产品id
            $pink['pid'] = $order['cart_id'];//产品id
            $special = Special::PreWhere()->where('id', $order['cart_id'])->find();
            if (!$special) return false;
            $pink['people'] = $special['pink_number'];//几人拼团
            $pink['price'] = $special['is_fake_pink'] ? $special['pink_money'] : $special['money'];//单价
            $pink['stop_time'] = bcadd(time(), bcmul($order['pink_time'], 3600, 0), 0);//结束时间
            $pink['add_time'] = time();//开团时间
            $res1 = self::set($pink)->toArray();
            $res2 = StoreOrder::where('id', $order['id'])->update(['pink_id' => $res1['id']]);
            $res = $res1 && $res2;
            $openid = WechatUser::uidToOpenid($order['uid']);
            WechatTemplateService::sendTemplate($openid, WechatTemplateService::ORDER_USER_PINGT_SUCCESS, [
                'first' => '您已成功开团，点击查看拼团详情~',
                'keyword1' => $special['title'],
                'keyword2' => $special['pink_money'],
                'keyword3' => $special['pink_number'],
                'keyword4' => date('Y-m-d H:i:s', $pink['add_time']),
                'remark' => '分享至朋友圈或好友群，成团速度更快哦~'
            ], Url::build('wap/special/order_pink', ['pink_id' => $res1['id']], true, true));
            $userInfo = User::where('uid', $order['uid'])->find();
            try {
                if ($userInfo && !$userInfo->is_promoter && $userInfo->spread_uid) {
                    $spreadOpenid = WechatUser::uidToOpenid($userInfo->spread_uid);
                    $people = self::getPinkPeople($res1['id'], $special['pink_number']);
                    WechatTemplateService::sendTemplate($spreadOpenid, WechatTemplateService::PINK_ORDER_REMIND, [
                        'first' => '您的好友【' . $userInfo->nickname . '】通过您的邀请开始了【' . $special['title'] . '】的拼团！',
                        'keyword1' => $special['title'],
                        'keyword2' => '差' . $people . '人',
                        'remark' => '点我立即协助【' . $userInfo->nickname . '】拼团,拼团成功即可获得佣金！'
                    ], Url::build('wap/special/order_pink', ['pink_id' => $res1['id'], 'is_help' => 1], true, true));
                }
            } catch (\Exception $e) {
            }

            if ($res) return true;
            else return false;
        }
    }
}