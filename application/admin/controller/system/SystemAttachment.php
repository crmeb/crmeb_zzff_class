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

namespace app\admin\controller\system;

use app\admin\model\system\SystemAttachment as SystemAttachmentModel;
use app\admin\controller\AuthController;
use service\SystemConfigService;
use service\UploadService as Upload;

/**
 * 附件管理控制器
 * Class SystemAttachment
 * @package app\admin\controller\system
 *
 */
class SystemAttachment extends AuthController
{

    /**
     * 编辑器上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('upfile', 'editor/' . date('Ymd'));
        //产品图片上传记录
        $fileInfo = $res->fileInfo->getinfo();
        $thumbPath = Upload::thumb($res->dir);
        SystemAttachmentModel::attachmentAdd($res->fileInfo->getSaveName(), $fileInfo['size'], $fileInfo['type'], $res->dir, $thumbPath, 0);
        $info = array(
            "originalName" => $fileInfo['name'],
            "name" => $res->fileInfo->getSaveName(),
            "url" => '.' . $res->dir,
            "size" => $fileInfo['size'],
            "type" => $fileInfo['type'],
            "state" => "SUCCESS"
        );
        echo json_encode($info);
    }

    public function index($action)
    {
        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("system/plug/ueditor/php/config.json")), true);
        $CONFIG['imageUrlPrefix'] = SystemConfigService::get('uploadUrl');
        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $result = json_encode([]);
                break;

            /* 列出图片 */
            case 'listimage':
                $result = json_encode([]);
                break;

            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }

        return $result;
    }
}
