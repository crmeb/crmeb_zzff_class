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


namespace app\index\controller;

use think\Exception;
use think\exception\ErrorException;
use think\Queue;

class PushJob
{
    /**
     * 一个使用了队列的 action
     */
    public static function actionWithDoPinkJob(array $data){
        try{
            // 1.当前任务将由哪个类来负责处理。
            //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
            $jobHandlerClassName  = 'app\index\job\PullDoPink';
            // 2.当前任务归属的队列名称，如果为新队列，会自动创建
            $jobQueueName  	  = "doPinkJobQueue";
            // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
            //   ( jobData 为对象时，存储其public属性的键值对 )
            $jobData   = [ 'pinkInfo' => $data, 'time' => date('Y-m-d H:i:s')] ;
            if (!isset($data['pink_time']) || !$data['pink_time']) return true;
            $timewait = $data['pink_time'] + 300;
            //$jobData   = [ 'pinkInfo' => 'hahah', 'time' => date('Y-m-d H:i:s'), 'b' => 21] ;
            //$timewait = 20;
            // 4.将该任务推送到消息队列，等待对应的消费者去执行
            $isPushed = Queue::later($timewait, $jobHandlerClassName , $jobData , $jobQueueName );
            //$isPushed = Queue::push($jobHandlerClassName , $jobData , $jobQueueName );
            // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
            if( $isPushed !== false ){
                return 1;
            }else{
                return 1;
            }
        }catch (ErrorException $e){
            echo $e->getMessage();
        }

    }
}
