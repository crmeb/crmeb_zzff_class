<?php

namespace app\wap\model\special;

use app\wap\model\live\LiveStudio;
use basic\ModelBasic;
use traits\ModelTrait;

class SpecialTask extends ModelBasic
{
    use ModelTrait;

    public static function defaultWhere()
    {
        return self::where('is_show', 1);
    }

    public static function getTashCount($course_id)
    {
        return self::where(['coures_id' => $course_id, 'is_show' => 1])->count();
    }

    public static function getTashList($course_id)
    {
        $list = self::where(['is_show' => 1, 'coures_id' => $course_id])->order('sort desc')->field('image,title,id,is_pay,play_count,live_id')->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item) {
            $item['stream_name'] = '';
            $item['studio_pwd'] = '';
            if ($item['live_id']) {
                $liveInfo = LiveStudio::where('id', $item['live_id'])->field(['stream_name', 'studio_pwd'])->find();
                if ($liveInfo) {
                    $item['stream_name'] = $liveInfo['stream_name'];
                    $item['studio_pwd'] = $liveInfo['studio_pwd'];
                }
            }
        }
        return $list;
    }

    public static function getSpecialTaskOne($task_id) {
        if (!$task_id) {
            return false;
        }
        return self::find($task_id);
    }

}