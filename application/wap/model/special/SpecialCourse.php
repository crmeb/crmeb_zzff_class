<?php

namespace app\wap\model\special;

use app\wap\model\user\User;
use basic\ModelBasic;
use traits\ModelTrait;

class SpecialCourse extends ModelBasic
{
    use ModelTrait;

    public static function getCouresCount($special_id)
    {
        $tasjIds = self::where(['is_show' => 1, 'special_id' => $special_id])->column('id');
        $ids = [];
        foreach ($tasjIds as $id) {
            if (SpecialTask::getTashCount($id)) $ids[] = $id;
        }
        return $ids;
    }

    public static function getSpecialCouresList($special_id, $limit = 10, $page = 1)
    {
        $list = SpecialTask::where('special_id', $special_id)->order('sort desc,add_time desc')->page($page, $limit)->select();
        $page++;
        return compact('page', 'list');
    }

    public static function getSpecialSourceList($special_id, $limit = 10, $page = 1,$uid=0)
    {
        $special = Special::get($special_id);
        if (!$special) return compact('page', 'list');
        //获得专栏下面的专题
        $cloumnSource = SpecialSource::getSpecialSource($special_id, false, $limit, $page);
        $list = array();
        if (!$cloumnSource) return compact('page', 'list');
        foreach ($cloumnSource as $k => $v) {
            if ($special['type'] == SPECIAL_COLUMN) {
                $cloumnTask = Special::get($v['source_id']);
                $cloumnTask = $cloumnTask ? $cloumnTask->toArray() : [];
                //获得专题下面的素材
                $specialTask = array();
                $specialSource = SpecialSource::getSpecialSource($v['source_id']);
                if(count($specialSource) > 0){
                    foreach ($specialSource as $sk => $sv) {
                        $task = SpecialTask::where('is_show',1)->where('id',$sv['source_id'])->find();
                        if ($task ? $task = $task->toArray() : false){
                            $task['special_id'] = $sv['special_id'];
                            $task['pay_status'] = $sv['pay_status'];
                            $taskSpecialIsPay = self::specialIsPay($special_id,$uid);
                            if (!$taskSpecialIsPay){//如果整个专题免费，那么里面素材都免非，否则就默认素材本身的状态
                                $task['pay_status'] = $taskSpecialIsPay;
                            }
                            $specialTask[] = $task;
                        }

                    }
                }
                array_multisort(array_column($specialTask,'sort'),SORT_DESC,$specialTask);
                if ($cloumnTask) {
                    $cloumnTask['special_task'] = $specialTask;
                    $cloumnTask['pay_status'] = $v['pay_status'];//付费,先默认素材本身的付费状态
                    $specialIsPay = self::specialIsPay($v['source_id'],$uid);
                    if (!$specialIsPay){//如果整个专题免费，那么里面素材都免非，否则就默认素材本身的状态
                        $cloumnTask['pay_status'] = $specialIsPay;
                    }
                    $cloumnTask['pay_status'] = $specialIsPay;
                    $cloumnTask['cloumn_special_id'] = $special_id;
                    if ($cloumnTask['is_show'] == 1) {
                        $list[] = $cloumnTask;
                    }
                }

            } else {
                $task = SpecialTask::getSpecialTaskOne($v['source_id']);
                if ($task ? $task =  $task->toArray() : false) {
                $task['pay_status'] = $v['pay_status'];//付费
                $specialIsPay = self::specialIsPay($special_id,$uid);
                if (!$specialIsPay){//如果整个专题免费，那么里面素材都免非，否则就默认素材本身的状态
                    $task['pay_status'] = $specialIsPay;
                }
                $task['special_id'] = $special_id;
                if ($task['is_show'] == 1 && $special['is_show'] == 1) {
                    $list[] = $task;
                }
                }
            }
        }
       array_multisort(array_column($list,'sort'),SORT_DESC,$list);
        $page++;
        return compact('page', 'list');
    }

    public static function get_cloumn_task($special_id, $source_id = false, $limit = 10, $page = 1,$uid=0)
    {
        $special = Special::get($special_id);
        if (!$special) return [];
        $cloumn_source = SpecialSource::getSpecialSource($special_id, $source_id);
        if (!$cloumn_source) return [];
        $cloumn_source = array_column($cloumn_source->toArray(), 'source_id');
        $special_source = SpecialSource::getSpecialSource($cloumn_source, $source_id, $limit, $page);
        $list = array();
        if (!$special_source) return compact('page', 'list');
        foreach ($special_source as $k => $v) {
            $task = SpecialTask::getSpecialTaskOne($v['source_id']);
            $task_special = Special::get($v['special_id']);
            $specialIsPay = self::specialIsPay($v['special_id'],$uid);
            $task['pay_status'] = $specialIsPay;
            $task['special_id'] = $v['special_id'];
            if ($task['is_show'] == 1 && $task_special['is_show'] == 1) {
                $list[] = $task;
            }
        }
        $page++;
        return compact('page', 'list');
    }

    /**专题是否需要付费
     * @param $special_id
     * @return int
     * @throws \think\exception\DbException
     */
    public static function specialIsPay($special_id,$uid)
    {
        if (!$special_id) return false;
        $special = Special::get($special_id);
        if (!$special) return false;
        $specialIsPay = 1;//收费
        if(!$uid) return $specialIsPay;
        $uid = User::getActiveUid();
        $isMember = User::getUserInfo($uid);
        $isPay = SpecialBuy::PaySpecial($special['id'], $uid);
        if ($special['pay_type'] == 0) {//专栏里面整个专题免费
            $specialIsPay = 0;//免费
        }
        if ($isPay === false && $special['pay_type'] == 0 && $special['is_pink'] == 0) {//没有购买，
            $specialIsPay = 0;//免费
        }
        if ($isMember['level'] > 0 && $special['member_pay_type'] == 0) {//会员，
            $specialIsPay = 0;//免费
        }
        if ($isPay) {//购买过，
            $specialIsPay = 0;//免费
        }
        return $specialIsPay;
    }

}