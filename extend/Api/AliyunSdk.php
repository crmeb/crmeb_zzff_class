<?php

namespace Api;

abstract class AliyunSdk
{

    /**
     * AccessKey
     * @var mixed|null
     */
    protected $AccessKey = null;

    /**
     * AccessKeySecret
     * @var mixed|null
     */
    protected $AccessKeySecret = null;

    /**
     * 自动加载阿里云类
     * @var array
     */
    protected $autoLoadPath = [];

    /**
     * 是否打印错误信息
     * @var bool
     */
    protected $showError = false;

    /**
     * 接口数据集合
     * @var array
     */
    protected $request = [];

    /**
     * 阿里云接口实例化结果
     * @var null
     */
    protected $client = null;

    /**
     * 动作名称
     * @var array
     */
    protected $action = [];

    /**
     * 配置信息
     * @var array
     */
    protected $config;

    /**
     * 类实例化
     * @var null
     */
    protected static $instance = null;

    /**
     * 错误信息
     * @var null
     */
    protected static $errorInfo = null;

    /**
     * 初始化程序
     * */
    protected function __construct($cofing = [])
    {
        $this->AccessKey = isset($cofing['AccessKey']) ? $cofing['AccessKey'] : null;
        $this->AccessKeySecret = isset($cofing['AccessKeySecret']) ? $cofing['AccessKeySecret'] : null;
        $this->config = $cofing;
        $this->autoLoaderClass();
        $this->_initialize();
    }

    /**
     * 自动加载类
     * */
    protected function autoLoaderClass()
    {
        if ($this->autoLoadPath) {
            require_once(ROOT_PATH . '/extend/Api/aliyun/aliyun-php-sdk-core/Config.php');
            foreach ($this->autoLoadPath as $item) {
                \Autoloader::addAutoloadPath('aliyun-php-sdk-' . $item);
            }
        }
    }

    /**
     * 实例化阿里云接口
     * */
    abstract protected function _initialize();

    /**
     * 设置错误信息
     * @param object | string $error
     * @return boolean
     * */
    protected static function setErrorInfo($error, $thsiAction = null)
    {
        $_this = self::instance();
        $request = \think\Request::instance();
        if ($error instanceof \Exception) {
            self::$errorInfo = [
                'line' => $error->getLine(),
                'msg' => $error->getMessage(),
                'code' => $error->getCode(),
                'file' => $error->getFile(),
            ];
            (!$request->isAjax() && $_this->showError) && dump([
                'msg' => $error->getMessage(),
                'code' => $error->getCode(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'action' => $thsiAction,
            ]);
        } else {
            self::$errorInfo = $error;
            (!$request->isAjax() && $_this->showError) && dump($error);
        }
        return false;
    }

    /**
     * 获取错误信息
     * @param string $error
     * @return array
     * */
    public static function getErrorInfo($error = '')
    {
        if (is_null(self::$errorInfo)) self::$errorInfo = $error;
        $errorInfo = self::$errorInfo;
        self::$errorInfo = null;
        if (!is_array($errorInfo)) {
            return ['msg' => $errorInfo];
        }
        return $errorInfo;
    }

    /**
     * 初始化
     * @param array $cofing
     * @return $this
     */
    public static function instance($cofing = [])
    {
        if (is_null(self::$instance)) self::$instance = new static($cofing);
        return self::$instance;
    }

    /**
     * 设置时间格式
     * */
    public static function setTimeFormat($time = '')
    {
        $time = $time ? $time : time();
        if (is_string($time)) {
            if ((int)$time == 0) {
                $data = date("Y-m-d\\TH:i:s\\Z", strtotime($time));
            } else {
                $data = date("Y-m-d\\TH:i:s\\Z", $time);
            }
        } else {
            $data = date("Y-m-d\\TH:i:s\\Z", $time);
        }
        return $data;
    }

    /**
     * 是否打印错误信息
     * @param boolean $showError
     * @return $this
     * */
    public function setShowError($showError)
    {
        $this->showError = $showError;
        return $this;
    }

    /**
     * 设置$AccessKey
     * @param string $AccessKey
     * @return $this
     * */
    public function setAccessKey($AccessKey)
    {
        $this->AccessKey = $AccessKey;
        return $this;
    }

    /**
     * 设置$AccessKeySecret
     * @param string $AccessKeySecret
     * @return $this
     * */
    public function setAccessKeySecret($AccessKeySecret)
    {
        $this->AccessKeySecret = $AccessKeySecret;
        return $this;
    }

}