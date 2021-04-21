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


namespace app\admin\model\system;

use traits\ModelTrait;
use basic\ModelBasic;
use service\AlipayDisposeService;

/**
 * Class SystemBroadcast
 * @package app\admin\model\system
 */
class SystemBroadcast extends ModelBasic
{
    use ModelTrait;

    /**
     * 直播域名列表
     */
    public static function broadcastList()
    {
        $list=self::where('is_del',0)->order('id desc')->select();
        $list=count($list)>0 ? $list->toArray() : [];
        $array=[];
        foreach ($list as $key=>$value){
            if($value['domain_status']=='configuring'){
                $res=AlipayDisposeService::describeLiveDomainDetails($value['domain_name'],$value['region']);
                if(is_array($res) && array_key_exists('DomainDetail',$res)){
                    self::where('is_del', 0)->where('id', $value['id'])->update(['cname' => $res['DomainDetail']['Cname'], 'domain_status' => $res['DomainDetail']['DomainStatus']]);
                  array_push($array,$value);
                }else{
                    continue;
                }
            }else{
                array_push($array,$value);
            }
        }
        return $array;
    }
    /**获取域名主KEY
     * @param string $domainName
     * @param string $regionId
     * @return mixed|null
     */
    public static function auth_key($domainName='',$regionId='')
    {
        $data=AlipayDisposeService::describeLiveDomainConfigs($domainName,$regionId);
        if(is_array($data) && array_key_exists('DomainConfigs',$data)){
            $FunctionArg=$data['DomainConfigs']['DomainConfig'][0]['FunctionArgs']['FunctionArg'];
            $auth_key=null;
            foreach ($FunctionArg as $k=>$vc){
                if($vc['ArgName']=='auth_key1'){
                    $auth_key=$vc['ArgValue'];
                }
            }
            return $auth_key;
        }else{
            return false;
        }
    }
    /**
     * 保存存储空间
     *  @param $list
     */
    public static function addBroadcast($data,$res1,$auth_key)
    {
        $data['add_time']=time();
        $data['auth_key1']=$auth_key;
        $data['cname']=$res1['DomainDetail']['Cname'];
        $data['domain_status']=$res1['DomainDetail']['DomainStatus'];
        $res=self::set($data);
        return $res;
    }
}