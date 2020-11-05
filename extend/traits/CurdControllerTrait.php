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
namespace traits;

use service\JsonService;
use think\Request;

trait CurdControllerTrait
{
    public function change_field($id,$field)
    {
        if(!isset($this->bindModel)) return exception('方法不存在!');
        if(!class_exists($this->bindModel)) return JsonService::fail('操作Model不存在!');
        $model = new $this->bindModel;
        $pk = $model->getPk();
        if(strtolower($pk) == strtolower($field)) return JsonService::fail('主键不允许修改!');
        $data = $model->where($pk,$id)->find();
        if(!$data) JsonService::fail('记录不存在!');
        $value = Request::instance()->post($field);
        if($value === null) return JsonService::fail('请提交需要编辑的数据!');
        $data->$field = $value;
        return false !== $data->save() ? JsonService::successful('编辑成功!') : JsonService::fail('编辑失败!');

    }

    public function consult_field($id,$field)
    {
        if(!isset($this->bindModel)) return exception('方法不存在!');
        if(!class_exists($this->bindModel)) return JsonService::fail('操作Model不存在!');
        $model = new $this->bindModel;
        $data = $model->where('nid',$id)->find();
        $value = Request::instance()->post($field);
        if($value === null) return JsonService::fail('请提交需要编辑的数据!');
        if(!$data){
            $data[$field] = $value;
            $data['nid'] = $id;
            return false !== $model->create($data) ? JsonService::successful('添加成功!') : JsonService::fail('添加失败!');
        }else{
            $data->$field = $value;
            return false !== $data->save() ? JsonService::successful('编辑成功!') : JsonService::fail('编辑失败!');
        }
    }
}