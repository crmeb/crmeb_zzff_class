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

namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\MemberCard as MemberCardMode;
use app\admin\model\user\MemberCardBatch;
/**
 * 会员设置 model
 * Class User
 * @package app\admin\model\user
 */

class MemberRecord extends ModelBasic
{
    use ModelTrait;

    public static function getPurchaseRecordList($where){
        $model = self::setWherePage(self::setWhere($where), $where, ['u.nickname', 'u.uid'], ['p.uid']);
        $model=$model->alias('p')
            ->join('user u', 'p.uid=u.uid', 'left')
            ->field('p.*,u.nickname');

          $list = $model ->page((int)$where['page'], (int)$where['limit'])
            ->order('p.add_time DESC')
            ->select()
            ->each(function ($item) {
                $item['overdue_time'] = date('Y-m-d H:i:s', $item['overdue_time']);
                $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
                if($item['type']==1){
                    $item['price'] ='无';
                }
                if(!$item['type']){
                    switch ($item['validity']){
                        case 30:
                            $item['title'] = '月卡';
                            break;
                        case 90:
                            $item['title'] = '季卡';
                            break;
                        case 365:
                            $item['title'] = '年卡';
                            break;
                        case -1:
                            $item['title'] = '终身卡';
                            break;
                        default:
                            $item['title'] = '免费';
                    }
                }else{
                    $item['title'] = '卡密';
                }
                if (!$item['code']) {
                    $item['code'] = '无';
                }
                $item['uid'] =$item['nickname'].'/'.$item['uid'];
            })->toArray();
        $count = self::setWherePage(self::setWhere($where), $where,['u.nickname', 'u.uid'], ['p.uid'])->alias('p')->join('user u', 'p.uid=u.uid', 'left')->count();
        return ['count' => $count, 'data' => $list];
    }
    /*
      * 设置搜索条件
      *
      */
    public static function setWhere($where)
    {
        $model=new self;
        if ($where['title'] != '') {
            $model = $model->where('p.uid|u.nickname','like',"%$where[title]%");
        }
        if ($where['type'] != '') {
            switch ($where['type']){
                case 1:
                    $model=$model->where('p.validity',30)->where('p.type',0);
                    break;
                case 2:
                    $model=$model->where('p.validity',90)->where('p.type',0);
                    break;
                case 3:
                    $model=$model->where('p.validity',365)->where('p.type',0);
                    break;
                case 4:
                    $model=$model->where('p.validity','<',0)->where('p.type',0);
                    break;
                case 5:
                    $model=$model->where('p.type',1);
                    break;
            }

        }
        return $model;
    }
    public static function userOneRecord($uid=0){
        $model = new self;
        if ($uid != '') $model = $model->where('a.uid',$uid);
        $model = $model->alias('a');
        $model = $model->field('a.*,b.nickname');
        $model = $model->join('__USER__ b', 'b.uid=a.uid', 'LEFT');
        $model = $model->order('a.id desc');
        return self::page($model,function ($item){
            $item['overdue_time'] = date('Y-m-d H:i:s', $item['overdue_time']);
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            if($item['type']==1){
                $item['price'] ='无';
            }
            if(!$item['type']){
                switch ($item['validity']){
                    case 30:
                        $item['title'] = '月卡';
                        break;
                    case 90:
                        $item['title'] = '季卡';
                        break;
                    case 365:
                        $item['title'] = '年卡';
                        break;
                    case -1:
                        $item['title'] = '终身卡';
                        break;
                    default:
                        $item['title'] = '免费';
                }
            }else{
                $item['title'] = '卡密';
            }
            if (!$item['code']) {
                $item['code'] = '无';
            }
        });
    }
}