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

namespace app\admin\model\wechat;

use app\admin\model\wechat\StoreService as ServiceModel;
use app\wap\model\user\User;
use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 客服管理 model
 * Class StoreProduct
 * @package app\admin\model\store
 */
class StoreServiceLog extends ModelBasic
{
    use ModelTrait;
    /**
     * @return array
     */
    public static function getChatList($uid,$to_uid,$mer_id){
        $model = new self;
        $where = "mer_id = ".$mer_id." AND ((uid = ".$uid." AND to_uid = ".$to_uid.") OR (uid = ".$to_uid." AND to_uid = ".$uid."))";
        $model->where($where);
        $model->order("add_time desc");
        return self::page($model,function($item,$key) use ($mer_id){
            $user = StoreService::field("nickname,avatar")->where('mer_id',$mer_id)->where(array("uid"=>$item["uid"]))->find();
            if(!$user)$user = User::field("nickname,avatar")->where(array("uid"=>$item["uid"]))->find();
            $item["nickname"] = $user["nickname"];
            $item["avatar"] = $user["avatar"];
        });
    }
}