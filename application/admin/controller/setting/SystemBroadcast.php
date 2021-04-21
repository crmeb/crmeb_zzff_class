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
use service\FormBuilder as Form;
use think\Request;
use think\Url;
use app\admin\controller\AuthController;
use service\AlipayDisposeService;
use app\admin\model\system\SystemBucket as SystemBucketModel;
use app\admin\model\system\SystemBroadcast as SystemBroadcastModel;
use service\SystemConfigService;
/**
 * Class SystemBroadcast
 * @package app\admin\controller\setting
 */
class SystemBroadcast extends AuthController
{

    protected static $endpoint =[
        '华北2(北京)'=>'cn-beijing',
        '华东2(上海)'=>'cn-shanghai',
        '华南1(深圳)'=>'cn-shenzhen',
        '华北1(青岛)'=>'cn-qingdao',
        '亚太东南1(新加坡)'=>'ap-southeast-1',
        '德国'=>'eu-central-1',
        '亚太东北1(东京)'=>'ap-northeast-1',
        '印度(孟买)'=>'ap-south-1',
        '印度尼西亚(雅加达)'=>'ap-southeast-5',
    ];
    /**
     * 视频直播配置页面
     * @return \think\Response
     */
    public function index()
    {
        $this->assign(['list'=>SystemBroadcastModel::broadcastList()]);
        return $this->fetch();
    }


    /**
     * 添加视频直播域名
     */
    public function create()
    {
        $endpoint=self::$endpoint;
        $this->assign(['endpoint'=>json_encode($endpoint)]);
        return $this->fetch();
    }

