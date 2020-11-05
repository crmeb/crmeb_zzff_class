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

namespace app\admin\controller\system;

use app\admin\controller\AuthController;
use app\admin\model\special\Special;
use app\admin\model\special\SpecialSource;
use app\admin\model\special\SpecialTask;
use service\CacheService;
use service\JsonService as Json;

/**
 * 清除缓存
 * Class Clear
 * @package app\admin\controller
 *
 */
class systemClear extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }

    public function refresh_cache()
    {
        `php think optimize:schema`;
        `php think optimize:autoload`;
        `php think optimize:route`;
        `php think optimize:config`;
        return Json::successful('数据缓存刷新成功!');
    }

    public function delete_cache()
    {
        $this->delDirAndFile("./runtime/temp");
        $this->delDirAndFile("./runtime/cache");
        return Json::successful('清除缓存成功!');
    }

    public function delete_log()
    {
        $this->delDirAndFile("./runtime/log");
        return Json::successful('清除日志成功!');
    }

    function delDirAndFile($dirName, $subdir = true)
    {
        if ($handle = opendir("$dirName")) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$dirName/$item"))
                        $this->delDirAndFile("$dirName/$item", false);
                    else
                        @unlink("$dirName/$item");
                }
            }
            closedir($handle);
            if (!$subdir) @rmdir($dirName);
        }
    }

    public function data_compatible_back()
    {
        $specialList = Special::select();
        if (!$specialList) {
            return Json::successful('无需兼容!');
        }
        $isTask = SpecialSource::find();
        if ($isTask) {
            return Json::successful('无需兼容!');
        }

        try{
            foreach ($specialList as $k => $v) {
                SpecialTask::beginTrans();
                $specialTaskList = SpecialTask::where('special_id', $v['id'])->select();
                if (count($specialTaskList)) {
                    foreach ($specialTaskList as $tk => $tv) {
                        $source_inster['special_id'] = $tv['special_id'];
                        $source_inster['source_id'] = $tv['id'];
                        $source_inster['pay_status'] = PAY_MONEY;
                        $source_inster['add_time'] = time();
                        SpecialSource::set($source_inster);
                        if ($tv['live_id'] == 0){
                            $task_update['type'] = 1;
                        }else{
                            $task_update['type'] = 4;
                        }
                        SpecialTask::where(['id'=>$tv['id']])->update($task_update);
                    }
                    Special::where(['id'=>$v['id']])->update(['type' => 1]);
                }else{
                    Special::where(['id'=>$v['id']])->update(['type' => 4]);
                }

                SpecialTask::commitTrans();
            }
            return Json::successful('兼容成功!');
        }catch (\Exception $e){
            SpecialTask::rollbackTrans();
            echo $e->getMessage();die;
            return Json::fail('兼容失败!');
        }

    }
    public function data_compatible()
    {
        $specialList = Special::select();
        if (!$specialList) {
            return Json::successful('无需兼容!');
        }

        try{
            foreach ($specialList as $k => $v) {
                Special::where(['id'=>$v['id']])->update(['type' => 1]);
            }
            $specialTaskList = SpecialTask::select();
                foreach ($specialTaskList as $tk => $tv) {
                    SpecialTask::where(['id'=>$tv['id']])->update(['type' => 1]);
                }
            return Json::successful('兼容成功!');
        }catch (\Exception $e){
            return Json::fail('兼容失败!');
        }

    }
}


