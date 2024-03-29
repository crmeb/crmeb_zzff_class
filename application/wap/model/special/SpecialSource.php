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


use app\admin\model\live\LiveStudio;
use app\admin\model\order\StoreOrder;
use app\admin\model\system\RecommendRelation;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class Special 专题素材关联表
 * @package app\admin\model\special
 */
class SpecialSource extends ModelBasic
{
    use ModelTrait;

    /**获取专题素材
     * @param bool $special_id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSpecialSource($special_id = false, $source_id = false, $limit = false, $page = false)
    {
        $where = array();
        $data = self::where($where);
        if ($special_id) {
            if (is_array($special_id)) {
                $data->whereIn('special_id', $special_id);
            }else{
                $where['special_id'] = $special_id;
                $data->where($where);
            }

        }
        if ($source_id) {
            if (!is_array($source_id)) {
                $where['source_id'] = $source_id;
                $data->where($where);
            } else {
                $data->whereIn('source_id', $source_id);
            }
        }
        if ($page) {
            $data->page($page, !$limit ? 10 : $limit);
        }
        return $data->select();
    }

    /**更新及添加专题素材
     * @param $source_list_ids  一维数组，素材id
     * @param int $special_id 专题id
     * @return bool
     */
    public static function saveSpecialSource($source_list_ids, int $special_id)
    {
        if (!$special_id || !is_numeric($special_id)) {
            return false;
        }
        if (!$source_list_ids || !is_array($source_list_ids)) {
            return false;
        }
        try {
            $specialSourceAll = self::getSpecialSource($special_id)->toArray();

            if ($specialSourceAll) {
                self::where(['special_id' => $special_id])->delete();
            }
            $inster['special_id'] = $special_id;
            foreach ($source_list_ids as $sk => $sv) {
                $inster['source_id'] = $sv['id'];
                $inster['pay_status'] = $sv['pay_status'];
                $inster['add_time'] = time();
                self::set($inster);
            }
                return true;

        } catch (\Exception $e) {
            return false;
        }
    }


}