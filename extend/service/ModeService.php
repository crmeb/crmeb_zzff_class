<?php
/**
 *
 * @author: songtao
 * @day: 2017/11/02
 */

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