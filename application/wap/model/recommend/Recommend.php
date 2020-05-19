<?php

namespace app\wap\model\recommend;

use app\wap\model\user\User;
use basic\ModelBasic;
use traits\ModelTrait;

class Recommend extends ModelBasic
{
    use ModelTrait;

    public static function getRecommend()
    {
        return self::where(['is_fixed' => 1, 'is_show' => 1])
            ->order('sort desc,add_time desc')
            ->field(['title','icon','type','link','grade_id','id'])
            ->select();
    }

    public static function getRecommendIdAll($uid)
    {
        $model = self::where(['is_fixed' => 0, 'is_show' => 1]);
        if ($grade_id = User::where('uid', $uid)->value('grade_id')) $model = $model->where('grade_id', 'in', [$grade_id, 0]);
        $all = $model->field('id,type')->select();
        $idsAll = [];
        foreach ($all as $item) {
            if (RecommendRelation::getRelationCount($item['id'], (int)$item['type'])) array_push($idsAll, $item['id']);
        }
        return [$idsAll, $grade_id];
    }

    /**
     * 获取主页推荐列表
     *  $page 分页
     *  $limit
     * */
    public static function getContentRecommend($page, $limit, $uid)
    {
        list($idsAll, $grade_id) = self::getRecommendIdAll($uid);
        $model = self::where(['is_fixed' => 0, 'is_show' => 1])->where('id', 'in', $idsAll)
            ->field(['id', 'typesetting', 'title', 'type', 'icon', 'image', 'grade_id', 'show_count'])
            ->page($page, $limit)->order('sort desc,add_time desc');
        if ($grade_id) $model = $model->where('grade_id', $grade_id);
        $recommend = $model->select();
        $recommend = count($recommend) ? $recommend->toArray() : [];
        foreach ($recommend as &$item) {
            $item['sum_count'] = RecommendRelation::getRelationCount($item['id'], (int)$item['type']);
            $item['list'] = RecommendRelation::getRelationList($item['id'], (int)$item['type'], $item['typesetting'], $item['show_count']);
            if ($item['typesetting'] == 4) {
                list($ceilCount, $data) = self::getTypesettingList($item['list']);
                $item['data'] = $data;
                $item['ceilCount'] = $ceilCount;
            } else {
                $item['data'] = [];
                $item['ceilCount'] = 0;

            }
            $item['courseIndex'] = 1;
        }
        $page++;
        return compact('recommend', 'page');
    }

    /**
     * 获取数组
     * @param array $list
     * @return array
     */
    public static function getTypesettingList(array $list)
    {
        $ceilCount = ceil(count($list) / 3);
        $data = [];
        for ($i = 0; $i < $ceilCount; $i++) {
            $data[] = ['value' => array_slice($list, $i * 3, $i * 3 + 3)];
        }
        return [$ceilCount, $data];
    }
}