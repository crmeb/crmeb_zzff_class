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
namespace app\wap\model\special;

use app\admin\model\special\SpecialSource;
use app\wap\model\store\StoreOrder;
use app\wap\model\store\StorePink;
use app\wap\model\user\User;
use basic\ModelBasic;
use service\SystemConfigService;
use think\Url;
use traits\ModelTrait;

class Special extends ModelBasic
{
    use ModelTrait;

    public function profile()
    {
        return $this->hasOne('SpecialContent', 'special_id', 'id')->field('content');
    }

    //动态赋值
    public static function getPinkStrarTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public static function getPinkEndTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public static function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public static function getBannerAttr($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public static function getLabelAttr($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    /**
     * 设置专题显示条件
     * @param string $alias 别名
     * @param null $model model
     * @param bool $isAL 是否起别名,默认执行
     * @return $this
     */
    public static function PreWhere($alias = '', $model = null, $isAL = false)
    {
        self::setPinkSpecial();
        if (is_null($model)) $model = new self();
        if ($alias) {
            $isAL || $model = $model->alias($alias);
            $alias .= '.';
        }
        return $model->where(["{$alias}is_del" => 0]);
    }

    /**
     * 获取拼团详情页的专题详情和分享连接
     * @param string $order_id 订单id
     * @param int $pinkId 当前拼团id
     * @param int $uid 当前用户id
     * @return array
     * */
    public static function getPinkSpecialInfo($order_id, $pinkId, $uid)
    {
        $special = self::PreWhere()->where('id', StoreOrder::where('order_id', $order_id)->value('cart_id'))
            ->field(['image', 'title', 'abstract', 'money', 'label', 'id', 'is_pink', 'pink_money'])->find();
        if (!$special) return [];
        $special['image'] .= get_oss_process($special['image'], 4);
        $special['link'] = SystemConfigService::get('site_url') . Url::build('special/details') . '?id=' . $special['id'] . '&pinkId=' . $pinkId . '&partake=1#partake';
        $special['abstract'] = self::HtmlToMbStr($special['abstract']);
        return $special;
    }

    public static function getPinkList($pink_id)
    {

    }

    /**
     * 设置拼团到时间的专题
     * */
    public static function setPinkSpecial()
    {
        self::where('pink_strar_time', '<', time())->where('pink_end_time', '<', time())->update([
            'is_pink' => 0,
            'pink_strar_time' => 0,
            'pink_end_time' => 0
        ]);
    }

    /**
     * 获取单个专题的详细信息,拼团信息,拼团用户信息
     * @param $uid 用户id
     * @param $id 专题id
     * @param $pinkId 拼团id
     * */
    public static function getOneSpecial($uid, $id, $pinkId)
    {
        $special = self::PreWhere()->find($id);
        if (!$special) return self::setErrorInfo('您要查看的专题不存在!');
        self::update(['browse_count' => $special->browse_count + 1], ['id' => $id]);
        $title = $special->title;
        $pinkUser = StorePink::getPinkAttend($id);
        $pinkUserFase = StorePink::getPinkAttendFalse($id);
        $pinkUser = array_merge($pinkUser, $pinkUserFase);
        $pinkIngList = StorePink::getPinkAll($id, $pinkId, 3);
        foreach ($pinkIngList as &$item) {
            $item['difftime'] = [];
            $pinkAll = StorePink::getPinkMember($item['k_id'] ? $item['k_id'] : $item['id']);
            $pinkAll = StorePink::getPinkTFalseList($pinkAll, $item['k_id'] ? $item['k_id'] : $item['id'], $id);
            $pinkAllCount = count($pinkAll);
            $pinkT = $item['k_id'] ? StorePink::getPinkUserOne($item['k_id']) : $item;
            $item['num'] = (int)$pinkT['people'] - ($pinkAllCount + 1);
        }
        $special->fake_sales += StoreOrder::where(['paid' => 1, 'cart_id' => $id, 'refund_status' => 0])->count();
        $special->collect = self::getDb('special_relation')->where(['link_id' => $id, 'type' => 0, 'uid' => $uid, 'category' => 1])->count() ? true : false;
        $special->content = htmlspecialchars_decode($special->profile->content);
        $special->profile->content = '';
        $swiperlist = json_encode($special->banner);
        $special = json_encode($special->toArray());
        $pinkUser = json_encode($pinkUser);
        $pinkIngList = json_encode($pinkIngList);
        return compact('swiperlist', 'special', 'title', 'pinkUser', 'pinkIngList');
    }

    /**
     * 我的课程和我的收藏
     * @param int $type 1=收藏,0=我的购买
     * @param int $page 页码
     * @param int $limit 每页显示条数
     * @param int $uid 用户uid
     * @return array
     * */
    public static function getGradeList($type, $page, $limit, $uid, $search = '')
    {
        if ($type)
            $list = self::PreWhere('a')->where('s.uid', $uid)->order('a.sort desc')->join('__SPECIAL_RELATION__ s', 'a.id=s.link_id')->field('a.*')->page($page, $limit)->select();
        else {
            $model = self::PreWhere('s', SpecialBuy::where('s.uid', $uid)->where('s.is_del', 0)->order('a.sort desc,s.add_time desc')->alias('s'), true)->join('__SPECIAL__ a', 'a.id=s.special_id');
            if ($search) {
                $model = $model->where('a.title|a.abstract', 'like', "%$search%");
            }
            $list = $model->field('a.*,a.type as types,s.*')->page($page, $limit)->select();
        }
        $list=count($list)>0 ? $list->toArray() :[];
        foreach ($list as &$item) {
            $item['image'] = get_oss_process($item['image'], 4);
            if (is_string($item['label'])) $item['label'] = json_decode($item['label'], true);
            $item['subject_name'] = SpecialSubject::where('id', $item['subject_id'])->value('name');
            if($type) $id=$item['id'];
            else $id=$item['special_id'];
            $item['s_id'] =$id;
            $specialSourceId = SpecialSource::getSpecialSource($id);
            if($specialSourceId) $item['count']=count($specialSourceId);
            else $item['count']=0;
        }
        $page += 1;
        return compact('list', 'page');
    }

    /**
     * 获取某个专题的详细信息
     * @param int $id 专题id
     * @return array
     * */
    public static function getSpecialInfo($id)
    {
        $special = self::PreWhere()->find($id);
        if (!$special) return self::setErrorInfo('没有找到此专题');
        $special->abstract = self::HtmlToMbStr($special->abstract);
        return $special->toArray();
    }

    /**
     * 获取推广专题列表
     * @param array $where 查询条件
     * @param int $uid 用户uid
     * @return array
     * */
    public static function getSpecialSpread($where, $uid)
    {
        $is_promoter = User::where('uid', $uid)->value('is_senior');
        $store_brokerage_ratio = SystemConfigService::get('store_brokerage_ratio');
        $store_brokerage_two = SystemConfigService::get('store_brokerage_two');
        $store_brokerage_ratio = bcdiv($store_brokerage_ratio, 100, 2);
        $store_brokerage_two = bcdiv($store_brokerage_two, 100, 2);
        $ids = SpecialSubject::where('a.is_show', 1)->alias('a')->join('__SPECIAL__ s', 's.subject_id=a.id')->column('a.id');
        $subjectIds = [];
        foreach ($ids as $item) {
            if (self::PreWhere()->where('is_show', 1)->where('subject_id', $item)->count()) array_push($subjectIds, $item);
        }
        $model = SpecialSubject::where('is_show', 1)->order('sort desc')->field('id,name');
        if ($where['grade_id']) $model = $model->where('grade_id', $where['grade_id']);
        $list = $model->where('id', 'in', $subjectIds)->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($list) ? $list->toArray() : [];
        foreach ($data as &$item) {
            $item['list'] = self::PreWhere()->where('is_show', 1)->where('subject_id', $item['id'])->field([
                'image', 'id', 'title', 'money'
            ])->order('sort desc')->select();
            if (count($item['list'])) $item['list'] = $item['list']->toArray();
            foreach ($item['list'] as &$value) {
                $value['image'] = get_oss_process($value['image'], 4);
                $value['spread_money'] = bcmul($value['money'], $store_brokerage_ratio, 2);
            }
        }
        $page = (int)$where['page'] + 1;
        return compact('data', 'page');
    }

    /**
     * 设置查询条件
     * @param $where
     * @return $this
     */
    public static function setWhere($where)
    {
        if ($where['type']) {
            $model = self::PreWhere('a');
            if ($where['subject_id']) {
                $model = $model->where('a.subject_id', $where['subject_id']);
            }
            if ($where['search']) {
                $model = $model->where('a.title|a.abstract', 'LIKE', "%$where[search]%");
            }
            return $model->order('a.sort desc,a.id desc')->where('a.is_show', 1)
                ->join('special_record r', 'r.special_id = a.id')
                ->group('a.id')->where('uid', $where['uid']);
        } else {
            $model = self::PreWhere();
            if ($where['subject_id']) {
                $model = $model->where('subject_id', $where['subject_id']);
            }
            if ($where['search']) {
                $model = $model->where('title|abstract', 'LIKE', "%$where[search]%");
            }
            return $model->where('is_show', 1)->order('sort desc,id desc');
        }
    }

    /**
     * 获取专题列表
     * @param $where
     * @return mixed
     */
    public static function getSpecialList($where)
    {
        if ($where['type']) {
            $alias = 'a.';
            $field = [$alias . 'id', $alias . 'browse_count', $alias . 'image', $alias . 'title',$alias . 'type', $alias . 'money', $alias . 'pink_money', $alias . 'is_pink', $alias . 'subject_id', $alias . 'label', 'r.number'];
        } else {
            $field = ['browse_count', 'image', 'title','type', 'money', 'pink_money', 'is_pink', 'subject_id', 'label', 'id'];
        }

        $list = self::setWhere($where)
            ->field($field)
            ->page($where['page'], $where['limit'])
            ->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item) {
            $specialSourceId = SpecialSource::getSpecialSource($item['id']);
            if($specialSourceId) $item['count']=count($specialSourceId);
            else $item['count']=0;
            $item['subject_name'] = SpecialSubject::where('id', $item['subject_id'])->value('name');
        }
        return $list;
    }

}