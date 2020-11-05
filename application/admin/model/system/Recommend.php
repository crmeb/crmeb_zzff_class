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

namespace app\admin\model\system;


use app\admin\model\special\Grade;
use app\admin\model\special\SpecialSubject;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class SystemAdmin
 * @package app\admin\model\system
 */
class Recommend extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    public static function setAddTimeAttr($value)
    {
        return time();
    }

    public static function getTypeseTingAttr($value, $data)
    {
        $name = '';
        switch ($data['typesetting']) {
            case 1:
                $name = '大图';
                break;
            case 2:
                $name = '宫图';
                break;
            case 3:
                $name = '小图';
                break;
            case 4:
                $name = '左右切换';
                break;
            default:
                $name = '其他';
                break;
        }
        return $name;
    }

    public static function getTypeNameAttr($value, $data)
    {
        $name = '';
        switch ($data['type']) {
            case 1:
                $name = '图文';
                break;
            case 0:
                $name = '专题';
                break;
            case 2:
                $name = '直播';
                break;
            case 3:
                $name = '自定义';
                break;
        }
        return $name;
    }

    public static function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public static function getIconKeyAttr($value, $data)
    {
        if (!$data['icon']) return '';
        $value = parse_url($data['icon']);
        $value = isset($value['path']) ? substr($value['path'], 1) : '';
        return $value;
    }

    public static function getImageKeyAttr($value, $data)
    {
        if (!$data['image']) return '';
        $value = parse_url($data['image']);
        $value = isset($value['path']) ? substr($value['path'], 1) : '';
        return $value;
    }

    public static function fixedList()
    {
        $list = self::where('is_fixed', 1)->order('sort desc,add_time desc')->select();
        foreach ($list as &$item) {
            $item['number'] = RecommendRelation::where(['recommend_id' => $item['id']])->count();
        }
        return $list;
    }

    public static function getRecommendList($where)
    {
        $model = self::where('is_fixed', $where['is_fixed']);
        if ($where['order']) {
            $model->order(self::setOrder($where['order']));
        } else $model->order('sort desc,add_time desc');
        $data = $model->page((int)$where['page'], (int)$where['limit'])->select();
        foreach ($data as $item) {
            $item['type_name'] = self::getTypeNameAttr('', $item);
            $item['type_ting'] = self::getTypeseTingAttr('', $item);
            $item['number'] = RecommendRelation::where(['recommend_id' => $item['id']])->count();
            $item['grade_title'] = Grade::where(['id' => $item['grade_id']])->value('name');
        }
        $count = self::where('is_fixed', $where['is_fixed'])->count();
        return compact('data', 'count');
    }
}