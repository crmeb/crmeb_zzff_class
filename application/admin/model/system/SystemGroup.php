<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/13
 */

namespace app\admin\model\system;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 数据组model
 * Class SystemGroup
 * @package app\admin\model\system
 */
class SystemGroup extends ModelBasic
{
    use ModelTrait;

    /**
     * 根据id获取当前记录中的fields值
     * @param $id
     * @return array
     */
    public static function getField($id){
        $fields = json_decode(self::where('id',$id)->value("fields"),true);
        return compact('fields');
    }

    public static function getGroupDataByType($where,$page = 1, $limit = 10){
        $data = self::where([]);
        if (isset($where['config_name']) && $where['config_name']){
            if ($where['config_name']) {
                if (is_array($where['config_name'])) {
                    $data = self::whereIn('config_name',$where['config_name']);
                }else{
                    $data = self::where('config_name',$where['config_name']);
                }
            }
        }
        $data->page((int)$page, (int)$limit);
        $list = $data->paginate();
        $total = $list->total();
        $page = $list->render();
        return compact('list', 'total', 'page');
    }
}