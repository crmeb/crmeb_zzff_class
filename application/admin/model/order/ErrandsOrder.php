<?php
namespace app\admin\model\order;

use service\PHPExcelService;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\user\User;

/**
 * 跑腿订单管理Model
 * Class ErrandsOrder
 * @package app\admin\model\store
 */
class ErrandsOrder extends ModelBasic
{
    use ModelTrait;

    public static function OrderList($where){
        $list=self::setWhere($where)->order('add_time desc')->page((int)$where['page'],(int)$where['limit'])->select();
        $data=count($list) ? $list->toArray() : [];
        foreach ($data as &$item){
            $item['nickname']=User::where('uid',$item['uid'])->value('nickname');
            $ordertype=self::setOrderType($item['order_type']);
            $item['pink_name']=$ordertype['pink_name'];
            $item['color']=$ordertype['color'];
            $pay_type=self::setPayType($item['paid'],$item['pay_type']);
            $item['pay_type_name']=$pay_type['pay_type_name'];
            $item['pay_type_info']=$pay_type['pay_type_info'];
            $item=self::setStatusName($item);
            if($item['delivery_time']==0){
                $item['delivery_time']='不限时间';
            }else{
                $item['delivery_time']=$item['delivery_time'].'分钟内送达';
            }
        }
        if($where['excel']) self::SaveExcel($data);
        $count=self::setWhere($where)->count();
        return compact('data','count');
    }

    /*
     * 保存并下载excel
     * $list array
     * return
     */
    public static function SaveExcel($list){
        $export = [];
        foreach ($list as $index=>$item){
            $export[] = [
                $item['order_id'],
                $item['pay_type_name'],
                $item['total_price'],
                $item['delivery_price'],
                $item['pay_price'],
                $item['refund_price'],
                $item['user_make'],
                $item['admin_make'],
                [$item['real_name'],$item['user_phone'],$item['user_address']],
                [$item['paid'] == 1? '已支付':'未支付','支付时间: '.($item['pay_time'] > 0 ? date('Y/md H:i',$item['pay_time']) : '暂无')]
            ];
        }
        PHPExcelService::setExcelHeader(['订单号','支付方式','总价','配送费','支付金额','退款金额','用户备注','管理员备注','收货人信息','支付状态'])
            ->setExcelTile('订单导出','订单信息'.time(),' 生成时间：'.date('Y-m-d H:i:s',time()))
            ->setExcelContent($export)
            ->ExcelSave();
    }

    public static function setWhere($where){
        $model=self::setSQLStatus($where['status']);
        if($where['real_name']!='') $model->where('real_name|good_name',"%$where[real_name]%");
        if($where['order_type']!='') $model->where('order_type',$where['order_type']);
        return self::getModelTime($where,$model);
    }

    public static function setSQLStatus($status,$alias='',$model=null){
        if($model===null) $model=new self();
        if($alias) $alias.='.';
        if($status==='') return $model;
        switch ($status){
            case 5:
                //未支付
                $model->where($alias.'paid',0)->where($alias.'refund_status',0);
                break;
            case 0:
                //待接单
                $model->where($alias.'status',0)->where($alias.'paid',1)->where($alias.'refund_status',0);
                break;
            case 1:
                //派送中
                $model->where($alias.'status',1)->where($alias.'paid',1)->where($alias.'refund_status',0);
                break;
            case 2:
                //已送达
                $model->where($alias.'status',2)->where($alias.'paid',1)->where($alias.'refund_status',0);
                break;
            case -2:
                //已退款
                $model->where($alias.'status',-2)->where($alias.'paid',1)->where($alias.'refund_status',2);
                break;
        }
        return $model;
    }
    public static function setPayType($paid,$pay_type){
        $pay_type_name='';
        $pay_type_info=0;
        if($paid==1){
            switch ($pay_type){
                case 'weixin':
                    $pay_type_name='微信支付';
                    break;
                case 'yue':
                    $pay_type_name='微信支付';
                    break;
                case 'offline':
                    $pay_type_name='线下支付';
                    break;
                default:
                    $pay_type_name='其他支付';
                    break;
            }
        }else{
            switch ($pay_type){
                default:
                    $pay_type_name='未支付';
                    break;
                case 'offline':
                    $pay_type_name='线下支付';
                    $pay_type_info=1;
                    break;
            }
        }
        return compact('pay_type_name','pay_type_info');
    }

    public static function setOrderType($order_type){
        $item=[];
        switch ($order_type){
            case 1:
                $item['pink_name']='[帮我买订单]';
                $item['color']='#32c5e9';
                break;
            case 2:
                $item['pink_name']='[帮我送订单]';
                $item['color']='#7B68EE';
                break;
            case 3:
                $item['pink_name']='[帮我取订单]';
                $item['color']='#7B68EE';
                break;
            case 4:
                $item['pink_name']='[个性服务订单]';
                $item['color']='#EE82EE';
                break;
            default:
                $item['pink_name']='[其他订单]';
                $item['color']='#778899';
                break;
        }
        return $item;
    }

    public static function setStatusName($item){
        if($item['paid']==0 && $item['status']==0){
            $item['status_name']='未支付';
        }else if($item['paid']==1 && $item['status']==0 && $item['refund_status']==0){
            $item['status_name']='待接单';
        }else if($item['paid']==1 && $item['status']==1 && $item['refund_status']==0){
            $item['status_name']='配送中';
        }else if($item['paid']==1 && $item['status']==2 && $item['refund_status']==0){
            $item['status_name']='待评价';
        }else if($item['paid']==1 && $item['status']==3 && $item['refund_status']==0){
            $item['status_name']='已完成';
        }else if($item['paid']==1 && $item['refund_status']==1){
            $item['status_name']=<<<HTML
<b style="color:#f124c7">申请退款</b><br/>
<span>退款原因：{$item['refund_reason_wap']}</span>
HTML;
        }else if($item['paid']==1 && $item['refund_status']==2){
            $item['status_name']='已退款';
        }
        return $item;
    }

}