<?php

namespace app\wap\model\wap;

use app\admin\model\special\SpecialSource;
use app\wap\model\recommend\RecommendRelation;
use app\wap\model\special\Special;
use app\wap\model\special\SpecialTask;
use traits\ModelTrait;
use basic\ModelBasic;
use app\wap\model\article\Article;
use think\Db;

/**
 * Class Search
 * @package app\wap\model
 */
class Search extends ModelBasic
{
    use ModelTrait;

    public static function getHostSearch()
    {
        return self::order('add_time desc')->column('name');
    }

    /**
     * 获取搜索内容
     * @param $search
     * @param int $limit
     * @param int $page
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSearchContent($search, $limit = 3,$uid=0, $page = 0)
    {
        $specialModel = Special::PreWhere()->where('title|abstract', 'LIKE', "%$search%")->field(['is_pink', 'pink_money', 'label', 'id', 'title', 'abstract', 'image', 'money'])
            ->order('sort desc');
        if ($page === 0)
            $special = $specialModel->limit($limit)->select();
        else
            $special = $specialModel->page((int)$page, (int)$limit)->select();
        $tashModel = SpecialTask::where('a.is_show', 1)->where('a.title|a.abstract', 'LIKE', "%$search%")->alias('a')->join('__SPECIAL_COURSE__ c', 'c.id=a.coures_id')
            ->field(['a.id', 'a.title', 'a.image', 'a.play_count', 'c.special_id'])->order('a.sort desc');
        if ($page === 0)
            $tash = $tashModel->limit($limit)->select();
        else
            $tash = $tashModel->page((int)$page, (int)$limit)->select();
        $special = count($special) ? $special->toArray() : [];
        $tash = count($tash) ? $tash->toArray() : [];
        foreach ($special as &$item) {
            $item['image'] = get_oss_process($item['image'],4);
        }
        foreach ($tash as &$item) {
            $item['image'] = get_oss_process($item['image'],4);
        }
        $searchList['special'] = $special;
        $searchList['tash'] = $tash;
        $data=[
            'uid'=>$uid,
            'search'=>$search,
            'add_time'=>time()
        ];
        Db::name('search_history')->insert($data);
        return $searchList;
    }

    /**
     * 获取更多搜索内容并分页
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getMoerList($where)
    {
        $moreList = self::getSearchContent($where['search'], $where['limit'], $where['page']);
        $page = $where['page'] + 1;
        return ['page' => $page, 'more_list' => $where['type'] ? $moreList['tash'] : $moreList['special']];
    }

    /**
     *
     * @param $where
     * @return array
     */
    public static function getUnifiendList($where)
    {
        $ids = RecommendRelation::where(['type' => $where['type'], 'recommend_id' => $where['recommend_id']])->column('link_id');
        switch ((int)$where['type']) {
            case 0:
            case 2:
                $model = Special::PreWhere();
                if (isset($where['subject_id']) && $where['subject_id']) $model = $model->where('subject_id', $where['subject_id']);
                $field = ['title', 'abstract', 'image','type','label', 'money', 'id', 'is_pink', 'pink_money'];
                break;
            case 1:
                $model = Article::PreWhere();
                $field = ['title', 'synopsis as abstract', 'image_input as image', 'label', 'id'];
                break;
            default:
                return ['list' => [], 'page' => 0];
                break;
        }
        $list = $model->where('id', 'in', $ids)->order('sort desc')->field($field)->page((int)$where['page'], (int)$where['limit'])->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item) {
            if (!isset($item['money'])) $item['money'] = 0;
            $item['money'] = (float)$item['money'];
            $item['image'] = get_oss_process($item['image'],$where['typesetting']);
            $item['count'] =0;
            if($where['type']==0 || $where['type']==2){
                $specialSourceId = SpecialSource::getSpecialSource($item['id']);
                if($specialSourceId) $item['count']=count($specialSourceId);
                else $item['count'] =0;
            }
        }
        $page = $where['page'] + 1;
        return compact('list', 'page');
    }

    /**
     * 用户搜索历史
     */
    public static function userSearchHistory($uid=0){
        $list=Db::name('search_history')->where('uid',$uid)->limit(0,10)->order('add_time DESC')->select();
        return $list;
    }
}