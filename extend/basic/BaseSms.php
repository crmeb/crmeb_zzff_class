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

namespace basic;

use service\AccessTokenServeService;

/**
 * Class BaseSmss
 * @package crmeb\basic
 */
abstract class BaseSms extends BaseStorage
{

    /**
     * access_token
     * @var null
     */
    protected $accessToken = NULL;


    public function __construct($name, AccessTokenServeService $accessTokenServeService,$configFile)
    {
        $this->accessToken = $accessTokenServeService;
        $this->name = $name;
        $this->configFile = $configFile;
        $this->initialize();
    }

    /**
     * @param array $config
     * @return mixed|void
     */
    protected function initialize(array $config = [])
    {
    }


    /**
     * 开通服务
     * @return mixed
     */
    abstract public function open();

    /**
     * 修改
     * @return mixed
     */
    abstract public function modify($sign);

    /**
     * 信息
     * @return mixed
     */
    abstract public function info();

    /**
     * 发送短信
     * @return mixed
     */
    abstract public function send($phone, $templateId, $data);

    /**
     * 模版
     * @return mixed
     */
    abstract public function temps($page, $limit);

    /**
     * 申请模版
     * @return mixed
     */
    abstract public function apply($title, $content, $type);

    /**
     * 申请模版记录
     * @return mixed
     */
    abstract public function applys($temp_type, $page, $limit);

    /**
     * 发送记录
     * @return mixed
     */
    abstract public function record($record_id);
}
