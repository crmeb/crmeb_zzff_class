<?php

namespace app\wap\model\special;

use basic\ModelBasic;
use traits\ModelTrait;

class SpecialBuy extends ModelBasic
{
    use ModelTrait;

    protected function setAddTimeAttr()
    {
        return time();
    }

    protected function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    protected function getTypeAttr($value)
    {
        $name = '';
        switch ($value) {
            case 0:
                $name = '支付获得';
                break;
            case 1:
                $name = '拼团获得';
                break;
            case 2:
                $name = '领取礼物获得';
                break;
            case 3:
                $name = '赠送获得';
                break;
        }
        return $name;
    }
    public static function setAllBuySpecial($order_id, $uid, $special_id, $type = 0)
    {
        if (!$order_id || !$uid || !$special_id) return false;
        //如果是专栏，记录专栏下所有专题购买。
        $special = Special::get($special_id);
        if ($special['type'] == SPECIAL_COLUMN) {
            $special_source = SpecialSource::getSpecialSource($special['id']);
            if ($special_source){
                foreach($special_source as $k => $v) {
                    $task_special = Special::get($v['source_id']);
                    if ($task_special['is_show'] == 1){
                        self::setBuySpecial($order_id, $uid, $v['source_id'], $type);
                    }
                }
            }
            self::setBuySpecial($order_id, $uid, $special_id, $type);
        }else{
            self::setBuySpecial($order_id, $uid, $special_id, $type);
        }
    }

    public static function setBuySpecial($order_id, $uid, $special_id, $type = 0)
    {
        $add_time = time();
        if (self::be(['order_id' => $order_id, 'uid' => $uid, 'special_id' => $special_id, 'type' => 0])) return false;
        return self::set(compact('order_id', 'uid', 'special_id', 'type', 'add_time'));
    }

    public static function PaySpecial($special_id, $uid)
    {
        return self::where(['uid' => $uid, 'special_id' => $special_id, 'is_del' => 0])->count() ? true : false;
    }

    public static function getPayList($where)
    {
        $list = self::where(['a.uid' => $where['uid']])->alias('a')->join('__SPECIAL__ s', 's.id=a.special_id')
            ->field('a.*,s.title')->order('a.add_time desc')->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($list as &$item) {
            $item['pay_price'] = self::getDb('store_order')->where('order_id', $item['order_id'])->value('pay_price');
        }
        return $list;
    }
}