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


namespace app\admin\model\ump;


use traits\ModelTrait;
use basic\ModelBasic;
use think\Db;
use app\admin\model\user\User;
use service\PHPExcelService;

class EventSignUp extends ModelBasic
{
    use ModelTrait;

    public static function getUserSignUpAll($where){
        $model = new self;
        if($where['id']!='' || $where['id']!=0) $model = $model->where('activity_id',$where['id']);
        if($where['real_name']!='') $model = $model->where('order_id','like',"%$where[real_name]%");
       if($where['status']!='') $model = self::statusByWhere($where['status'],$model);
        $model = $model->where('is_del',0);
        $model = $model->order('add_time DESC');
        if (isset($where['excel']) && $where['excel'] == 1) {
            $data = ($data = $model->select()) && count($data) ? $data->toArray() : [];
        } else {
            $data = $model->page((int)$where['page'],(int)$where['limit'])->select();
            $data = count($data) ? $data->toArray() : [];
        }

        foreach ($data as &$v){
            $v['addTime']=date('Y-m-d H:i:s',$v['add_time']);
            $v['is_fill']=EventRegistration::where('id',$v['activity_id'])->value('is_fill');
            if($v['pay_type']=='weixin'){
                $v['pay_type']='微信支付';
            }elseif ($v['pay_type']=='zhifubao'){
                $v['pay_type']='支付宝支付';
            }elseif ($v['pay_type']=='yue'){
                $v['pay_type']='余额支付';
            }else{
                $v['pay_type']='其他支付';
            }
            if($v['status']){
                $v['write_off']='已核销';
            }else{
                $v['write_off']='未核销';
            }
            if($v['user_info'] && $v['is_fill']){
                $user_info=json_decode($v['user_info']);
                if($user_info->sex==1){
                    $sex='男';
                }elseif($user_info->sex==2){
                    $sex='女';
                }else{
                    $sex='保密';
                }
                $v['userInfo']=<<<HTML
                    <b >姓名：$user_info->name</b><br/>
                    <b >电话：$user_info->phone</b><br/>
                    <b >性别：$sex</b><br/>
                    <b >年龄：$user_info->age</b><br/>
                    <b >公司：$user_info->company</b><br/>
                    <b >备注：$user_info->remarks</b><br/>
HTML;
            }else{
                $v['userInfo']='无';
            }
        }
        if (isset($where['excel']) && $where['excel'] == 1) {
            self::SaveExcel($data);
        }
        $count =$model->count();
        return compact('data','count');
    }
    public static function statusByWhere($status,$model = null,$alert='')
    {
        if($model == null) $model = new self;
        if('' === $status)
            return $model->where($alert.'paid',1);
        else if($status == 1)//已支付 未核销
            return $model->where($alert.'paid',1)->where($alert.'status',0);
        else if($status == 2)//已支付 已核销
            return $model->where($alert.'paid',1)->where($alert.'status',1);
       else
            return $model->where($alert.'paid',1);
    }
    /*
       * 保存并下载excel
       * $list array
       * return
       */
    public static function SaveExcel($list)
    {
        $export = [];
        foreach ($list as $index => $item) {
            $title =EventRegistration::where('id',$item['activity_id'])->value('title');
            if($item['user_info'] && $item['is_fill']){
                $user_info=json_decode($item['user_info']);
                if($user_info->sex==1){
                    $sex='男';
                }elseif($user_info->sex==2){
                    $sex='女';
                }else{
                    $sex='保密';
                }
                $userInfo='姓名：'.$user_info->name."\n"
                    .'电话：'.$user_info->phone."\n"
                    .'性别：'.$sex."\n"
                    .'年龄：'.$user_info->age."\n"
                    .'公司：'.$user_info->company."\n"
                    .'备注：'.$user_info->remarks;
            }else{
                $userInfo='无';
            }
            $export[] = [
                $item['order_id'],
                $title,
                $userInfo,
                $item['pay_type'],
                $item['pay_price'],
                $item['status'] == 1 ? '已核销' : '未核销',
                date('Y/md H:i', $item['add_time'])
            ];
        }
        PHPExcelService::setExcelHeader(['订单号','活动标题','报名信息','支付方式','支付金额', '状态', '报名时间'])
            ->setExcelTile('报名导出', '报名信息' . time(), ' 生成时间：' . date('Y-m-d H:i:s', time()))
            ->setExcelContent($export)
            ->ExcelSave();
    }

}