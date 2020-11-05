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
namespace app\wap\model\user;

use service\CanvasService;
use think\Url;
use traits\ModelTrait;
use basic\ModelBasic;
use service\SystemConfigService;

class SignPoster extends ModelBasic
{
    use ModelTrait;

    public static function todaySignPoster($uid){
        $time=strtotime(date('Y-m-d',time()));
        $url=SystemConfigService::get('site_url');
        $http=substr($url,0,4);
        $rest = substr($url, -1);
        if($http=='http' && $rest!='/') $urls=$url.'/';
        else if($http!='http') return false;
        else $urls=$url;
        $signPoster=self::order('sort DESC')->select();
        $signPoster=count($signPoster)>0 ? $signPoster->toArray() :[];
        $poster=SystemConfigService::get('sign_default_poster');
        if(count($signPoster)>0){
            foreach ($signPoster as $key=>$value){
                if($value['sign_time']==$time){
                    $poster=$value['poster'];
                    break;
                }
            }
        }
        $url = $urls .'wap/my/sign_in/spread_uid=' . $uid;
        if(!$poster) return false;
        try {
            $filename = CanvasService::foundSignCode($uid, $url, $poster,$urls);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $urls .$filename;
    }



}