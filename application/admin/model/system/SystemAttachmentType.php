<?php
/**
 * 附件目录
 *
 */
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

/**
 * 文件检验model
 * Class SystemFile
 * @package app\admin\model\system
 */
class SystemAttachmentType extends ModelBasic
{
    use ModelTrait;
    /**添加附件记录
     */
    public static function attachmentAdd($name,$att_size,$att_type,$att_dir,$satt_dir='',$pid = 0 )
    {
        $data['name'] = $name;
        $data['att_dir'] = $att_dir;
        $data['satt_dir'] = $satt_dir;
        $data['att_size'] = $att_size;
        $data['att_type'] = $att_type;
        $data['time'] = time();
        $data['pid'] = $pid;
        return self::create($data);
    }
    /**
     * 获取分类图
     * */
    public static function getAll($id){
        $model = new self;
        $where['pid'] = $id;
        $model->where($where)->order('att_id desc');
        return $model->page($model,$where,'',30);
    }
    /**
     * 获取单条信息
     * */
    public static function getinfo($att_id){
        $model = new self;
        $where['att_id'] = $att_id;
        return $model->where($where)->select()->toArray()[0];
    }

}