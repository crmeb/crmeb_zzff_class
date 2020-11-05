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

namespace service;


use app\admin\model\user\MemberCard;
use app\admin\model\special\Special;
use app\admin\model\special\SpecialSource;
use app\admin\model\special\SpecialTask;
use app\admin\model\user\MemberCardBatch;
use think\Db;
use think\Request;
use service\JsonService;

class ModeService
{
    /**根据标识选着模型对象
     * @param $model_type 表名
     * @return Special|SpecialTask|bool
     */
    public static function switch_model($model_type)
    {
        if (!$model_type) {
            return false;
        }
        switch ($model_type) {
            case 'task':
                return new SpecialTask();
                break;
            case 'special':
                return new Special();
                break;
            case 'source':
                return new SpecialSource();
                break;
            case 'member_card_batch':
                return new MemberCardBatch();
                break;
            case 'member_card':
                return new MemberCard();
                break;
            default:
                return false;
        }
    }

}