    /**
     *播流域名添加推流域名
     */
    public function addStreaming($id=0)
    {
        if(!$id) return $this->failed('参数有误');
        $domain=SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveVideo')->where('id',$id)->find();
        if(!$domain) return $this->failed('域名不存在或不是播流域名');
        $region=$domain['region'];
        $list = SystemBroadcastModel::where('is_del',0)->where('region',$region)->where('live_domain_type','liveEdge')->select();
        if(count($list)<1) return $this->failed('请先添加推流域名');
        $form = Form::create(Url::build('streamingSave',['id'=>$id]), [
            Form::select('push_flow', '添加推流域名')->setOptions(function () use($list) {
                $menus=[];
                foreach ($list as $menu) {
                    $menus[] = ['value' => $menu['domain_name'], 'label' => $menu['domain_name']];
                }
                return $menus;
            })->filterable(1),
        ]);
        $form->setMethod('post')->setTitle('播流域名配置')->setSuccessScript('parent.layer.close(parent.layer.getFrameIndex(window.name));parent.$(".J_iframe:visible")[0].contentWindow.location.reload();');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存数据
     */
    public function streamingSave($id=0)
    {
        $data = parent::postMore([
            ['push_flow', ''],
        ]);
        if (!$data['push_flow']) return Json::fail('请选择推流域名');
        if(!$id) return Json::fail('参数有误');
        $pushFlow=SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveEdge')->where('domain_name',$data['push_flow'])->find();
        if(!$pushFlow) return Json::fail('推流域名不存在或不是推流域名');
        $domain=SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveVideo')->where('id',$id)->find();
        if(!$domain) return Json::fail('域名不存在或不是播流域名');
        $describe=AlipayDisposeService::describeLiveStreamsNotifyUrlConfig($pushFlow['domain_name'],$pushFlow['region']);
        if(!$describe['LiveStreamsNotifyConfig']['NotifyUrl']){
            $res2=AlipayDisposeService::setLiveStreamsNotifyUrlConfigs($pushFlow['domain_name'],$pushFlow['region']);
            if(!$res2) return Json::fail('请设置推流域名回调地址！');
        }
        if(SystemBroadcastModel::be(['is_del'=>0,'live_domain_type'=>'liveVideo','push_domain'=>$data['push_flow']])) return Json::fail('该推流域名已绑定！');
        $res=AlipayDisposeService::addLiveDomainMappings($domain['domain_name'],$data['push_flow'],$domain['region']);
        if($res){
            SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveVideo')->where('id',$id)->update(['push_domain'=>$data['push_flow']]);
            return Json::successful('添加成功！');
        }else{
            return Json::fail('添加失败！');
        }
    }

    /**
     * 删除推流域名
     */
    public function delStreaming($id=0)
    {
        if(!$id) return Json::fail('参数有误');
        $domain=SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveVideo')->where('id',$id)->find();
        if(!$domain) return Json::fail('域名不存在或不是播流域名');
        $res=AlipayDisposeService::deleteLiveDomainMappings($domain['domain_name'],$domain['push_domain'],$domain['region']);
        if($res){
            SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveVideo')->where('id',$id)->update(['push_domain'=>'']);
            return Json::successful('删除成功！');
        }else{
            return Json::fail('删除失败！');
        }
    }
    /**
     *播流域名的录制配置
     */
    public function toConfigure($id=0)
    {
        if(!$id) return $this->failed('参数有误');
        $domain=SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveVideo')->where('id',$id)->find();
        $region='oss-'.$domain['region'].'.aliyuncs.com';
        $list = SystemBucketModel::where(['is_del'=>0,'is_use'=>0])->where('endpoint',$region)->select();
        if(count($list)<1) return $this->failed('请先添加对象存储oss');
        $form = Form::create(Url::build('toConfigureSave',['id'=>$id]), [
            Form::radio('format', '存储格式','m3u8')->options([['label' => 'm3u8', 'value' => 'm3u8'], ['label' => 'flv', 'value' => 'flv'], ['label' => 'mp4', 'value' => 'mp4']]),
            Form::number('duration', '录制周期(分)',30),
            Form::select('OssBucketId', 'OssBucket名称')->setOptions(function () use($list) {
                $menus=[];
                foreach ($list as $menu) {
                    $menus[] = ['value' => $menu['id'], 'label' => $menu['bucket_name']];
                }
                return $menus;
            })->filterable(1),
        ]);
        $form->setMethod('post')->setTitle('录制配置')->setSuccessScript('parent.layer.close(parent.layer.getFrameIndex(window.name));parent.$(".J_iframe:visible")[0].contentWindow.location.reload();');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存
     */
    public function toConfigureSave($id=0)
    {
        $data = parent::postMore([
            ['format', 'm3u8'],
            ['duration', 30],
            ['OssBucketId', 0],
        ]);
        if (!$data['OssBucketId']) return Json::fail('请选择oss桶');
        if (!$data['duration']) return Json::fail('请输入录制周期(分)');
        if(!$id) return Json::fail('参数有误');
        $domain=SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveVideo')->where('id',$id)->find();
        if(!$domain)return Json::fail('播流域名不存在');
        $bucket=SystemBucketModel::where('is_del',0)->where('id',$data['OssBucketId'])->find();
        if(!$bucket)return Json::fail('储存空间不存在');
        $res=AlipayDisposeService::addLiveAppRecordConfigs($domain['domain_name'],$domain['region'],'*','*',$bucket['bucket_name'],$bucket['endpoint'],$data['format'],$data['duration']);
        if($res){
            SystemBucketModel::where('is_del',0)->where('id',$data['OssBucketId'])->update(['is_use'=>2]);
            SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveVideo')->where('id',$id)->update(['bucket_name'=>$bucket['bucket_name']]);
            return Json::successful('录制配置设置成功！');
        }else{
            return Json::fail('录制配置设置失败！');
        }
    }

    /**
     * 删除录制配置
     */
    public function delLiveAppRecordConfig($id=0)
    {
        if(!$id) return Json::fail('参数有误!');
        $domain=SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveVideo')->where('id',$id)->find();
        if(!$domain) return Json::fail('域名不存在!');
        $res=AlipayDisposeService::deleteLiveAppRecordConfigs($domain['domain_name'],$domain['region'],'*','*');
        if($res){
            SystemBucketModel::where('is_del',0)->where('bucket_name',$domain['bucket_name'])->update(['is_use'=>0]);
            SystemBroadcastModel::where('is_del',0)->where('live_domain_type','liveVideo')->where('id',$id)->update(['bucket_name'=>'']);
            return Json::successful('录制配置设置成功！');
        }else{
            return Json::fail('录制配置设置失败！');
        }
    }
    /**
     * 保存视频直播域名
     */
    public function save()
    {
        $data = parent::postMore([
            ['domain_name', ''],
            ['region', ''],
            ['live_domain_type', 'liveVideo'],
            ['scope', 'domestic'],
        ]);
        if (!$data['domain_name']) return Json::fail('请输入直播域名');
        if(mb_substr($data['domain_name'],0,1,'utf-8')=='*') return Json::fail('暂不支持添加泛域名');
        if(SystemBroadcastModel::be(['domain_name'=>$data['domain_name'],'is_del'=>0])) return Json::fail('域名已存在！');
        $res=AlipayDisposeService::addLiveDomain($data);
        if($res){
            $res1=AlipayDisposeService::describeLiveDomainDetails($data['domain_name'],$data['region']);
            $res2=true;
            if($data['live_domain_type']=='liveEdge'){
                $res2=AlipayDisposeService::setLiveStreamsNotifyUrlConfigs($data['domain_name'],$data['region']);
            }
            $auth_key=SystemBroadcastModel::auth_key($data['domain_name'],$data['region']);
            $res3=true;
            if($data['live_domain_type']=='liveVideo'){
                $res3=AlipayDisposeService::batchSetLiveDomainConfigs($data['domain_name'],$data['region']);
            }
            if($res1 && $res2 && $res3 && $auth_key && SystemBroadcastModel::addBroadcast($data,$res1,$auth_key)){
                return Json::successful('添加直播域名成功！');
            }else{
                return Json::fail('添加到数据库失败/请确认域名是否和视频直播在同一个阿里云账户下！');
            }
        }else{
            return Json::fail('添加直播域名失败！');
        }
    }
    /**
     * 删除推流回调地址
     */
    public function delNotifUrl($id=0)
    {
        if(!$id) return Json::fail('参数有误!');
        $broadcast=SystemBroadcastModel::where('id',$id)->where('is_del',0)->find();
        if (!$broadcast) return Json::fail('直播域名不存在!');
        $res=AlipayDisposeService::deleteLiveStreamsNotifyUrlConfigs($broadcast['domain_name'],$broadcast['region']);
        if($res){
            return Json::successful('删除推流回调地址成功!');
        }else{
            return Json::fail('删除推流回调地址失败!');
        }
    }
    /**
     * 删除视频直播域名
     */
    public function delete($id=0)
    {
        if (!$id) return Json::fail('参数错误');
        $broadcast=SystemBroadcastModel::where('id',$id)->where('is_del',0)->find();
        if(!$broadcast) return Json::fail('删除直播域名不存在');
        $res=AlipayDisposeService::deleteLiveDomains($broadcast['domain_name'],$broadcast['region']);
        if($res){
            SystemBroadcastModel::where('id',$id)->update(['is_del'=>1]);
            return Json::successful('删除视频直播域名成功');
        }else{
            return Json::fail('删除视频直播域名失败');
        }
    }

    /**停用域名
     * @param int $id
     */
    public function offlines($id=0)
    {
        if (!$id) return Json::fail('参数错误');
        $broadcast=SystemBroadcastModel::where('id',$id)->where('is_del',0)->find();
        if(!$broadcast) return Json::fail('停用域名不存在');
        $res=AlipayDisposeService::stopLiveDomains($broadcast['domain_name'],$broadcast['region']);
        if($res){
            SystemBroadcastModel::where('id',$id)->update(['domain_status'=>'offline']);
            return Json::successful('停用域名成功');
        }else{
            return Json::fail('停用域名失败');
        }
    }

    /**启用域名
     * @param int $id
     */
    public function onlines($id=0)
    {
        if (!$id) return Json::fail('参数错误');
        $broadcast=SystemBroadcastModel::where('id',$id)->where('is_del',0)->find();
        if(!$broadcast) return Json::fail('启用不存在');
        $res=AlipayDisposeService::startLiveDomains($broadcast['domain_name'],$broadcast['region']);
        if($res){
            SystemBroadcastModel::where('id',$id)->update(['domain_status'=>'online']);
            return Json::successful('启用域名成功');
        }else{
            return Json::fail('启用域名失败');
        }
    }

    /**使用直播域名
     * @param int $id
     */
    public function userLiveUse($id=0)
    {
        if (!$id) return Json::fail('参数错误');
        $broadcast=SystemBroadcastModel::where('id',$id)->where('live_domain_type','liveVideo')->where('domain_status','online')->where('is_del',0)->find();
        if(!$broadcast) return Json::fail('域名不存在或不是播流域名');
        if(!$broadcast['push_domain']) return Json::fail('请绑定推流域名');
        $push_domain=SystemBroadcastModel::where('domain_name',$broadcast['push_domain'])->where('live_domain_type','liveEdge')->where('domain_status','online')->where('is_del',0)->find();
        if(!$push_domain) return Json::fail('绑定的推流域名不存在');
        $bucket=SystemBucketModel::where('is_del',0)->where('bucket_name',$broadcast['bucket_name'])->find();
        if(!$bucket) return Json::fail('储存oss桶不存在');
        $res=SystemConfigService::setOneValue('aliyun_live_rtmpLink',$broadcast['push_domain']);
        $res1=SystemConfigService::setOneValue('aliyun_live_playLike',$broadcast['domain_name']);
        $res2=SystemConfigService::setOneValue('aliyun_live_push_key',$push_domain['auth_key1']);
        $res3=SystemConfigService::setOneValue('aliyun_live_play_key',$broadcast['auth_key1']);
        $res4=SystemConfigService::setOneValue('aliyun_live_appName','zsffLive');
        $res5=SystemConfigService::setOneValue('aliyun_live_oss_bucket',$broadcast['bucket_name']);
        $res6=SystemConfigService::setOneValue('aliyun_live_end_point',$bucket['endpoint']);
        if($res && $res1 && $res2 && $res3 && $res4 && $res5 && $res6){
            SystemBucketModel::where('is_use',2)->where('is_del',0)->update(['is_use'=>0]);
            SystemBucketModel::where('id',$id)->where('is_del',0)->update(['is_use'=>2]);
            SystemBroadcastModel::where('id',$id)->where('live_domain_type','liveVideo')->where('domain_status','online')->where('is_del',0)->update(['is_use'=>1]);
            SystemBroadcastModel::where('domain_name',$broadcast['push_domain'])->where('live_domain_type','liveEdge')->where('domain_status','online')->where('is_del',0)->update(['is_use'=>1]);
            return Json::successful('设置直播域名成功');
        }else{
            return Json::fail('设置直播域名失败');
        }
    }
}
