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