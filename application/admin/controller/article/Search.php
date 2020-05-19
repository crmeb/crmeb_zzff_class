<?php

namespace app\admin\controller\article;

use app\admin\controller\AuthController;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use service\FormBuilder as Form;
use app\admin\model\article\Search as SearchModel;
/**
 * 搜索
 * Class Search
 * @package app\admin\controller\article
 */
class Search extends AuthController
{
    public function index(){
        $this->assign('list',SearchModel::getAll());
        return $this->fetch();
    }

    public function save($name=''){
        if(!$name) return Json::fail('请输入热词名称');
        if($res=SearchModel::saveSearch($name))
            return Json::successful('添加成功',$res);
        else
            return Json::fail(SearchModel::getErrorInfo('添加失败'));
    }

    public function del_search($id=0){
        if(!$id) return Json::fail('缺少参数');
        if(SearchModel::del($id))
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }
}