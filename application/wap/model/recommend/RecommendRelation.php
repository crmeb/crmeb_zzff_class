<?php

namespace app\wap\model\recommend;

use basic\ModelBasic;
use traits\ModelTrait;

class RecommendRelation extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取主页推荐列表下的专题和图文内容
     * @param int $recommend_id 推荐id
     * @param int $type 类型 0=专题,1=图文
     * @param int $imagetype 图片显示类型
     * @param int $limit 显示多少条
     * @return array
     * */
    public static function getRelationList($recommend_id, $type, $imagetype, $limit = 0)
    {
        $limit = $limit ? $limit : 4;
        if ($type == 0)
            $list = self::where('a.recommend_id', $recommend_id)
                ->alias('a')->order('a.sort desc')->limit($limit)
                ->join("__SPECIAL__ p", 'p.id=a.link_id')
                ->join('__SPECIAL_SUBJECT__ j', 'j.id=p.subject_id', 'LEFT')
                ->where(['p.is_show' => 1, 'p.is_del' => 0])
                ->field(['p.pink_money', 'p.is_pink', 'p.title', 'p.image', 'p.abstract', 'p.label', 'p.image', 'p.money', 'p.pay_type', 'p.type as special_type','j.name as subject_name', 'a.link_id','p.browse_count'])
                ->select();
        else
            $list = self::alias('a')->join('__ARTICLE__ e', 'e.id=a.link_id')
                ->where(['a.recommend_id' => $recommend_id, 'e.is_show' => 1])
                ->field(['e.title', 'e.image_input as image', 'e.synopsis as abstract', 'e.label', 'a.link_id','e.visit as browse_count'])
                ->limit($limit)->order('a.sort desc')->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item) {
            if (!isset($item['subject_name'])) $item['subject_name'] = '';
            if (!isset($item['money'])) $item['money'] = 0;
            $item['image'] = get_oss_process($item['image'],$imagetype);
            $item['label'] = $item['label'] ? json_decode($item['label']) : [];
            $special_type_name = "";
            if (isset($item['special_type']) && SPECIAL_TYPE[$item['special_type']]) {
                $special_type_name = explode("专题",SPECIAL_TYPE[$item['special_type']]) ? explode("专题",SPECIAL_TYPE[$item['special_type']])[0] : "";
            }
            $item['special_type_name'] = $special_type_name;
        }
        return $list;
    }

    /**
     * 获取主页推荐下图文或者专题的总条数
     * @param int $recommend_id 推荐id
     * @param int $type 类型
     * @return int
     * */
    public static function getRelationCount($recommend_id, $type)
    {
        if ($type == 0)
            $count = self::where('a.recommend_id', $recommend_id)->alias('a')
                ->join("__SPECIAL__ p", 'p.id=a.link_id')
                ->join('__SPECIAL_SUBJECT__ j', 'j.id=p.subject_id', 'LEFT')
                ->where(['p.is_del' => 0])->count();
        else
            $count = self::alias('a')->join('__ARTICLE__ e', 'e.id=a.link_id')->where(['a.recommend_id' => $recommend_id, 'e.is_show' => 1])->count();
        return $count;
    }
}