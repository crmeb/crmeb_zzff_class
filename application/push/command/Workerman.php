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

namespace app\push\command;

use Workerman\Worker;
use GatewayWorker\Register;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Workerman extends Command
{

    /**
     * 配置信息
     * @var array
     */
    protected $config;

    protected function configure()
    {
        $this->setName('workerman')
            ->addArgument('status', Argument::OPTIONAL, "action  start|stop|restart")
            ->addArgument('server', Argument::OPTIONAL, 'chat/channel')
            ->addOption('d', null, Option::VALUE_NONE, 'daemon（守护进程）方式启动')
            ->setDescription('workerman chat');
    }

    protected function init(Input $input, Output $output)
    {
        global $argv;
        $argv[1] = $input->getArgument('status') ?: 'start';
        $server = $input->getArgument('server');
        if ($input->hasOption('d')) {
            $argv[2] = '-d';
        } else {
            unset($argv[2]);
        }

        $this->config = \think\Config::get('workerman', []);

        return $server;
    }

    protected function execute(Input $input, Output $output)
    {
        $server = $this->init($input, $output);
        if (!$server || $server == 'chat') {
            $this->startGateWay();
        }
        if (!$server || $server == 'business') {
            $this->startBusinessWorker();
        }
        if (!$server || $server == 'channel') {
            $this->startRegister();
        }
        try {
            Worker::runAll();
        } catch (\Throwable $e) {
            $output->warning($e->getMessage());
        }
    }

    private function startBusinessWorker()
    {
        // bussinessWorker 进程
        $worker = new BusinessWorker();
        // worker名称
        $worker->name = $this->config['chat']['name'];
        // bussinessWorker进程数量
        $worker->count = $this->config['chat']['count'];
        //设置处理业务的类,此处制定Events的命名空间
        $worker->eventHandler = \app\push\controller\Events::class;
        // 服务注册地址
        $worker->registerAddress = $this->config['channel']['ip'] . ':' . $this->config['channel']['port'];
    }

    private function startGateWay()
    {
        $gateway = new Gateway($this->config['chat']['protocol'] . "://" . $this->config['chat']['ip'] . ":" . $this->config['chat']['port']);
        $gateway->name = $this->config['chat']['name'];
        // gateway进程数
        $gateway->count = $this->config['chat']['count'];
        $gateway->startPort = 2900;
        // 服务注册地址
        $gateway->registerAddress = $this->config['channel']['ip'] . ':' . $this->config['channel']['port'];
    }

    private function startRegister()
    {
        new Register('text://' . $this->config['text']['ip'] . ':' . $this->config['text']['port']);
    }
}
