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


namespace app\wap\controller;


use app\wap\model\user\SmsCode;
use app\admin\model\system\SystemGroup;
use app\admin\model\system\SystemGroupData;
use app\wap\model\store\StoreCategory;
use app\wap\model\store\StoreProduct;
use app\wap\model\wap\ArticleCategory;
use service\AliMessageService;
use service\JsonService;
use service\SystemConfigService;
use think\Session;

class PublicApi
{

    /*
   * 发送短信验证码
   * @param string $phone
   * */
    public function code($phone = '')
    {
        $name = "is_phone_code" . $phone;
        if ($phone == '') return JsonService::fail('请输入手机号码!');
        $time = Session::get($name, 'routine');
        if ($time < time() + 60) Session::delete($name, 'routine');
        if (Session::has($name, 'routine') && $time < time()) return JsonService::fail('您发送验证码的频率过高,请稍后再试!');
        $code = AliMessageService::getVerificationCode();
        SmsCode::set(['tel' => $phone, 'code' => $code, 'last_time' => time() + 300]);
        Session::set($name, time() + 60, 'routine');
        $res = AliMessageService::sendmsg($phone, $code);
        if($res){
            return JsonService::successful('发送成功',$res);
        } else {
            return JsonService::fail('发送失败!');
        }
    }

    public function get_cid_article($cid = 0, $first = 0, $limit = 8)
    {
        $list = ArticleCategory::cidByArticleList($cid, $first, $limit, 'id,title,image_input,visit,add_time,synopsis,url') ?: [];
        foreach ($list as &$article) {
            $article['add_time'] = date('Y-m-d H:i', $article['add_time']);
        }
        return JsonService::successful('ok', $list);
    }

    public function get_video_list($key = '', $first = 0, $limit = 8)
    {
        $gid = SystemGroup::where('config_name', $key)->value('id');
        if (!$gid) {
            $list = [];
        } else {
            $video = SystemGroupData::where('gid', $gid)->where('status', 1)->order('sort DESC,add_time DESC')->limit($first, $limit)->select();
            $list = SystemGroupData::tidyList($video);
        }
        return JsonService::successful('ok', $list);
    }

    public function get_category_product_list($limit = 4)
    {
        $cateInfo = StoreCategory::where('is_show', 1)->where('pid', 0)->field('id,cate_name,pic')
            ->order('sort DESC')->select()->toArray();
        $result = [];
        $StoreProductModel = new StoreProduct;
        foreach ($cateInfo as $k => $cate) {
            $cate['product'] = $StoreProductModel::alias('A')->where('A.is_del', 0)->where('A.is_show', 1)
                ->where('A.mer_id', 0)->where('B.pid', $cate['id'])
                ->join('__STORE_CATEGORY__ B', 'B.id = A.cate_id')
                ->order('A.is_benefit DESC,A.sort DESC,A.add_time DESC')
                ->limit($limit)->field('A.id,A.image,A.store_name,A.sales,A.price,A.unit_name')->select()->toArray();
            if (count($cate['product']))
                $result[] = $cate;
        }
        return JsonService::successful($result);
    }

    public function get_best_product_list($first = 0, $limit = 8)
    {
        return JsonService::successful(StoreProduct::getHotProduct('id,image,store_name,cate_id,price,vip_price,unit_name,sort,sales', 6));
    }

    public function wechat_media_id_by_image($mediaIds = '')
    {

        if (!$mediaIds) return JsonService::fail('参数错误');
        try {
            $mediaIds = explode(',', $mediaIds);
            $temporary = \service\WechatService::materialTemporaryService();
            $pathList = [];
            foreach ($mediaIds as $mediaId) {
                if (!$mediaId) continue;
                try {
                    $content = $temporary->getStream($mediaId);
                } catch (\Exception $e) {
                    continue;
                }
                $name = substr(md5($mediaId), 12, 20) . '.jpg';
                $res = \Api\AliyunOss::instance([
                    'AccessKey' => SystemConfigService::get('accessKeyId'),
                    'AccessKeySecret' => SystemConfigService::get('accessKeySecret'),
                    'OssEndpoint' => SystemConfigService::get('end_point'),
                    'OssBucket' => SystemConfigService::get('OssBucket'),
                    'uploadUrl' => SystemConfigService::get('uploadUrl'),
                ])->stream($content, $name);
                if ($res !== false) {
                    $pathList[] = $res['url'];
                }
            }
            return JsonService::successful($pathList);
        } catch (\Exception $e) {
            return JsonService::fail('上传失败', ['msg' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }
    }

}