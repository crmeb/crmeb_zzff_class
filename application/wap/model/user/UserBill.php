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

namespace app\wap\model\user;


use app\wap\model\special\Special;
use app\wap\model\store\StoreOrder;
use basic\ModelBasic;
use traits\ModelTrait;

class UserBill extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function income($title, $uid, $category, $type, $number, $link_id = 0, $balance = 0, $mark = '', $status = 1, $get_uid = 0)
    {
        $pm = 1;
        return self::set(compact('title', 'uid', 'link_id', 'category', 'type', 'number', 'balance', 'mark', 'status', 'pm', 'get_uid'));
    }

    public static function expend($title, $uid, $category, $type, $number, $link_id = 0, $balance = 0, $mark = '', $status = 1)
    {
        $pm = 0;
        return self::set(compact('title', 'uid', 'link_id', 'category', 'type', 'number', 'balance', 'mark', 'status', 'pm'));
    }

    public static function getSginDay($year, $month, $uid)
    {

        $model = self::where('uid', $uid)->where(['category' => 'integral', 'type' => 'sign', 'status' => 1, 'pm' => 1]);
        if (!$year && !$month) {
            $model->whereTime('add_time', 'm');
        } else {
            $t = date('t', strtotime($year . '-' . $month));
            $model->whereTime('add_time', 'between', [strtotime($year . '-' . $month), strtotime($year . '-' . $month . '-' . $t)]);
        }
        $list = $model->field(['from_unixtime(add_time,\'%d\') as time'])->order('time asc')->select();
        count($list) && $list = $list->toArray();
        foreach ($list as &$item) {
            $item['day'] = ltrim($item['time'], '\0');
        }
        return $list;
    }

    /**
     * 获取提现记录或者佣金记录
     * @param $where arrat 查询条件
     * @param $uid int 用户uid
     * @return array
     *
     * */
    public static function getSpreadList($where, $uid)
    {
        $uids = User::where('spread_uid', $uid)->column('uid');
        $uids1 = User::where('spread_uid', 'in', $uids)->group('uid')->column('uid');
        $model = self::where('a.uid', $uid)->alias('a')->join('__USER__ u', 'u.uid=a.uid')->where('a.link_id', 'neq', 0)->order('a.add_time desc');
        switch ((int)$where['type']) {
            case 0:
                $model=$model->join('store_order o', 'o.id = a.link_id')->whereIn('o.uid', $uids);
                $model = $model->where('a.category', 'now_money')->where('a.type', 'in', ['brokerage']);
                break;
            case 1:
                $model=$model->join('store_order o', 'o.id = a.link_id')->whereIn('o.uid', $uids1);
                $model = $model->where('a.category', 'now_money')->where('a.type', 'brokerage');
                break;
            case 2:
                $model = $model->where('a.category', 'now_money')->where('a.type', 'extract');
                break;
        }
        if ($where['data']) {
            $where['data'] = str_replace(['年', '月'], ['-', ''], $where['data']);
            $starttime = strtotime($where['data']);
            $dayArray = explode('-', $where['data']);
            $day = self::DaysInMonth($dayArray[0], $dayArray[1]);
            $endtime = bcadd(strtotime($where['data']), bcmul($day, 86400, 0), 0);
            $model = $model->where('a.add_time', 'between', [$starttime, $endtime]);
        }
        $list = $model->field(['a.get_uid', 'a.mark', 'a.title', 'a.number','FROM_UNIXTIME(a.add_time,"%Y-%c-%d %H:%i:%s") as add_time', 'a.pm', 'a.link_id'])
            ->page((int)$where['page'], (int)$where['limit'])->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item) {
            $item['nickname'] = '';
            if ((int)$where['type'] == 0 || (int)$where['type'] == 1) {
                $item['nickname'] = User::where('uid', $item['get_uid'])->value('nickname');
                $item['title'] = Special::PreWhere()->where('id', self::getDb('store_order')->where('id', $item['link_id'])->value('cart_id'))->value('title');
            }
//            $item['add_time'] =date('Y-m-d H:i:s',$item['add_time']);
        }
        $page = $where['page'] + 1;
        return compact('list', 'page');
    }

    /*
     * 获得某年某月的天数
     * */
    public static function DaysInMonth($month, $year)
    {
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }
    /*
         * 获取总佣金
         * */
    public static function getBrokerage($uid,$category = 'now_money',$type='brokerage',$where)
    {
        return self::getModelTime($where,self::where('uid','in',$uid)->where('category',$category)
            ->where('type',$type)->where('pm',1)->where('status',1))->sum('number');
    }

    public static function getUserGoldBill(array $where,$page = 0,$limit = 10)
    {
        $model = self::where('status',1);
        if ($where){
            $list = $model->where($where);
        }
        $list = $model->order('add_time desc')->page((int)$page,(int)$limit)->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item){
            $item['_add_time']  = date('Y-m-d H:i:s',$item['add_time']);
        }
        $page--;
        return ['list'=>$list,'page'=> $page];
    }

}