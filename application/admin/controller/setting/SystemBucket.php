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


namespace app\admin\controller\setting;

use service\JsonService as Json;
use think\Request;
use think\Url;
use app\admin\controller\AuthController;
use service\AlipayDisposeService;
use app\admin\model\system\SystemBucket as SystemBucketModel;
use service\SystemConfigService;
/**
 * Class SystemBucket
 * @package app\admin\controller\setting
 */
class SystemBucket extends AuthController
{

    protected static $endpoint =[
        '华东1(杭州)'=>'oss-cn-hangzhou.aliyuncs.com',
        '华东2(上海)'=>'oss-cn-shanghai.aliyuncs.com',
        '华北1(青岛)'=>'oss-cn-qingdao.aliyuncs.com',
        '华北2(北京)'=>'oss-cn-beijing.aliyuncs.com',
        '华北3(张家口)'=>'oss-cn-zhangjiakou.aliyuncs.com',
        '华北5(呼和浩特)'=>'oss-cn-huhehaote.aliyuncs.com',
        '华北6(乌兰察布)'=>'oss-cn-wulanchabu.aliyuncs.com',
        '华南1(深圳)'=>'oss-cn-shenzhen.aliyuncs.com',
        '华南2(河源)'=>'oss-cn-heyuan.aliyuncs.com',
        '华南3(广州)'=>'oss-cn-guangzhou.aliyuncs.com',
        '西南1(成都)'=>'oss-cn-chengdu.aliyuncs.com',
        '中国(香港)'=>'oss-cn-hongkong.aliyuncs.com',
        '新加坡'=>'oss-ap-southeast-1.aliyuncs.com',
        '澳大利亚(悉尼)'=>'oss-ap-southeast-2.aliyuncs.com',
        '马来西亚(吉隆坡)'=>'oss-ap-southeast-3.aliyuncs.com',
        '印度尼西亚(雅加达)'=>'oss-ap-southeast-5.aliyuncs.com',
        '日本(东京)'=>'oss-ap-northeast-1.aliyuncs.com',
        '印度(孟买)'=>'oss-ap-south-1.aliyuncs.com',
        '德国(法兰克福)'=>'oss-eu-central-1.aliyuncs.com',
        '英国(伦敦)'=>'oss-eu-west-1.aliyuncs.com',
        '美国(硅谷)'=>'oss-us-west-1.aliyuncs.com',
        '美国(佛吉尼亚)'=>'oss-us-east-1.aliyuncs.com',
        '阿联酋(迪拜)'=>'oss-me-east-1.aliyuncs.com',
    ];
    /**
     * 对象存储OSS配置页面
     * @return \think\Response
     */
    public function index()
    {
        $endpoint=self::$endpoint;
        $where = parent::getMore([
            ['endpoint', ''],
            ['types', 1],
        ], $this->request);
        $this->assign(['list'=>SystemBucketModel::bucKetList($where),'endpoint'=>$endpoint,'where'=>$where]);
        return $this->fetch();
    }

    /**
     * 拉取储存空间
     */
    public function pullBucket()
    {
        $endpoint=self::$endpoint;
        foreach ($endpoint as $key=>$value){
            $list=AlipayDisposeService::ossBucketList($value);
            if(count($list)>0){
                SystemBucketModel::addListBucket($list);
            }
        }
        return Json::successful('ok');
    }

    /**
     * 添加存储空间
     */
    public function create()
    {
        $endpoint=self::$endpoint;
        $this->assign(['endpoint'=>json_encode($endpoint)]);
        return $this->fetch();
    }

    /**
     * 添加存储空间
     */
    public function save()
    {
        $data = parent::postMore([
            ['bucket_name', ''],
            ['endpoint', ''],
            ['type', 1],
            ['jurisdiction', 1],
        ]);
        if (!$data['bucket_name']) return Json::fail('请输入存储空间名称');
        if(AlipayDisposeService::doesBucketExist($data['endpoint'],$data['bucket_name'])){
            return Json::fail('存储空间已存在');
        }
        $res=AlipayDisposeService::ossDispose($data['endpoint'],$data['bucket_name'],$data['jurisdiction'],$data['type']);
        if($res){
            $res1=AlipayDisposeService::putBucketCors($data['endpoint'],$data['bucket_name']);
            if($res1 && SystemBucketModel::addBucket($data)){
                return Json::successful('添加存储空间成功！');
            }else{
                return Json::fail('添加到数据库失败或设置跨域规则失败！');
            }
        }else{
            return Json::fail('添加存储空间失败！');
        }
    }

    /**
     * 删除存储空间
     */
    public function delete($id=0)
    {
        if (!$id) return Json::fail('参数错误');
        $bucket=SystemBucketModel::where('id',$id)->where('is_del',0)->find();
        if(!$bucket) return Json::fail('删除存储空间不存在');
        $res=AlipayDisposeService::deleteBucket($bucket['endpoint'],$bucket['bucket_name']);
        if($res){
            SystemBucketModel::where('id',$id)->update(['is_del'=>1]);
            return Json::successful('删除存储空间成功');
        }else{
            return Json::fail('删除存储空间失败');
        }
    }

    /**使用储存空间
     * @param int $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userUse($id=0)
    {
        if (!$id) return Json::fail('参数错误');
        $bucket=SystemBucketModel::where('id',$id)->where('is_del',0)->find();
        if(!$bucket) return Json::fail('存储空间不存在');
        $res=SystemConfigService::setOneValue('uploadUrl',$bucket['domain_name']);
        $res1=SystemConfigService::setOneValue('OssBucket',$bucket['bucket_name']);
        $res2=SystemConfigService::setOneValue('end_point',$bucket['endpoint']);
        if($res && $res1 && $res2){
            SystemBucketModel::where('is_use',1)->where('is_del',0)->update(['is_use'=>0]);
            SystemBucketModel::where('id',$id)->where('is_del',0)->update(['is_use'=>1]);
            return Json::successful('设置存储空间成功');
        }else{
            return Json::fail('设置存储空间失败');
        }
    }
    public function crossDomainRules()
    {
        $res=AlipayDisposeService::addLiveDomain('oss-cn-shanghai.aliyuncs.com','123456789ldw');
    }

}
