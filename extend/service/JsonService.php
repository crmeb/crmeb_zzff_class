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


class JsonService
{
    private static $SUCCESSFUL_DEFAULT_MSG = 'ok';

    private static $FAIL_DEFAULT_MSG = 'no';

    public static function result($code,$msg='',$data=[],$count=0)
    {
        exit(json_encode(compact('code','msg','data','count')));
    }
    public static function successlayui($count=0,$data=[],$msg='')
    {
        if(is_array($count)){
            if(isset($count['data'])) $data=$count['data'];
            if(isset($count['count'])) $count=$count['count'];
        }
        if(false == is_string($msg)){
            $data = $msg;
            $msg = self::$SUCCESSFUL_DEFAULT_MSG;
        }
        return self::result(0,$msg,$data,$count);
    }
    public static function successful($msg = 'ok',$data=[],$status=200)
    {
        if(false == is_string($msg)){
            $data = $msg;
            $msg = self::$SUCCESSFUL_DEFAULT_MSG;
        }
        return self::result($status,$msg,$data);
    }

    public static function status($status,$msg,$result = [])
    {
        $status = strtoupper($status);
        if(true == is_array($msg)){
            $result = $msg;
            $msg = self::$SUCCESSFUL_DEFAULT_MSG;
        }
        return self::result(200,$msg,compact('status','result'));
    }

    public static function fail($msg,$data=[],$code = false)
    {
        if(true == is_array($msg)){
            $data = $msg;
            $msg = self::$FAIL_DEFAULT_MSG;
        }
        return self::result($code ? $code : 400,$msg,$data);
    }

}