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

namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;


class SignPoster extends ModelBasic
{
    use ModelTrait;


    public static function getSignPosterList($where){
        $data=self::page((int)$where['page'],(int)$where['limit'])->order('sort DESC,add_time DESC')->select();
        count($data) && $data=$data->toArray();
        foreach ($data as &$item){
            $item['sign_time']=date('Y-m-d',$item['sign_time']);
        }
        $count=self::count();
        return compact('data','count');
    }
}