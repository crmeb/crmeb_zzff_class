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

use app\admin\model\special\Grade;
use app\admin\model\special\RecommendBanner;
use app\admin\model\special\Special;
use app\admin\model\system\Recommend;
use app\admin\model\system\RecommendRelation;
use app\admin\model\user\Group;
use service\FormBuilder as Form;
use service\JsonService as Json;
use service\UploadService as Upload;
use service\UtilService as Util;
use think\Request;
use think\Url;
use app\admin\model\system\SystemGroup as GroupModel;
use app\admin\model\system\SystemGroupData as GroupDataModel;
use app\admin\controller\AuthController;
use app\admin\model\system\SystemAttachment;

/**
 * 数据列表控制器  在组合数据中
 * Class SystemGroupData
 * @package app\admin\controller\system
 */
class SystemGroupData extends AuthController
{

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index($gid)
    {
        $where = Util::getMore([
            ['status', '']
        ], $this->request);
        $this->assign('where', $where);
        $this->assign(compact("gid"));
        $this->assign(GroupModel::getField($gid));
        $where['gid'] = $gid;
        $this->assign(GroupDataModel::getList($where));
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     */
    public function create($gid)
    {
        $Fields = GroupModel::getField($gid);
        $f = array();
        foreach ($Fields["fields"] as $key => $value) {
            $info = [];
            if (!empty($value["param"])) {
                $value["param"] = str_replace("\r\n", "\n", $value["param"]);//防止不兼容
                $params = explode("\n", $value["param"]);
                if (is_array($params) && !empty($params)) {
                    foreach ($params as $index => $v) {
                        if (strstr($v, '=>') !== false) {
                            list($left, $right) = explode('=>', $v);
                        } else if (strstr($v, '=') !== false) {
                            list($left, $right) = explode('=', $v);
                        }
                        $val["value"] = $left;
                        $val["label"] = $right;
                        $info[] = $val;
                    }
                }
            }

            switch ($value["type"]) {
                case 'input':
                    $f[] = Form::input($value["title"], $value["name"]);
                    break;
                case 'textarea':
                    $f[] = Form::input($value["title"], $value["name"])->type('textarea')->placeholder($value['param']);
                    break;
                case 'radio':
                    $f[] = Form::radio($value["title"], $value["name"], isset($info[0]["value"]) ? $info[0]["value"] : '')->options($info);
                    break;
                case 'checkbox':
                    $f[] = Form::checkbox($value["title"], $value["name"], isset($info[0]) ? $info[0] : '')->options($info);
                    break;
                case 'select':
                    $f[] = Form::select($value["title"], $value["name"], isset($info[0]) ? $info[0] : '')->options($info)->multiple(false);
                    break;
                case 'upload':
                    $f[] = Form::frameImageOne($value["title"], $value["name"], Url::build('admin/widget.images/index', array('fodder' => $value["title"])))->icon('image')->width('100%')->height('500px');
                    break;
                case 'uploads':
                    $f[] = Form::frameImages($value["title"], $value["name"], Url::build('admin/widget.images/index', array('fodder' => $value["title"])))->maxLength(5)->icon('images')->width('100%')->height('500px')->spin(0);
                    break;
                default:
                    $f[] = Form::input($value["title"], $value["name"]);
                    break;

            }
        }
        $f[] = Form::number('sort', '排序', 1);
        $f[] = Form::radio('status', '状态', 1)->options([['value' => 1, 'label' => '显示'], ['value' => 2, 'label' => '隐藏']]);
        $form = Form::make_post_form('添加数据', $f, Url::build('save', compact('gid')), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request, $gid)
    {
        $Fields = GroupModel::getField($gid);
        $params = $request->post();
        $value = array();
        foreach ($params as $key => $param) {
            foreach ($Fields['fields'] as $index => $field) {
                if ($key == $field["title"]) {
                    if ($param == "" || empty($param))
                        return Json::fail($field["name"] . "不能为空！");
                    else {
                        $value[$key]["type"] = $field["type"];
                        $value[$key]["value"] = $param;
                    }
                }
            }
        }

        $data = array("gid" => $gid, "add_time" => time(), "value" => json_encode($value), "sort" => $params["sort"], "status" => $params["status"]);
        GroupDataModel::set($data);
        return Json::successful('添加数据成功!');
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($gid, $id)
    {
        $GroupData = GroupDataModel::get($id);
        $GroupDataValue = json_decode($GroupData["value"], true);
        $Fields = GroupModel::getField($gid);
        $f = array();
        foreach ($Fields['fields'] as $key => $value) {
            $info = [];
            if (!empty($value["param"])) {
                $value["param"] = str_replace("\r\n", "\n", $value["param"]);//防止不兼容
                $params = explode("\n", $value["param"]);
                if (is_array($params) && !empty($params)) {
                    foreach ($params as $index => $v) {
                        if (strstr($v, '=>') !== false) {
                            list($left, $right) = explode('=>', $v);
                        } else if (strstr($v, '=') !== false) {
                            list($left, $right) = explode('=', $v);
                        }
                        $val["value"] = $left;
                        $val["label"] = $right;
                        $info[] = $val;
                    }
                }
            }
            switch ($value['type']) {
                case 'input':
                    $f[] = Form::input($value['title'], $value['name'], $GroupDataValue[$value['title']]['value']);
                    break;
                case 'textarea':
                    $f[] = Form::input($value['title'], $value['name'], $GroupDataValue[$value['title']]['value'])->type('textarea');
                    break;
                case 'radio':
                    $f[] = Form::radio($value['title'], $value['name'], $GroupDataValue[$value['title']]['value'])->options($info);
                    break;
                case 'checkbox':
                    if(array_key_exists($value['title'],$GroupDataValue)){
                        $f[] = Form::checkbox($value['title'], $value['name'], $GroupDataValue[$value['title']]['value'])->options($info);
                    }else{
                        $f[] = Form::checkbox($value["title"], $value["name"], isset($info[0]) ? $info[0] : '')->options($info);
                    }
                    break;
                case 'upload':
                    if (!empty($GroupDataValue[$value['title']]['value'])) {
                        $image = is_string($GroupDataValue[$value['title']]['value']) ? $GroupDataValue[$value['title']]['value'] : $GroupDataValue[$value['title']]['value'][0];
                    } else {
                        $image = '';
                    }
                    $f[] = Form::frameImageOne($value['title'], $value['name'], Url::build('admin/widget.images/index', array('fodder' => $value['title'])), $image)->icon('image')->width('100%')->height('500px');
                    break;
                case 'uploads':
                    $images = !empty($GroupDataValue[$value['title']]['value']) ? $GroupDataValue[$value['title']]['value'] : [];
                    $f[] = Form::frameImages($value['title'], $value['name'], Url::build('admin/widget.images/index', array('fodder' => $value['title'])), $images)->maxLength(5)->icon('images')->width('100%')->height('550px')->spin(0);
                    break;
                case 'select':
                    $f[] = Form::select($value['title'], $value['name'], $GroupDataValue[$value['title']]['value'])->setOptions($info);
                    break;
                default:
                    $f[] = Form::input($value['title'], $value['name'], $GroupDataValue[$value['title']]['value']);
                    break;

            }
        }
        $f[] = Form::input('sort', '排序', $GroupData["sort"]);
        $f[] = Form::radio('status', '状态', $GroupData["status"])->options([['value' => 1, 'label' => '显示'], ['value' => 2, 'label' => '隐藏']]);
        $form = Form::make_post_form('编辑', $f, Url::build('update', compact('id')), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $GroupData = GroupDataModel::get($id);
        $Fields = GroupModel::getField($GroupData["gid"]);
        $params = $request->post();
        foreach ($params as $key => $param) {
            foreach ($Fields['fields'] as $index => $field) {
                if ($key == $field["title"]) {
                    if ($param == "" || !$param || empty($param))
                        return Json::fail($field["name"] . "不能为空！");
                    else {
                        $value[$key]["type"] = $field["type"];
                        $value[$key]["value"] = $param;
                    }
                }
            }
        }
        $data = array("value" => json_encode($value), "sort" => $params["sort"], "status" => $params["status"]);
        GroupDataModel::edit($data, $id);
        return Json::successful('修改成功!');
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!GroupDataModel::del($id))
            return Json::fail(GroupDataModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    public function upload()
    {
        $res = Upload::image('file', 'common');
        $thumbPath = Upload::thumb($res->dir);
        //产品图片上传记录
        $fileInfo = $res->fileInfo->getinfo();
        SystemAttachment::attachmentAdd($res->fileInfo->getSaveName(), $fileInfo['size'], $fileInfo['type'], $res->dir, $thumbPath, 6);

        if ($res->status == 200)
            return Json::successful('图片上传成功!', ['name' => $res->fileInfo->getSaveName(), 'url' => Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
    }

    public function recommend()
    {
        $this->assign('fixedList', Recommend::fixedList());
        return $this->fetch();
    }

    public function recommend_list()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['order', ''],
            ['is_fixed', $this->request->param('is_fixed', 0)]
        ]);
        return Json::successlayui(Recommend::getRecommendList($where));
    }

    public function create_recemmend($id = 0)
    {
        if ($id) $this->assign('recemmend', Recommend::get($id));
        $this->assign('is_fixed', 1);
        $this->assign('grade_list', Grade::getAll());
        $this->assign('id', $id);
        return $this->fetch();
    }

    public function create_recemmend_v1($id = 0)
    {
        if ($id) $this->assign('recemmend', Recommend::get($id));
        $this->assign('is_fixed', 0);
        $this->assign('grade_list', Grade::getAll());
        $this->assign('is_fixed', 0);
        $this->assign('id', $id);
        return $this->fetch();
    }

    public function save_recemmend($id = 0)
    {
        $post = Util::postMore([
            ['icon', ''],
            ['image', ''],
            ['title', ''],
            ['type', ''],
            ['sort', 0],
            ['is_fixed', 0],
            ['is_show', 0],
            ['grade_id', 0],
            ['show_count', 0],
            ['typesetting', ''],
        ]);
        if ($id) {
            $post['is_show'] = $post['is_fixed'] ? 1 : $post['is_show'];
            $rescomm = Recommend::get($id);
            if (!$rescomm) return Json::fail('修改的信息不存在');
            Recommend::update($post, ['id' => $id]);
            return Json::successful('修改成功');
        } else {
            $post['add_time'] = time();
            if (Recommend::set($post))
                return Json::successful('保存成功');
            else
                return Json::fail('保存失败');
        }
    }

    public function recemmend_content($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        if ($this->request->isAjax()) {
            $where = Util::getMore([
                ['page', 1],
                ['limit', 20],
            ]);
            return Json::successlayui(RecommendRelation::getAll($where, $id));
        } else {
            $this->assign('id', $id);
            return $this->fetch();
        }
    }

    public function recemmed_delete($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        if (RecommendRelation::del($id))
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }

    /**
     * 设置单个产品上架|下架
     *
     * @return json
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && Json::fail('缺少参数');
        $res = Recommend::where(['id' => $id])->update(['is_show' => (int)$is_show]);
        if ($res) {
            return Json::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        } else {
            return Json::fail($is_show == 1 ? '显示失败' : '隐藏失败');
        }
    }

    public function set_show_banner($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && Json::fail('缺少参数');
        $res = RecommendBanner::where(['id' => $id])->update(['is_show' => (int)$is_show]);
        if ($res) {
            return Json::successful($is_show == 1 ? '显示成功' : '隐藏成功');
        } else {
            return Json::fail($is_show == 1 ? '显示失败' : '隐藏失败');
        }
    }

    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {
        $field == '' || $id == '' || $value == '' && Json::fail('缺少参数');
        if (Recommend::where(['id' => $id])->update([$field => $value]))
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_recemmend_value($field = '', $id = '', $value = '')
    {
        $field == '' || $id == '' || $value == '' && Json::fail('缺少参数');
        if (RecommendRelation::where(['id' => $id])->update([$field => $value]))
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    public function set_value_banner($field = '', $id = '', $value = '')
    {
        $field == '' || $id == '' || $value == '' && Json::fail('缺少参数');
        if (RecommendBanner::where(['id' => $id])->update([$field => $value]))
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete_recomm($id)
    {
        if (RecommendBanner::be(['recommend_id' => $id])) return Json::fail('删除失败，请先删除Banner图');
        if (RecommendRelation::be(['recommend_id' => $id])) return Json::fail('删除失败，请先删除内容管理里面的列表');
        if (!Recommend::del($id))
            return Json::fail('删除失败');
        else
            return Json::successful('删除成功!');
    }

    /**
     * 删除导航推荐
     * @param string $id
     */
    public function delete_banner($id = '')
    {
        if (!RecommendBanner::del($id))
            return Json::fail('删除失败');
        else
            return Json::successful('删除成功!');
    }

    public function recemmend_banner($id = '')
    {
        if ($id == '') return $this->failed('缺少参数');
        $this->assign('id', $id);
        $this->assign('type', 1);
        return $this->fetch();
    }

    public function recemmend_banner_list()
    {
        $where = Util::getMore([
            ['id', ''],
            ['page', ''],
            ['limit', ''],
        ]);
        if ($where['id'] == '') return Json::fail('缺少参数');
        return Json::successlayui(RecommendBanner::getRecemmodBannerList($where));
    }

    /*
     * 创建banner图
     * */
    public function create_recemmend_banner($id = '', $banner_id = 0)
    {
        $this->assign('id', $id);
        if ($banner_id) {
            $banner = RecommendBanner::get($banner_id);
            if (!$banner) return $this->failed('缺少修改的banner');
            $banner['pic_key'] = get_key_attr($banner['pic'], false);
            $this->assign('banner', $banner);
        }
        $this->assign('banner_id', (int)$banner_id);
        $this->assign('type', 2);
        return $this->fetch();
    }

    public function save_recemmend_banner($id = '', $banner_id = '')
    {
        $post = Util::postMore([
            ['url', ''],
            ['sort', ''],
            ['is_show', 0],
            ['pic', ''],
        ]);
        if ($id == '') return Json::fail('缺少参数');
        if ($post['pic'] == '') return Json::fail('请上传封面图!');
        if ($post['is_show'] == 'on') $post['is_show'] = 1;
        else $post['is_show'] = 0;
        $post['recommend_id'] = $id;
        if ($banner_id) {
            RecommendBanner::edit($post, $banner_id);
            return Json::successful('修改成功');
        } else {
            $post['add_time'] = time();
            RecommendBanner::set($post);
            return Json::successful('保存成功');
        }
    }

    /**
     * 首页导航固定跳转添加
     * @return mixed
     */
    public function navigation()
    {
        return $this->fetch();
    }

    /**
     * 自定义跳转导航添加和修改页面
     * @param int $id
     * @return mixed|void
     * @throws \FormBuilder\exception\FormBuilderException
     * @throws \think\exception\DbException
     */
    public function create_recemmend_custom($id = 0)
    {
        if ($id) {
            $recommend = Recommend::get($id);
            if (!$recommend) {
                return $this->failed('您修改的导航不存在');
            }
        }
        $f[] = Form::input('title', '导航名称', isset($recommend) ? $recommend->title : '');
        $f[] = Form::frameImageOne('icon', '图标', get_image_Url('icon'), isset($recommend) ? $recommend->icon : '')->icon('image')->width('100%')->height('500px');
        $f[] = Form::input('link', '跳转路径', isset($recommend) ? $recommend->link : '');
        $f[] = Form::input('sort', '排序', isset($recommend) ? $recommend->sort : 0);
        $f[] = Form::radio('is_show', '状态', isset($recommend) ? $recommend->is_show : 0)->options([['value' => 1, 'label' => '显示'], ['value' => 0, 'label' => '隐藏']]);
        $form = Form::make_post_form('编辑', $f, Url::build('save_recemmend_custom', compact('id')), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存自定义导航链接
     * @param int $id
     */
    public function save_recemmend_custom($id = 0)
    {
        $data = Util::postMore([
            ['title', ''],
            ['icon', ''],
            ['link', ''],
            ['is_show', 0],
            ['type', 3],
            ['is_fixed', 1],
        ]);

        if (!$data['title']) {
            return Json::fail('请填写导航名称');
        }
        if (!$data['icon']) {
            return Json::fail('请选择导航图标');
        }
        if (!$data['link']) {
            return Json::fail('请填写导航跳转地址');
        }

        if ($id) {
            Recommend::where('id', $id)->update($data);
            return Json::successful('修改成功');
        } else {
            $data['add_time'] = time();
            $res = Recommend::set($data);
            if ($res) {
                return Json::successful('添加成功');
            } else {
                return Json::fail('修改失败');
            }
        }
    }


    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index_v1($gid)
    {
        $this->assign(compact("gid"));
        return $this->fetch();
    }

    /**
     * 获取某个组合数据列表
     * @param int $gid
     * @param int $page
     * @param int $limit
     * @throws \think\Exception
     */
    public function get_group_data_list($gid = 0, $status = '', $page = 1, $limit = 10)
    {
        $model = GroupDataModel::where(function ($query) use ($gid, $status) {
            $query->where('gid', $gid);
            if ($status != '') {
                $query->where('status', $status);
            }
        });
        $data = $model->order('sort desc,id desc')->page($page, $limit)->select();
        $data = count($data) ? $data->toArray() : [];
        foreach ($data as &$item) {
            $value = json_decode($item['value'], true);
            foreach ($value as $key => $val) {
                $item[$key] = $value[$key]['value'];
            }
        }
        $count = $model->count();
        return Json::successlayui(compact('data', 'count'));
    }

    /**
     * 修改某个字段
     * @param string $field
     * @param int $id
     * @param string $value
     */
    public function set_group_data($field = '', $id = 0, $value = '')
    {
        if ('id' == $field) {
            return Json::fail('修改失败,主键不允许修改');
        }
        if (!$field && !$value) {
            return Json::fail('缺少修改参数');
        }
        $info = GroupDataModel::where('id', $id)->find();
        if (!$info) {
            return Json::fail('修改的信息不存在');
        }
        if (in_array($field, ['sort', 'status'])) {
            $info->{$field} = $value;
            $res = $info->save();
        } else {
            $infoVale = json_decode($info->value, true);
            $infoVale[$field]['value'] = $value;
            $info->value = json_encode($infoVale);
            $res = $info->save();
        }
        if ($res) {
            return Json::successful('修改成功');
        } else {
            return Json::fail('修改失败');
        }
    }

    /**
     * 添加组合数据页面
     * @return mixed
     */
    public function create_v1($id = 0)
    {
        $this->assign([
            'specialList' => json_encode(Special::PreWhere()->field(['id', 'title'])->order('sort desc,id desc')->select()),
            'cateList' => json_encode(Grade::field(['id', 'name as title'])->order('sort desc,id desc')->select()),
        ]);
        if ($id) {
            $info = GroupDataModel::get($id);
            if ($info) {
                $infoValue = json_decode($info->value, true);
                $this->assign('data', [
                    'title' => isset($infoValue['title']['value']) ? $infoValue['title']['value'] : "",
                    'pic' => isset($infoValue['pic']['value']) ? $infoValue['pic']['value'] : "",
                    'info' => isset($infoValue['info']['value']) ? $infoValue['info']['value'] : '',
                    'sort' => $info->sort,
                    'status' => $info->status,
                    'type' => isset($infoValue['type']['value']) ? $infoValue['type']['value'] : "''",
                    'select_id' => isset($infoValue['select_id']['value']) ? $infoValue['select_id']['value'] : "''",
                    'id' => $id
                ]);
            }
        }
        return $this->fetch();
    }

    public function save_group_data($name = '')
    {
        $data = Util::postMore([
            ['title', ''],
            ['id', ''],
            ['image', ''],
            ['info', ''],
            ['type', 0],
            ['select_id', 0],
            ['sort', 0],
            ['status', 0],
        ]);
        $gid = GroupModel::where(['config_name' => $name])->value('id');
        if (!isset($data['id']) || !$data['id']) {
            if (GroupDataModel::where('gid', $gid)->count() >= 3) {
                return Json::fail('最多能添加3条信息');
            }
        }
        if (!$data['title']) {
            return Json::fail('请输入标题');
        }
        if (!$data['image']) {
            return Json::fail('请选择图片');
        }
        if (!$data['info']) {
            return Json::fail('请输入简介');
        }
        if (!$data['select_id']) {
            return Json::fail('请选择' . ($data['type'] ? '分类' : "专题"));
        }
        $info = '{"pic":{"type":"upload","value":""},"title":{"type":"input","value":""},"info":{"type":"input","value":""},"wap_link":{"type":"select","value":""}}';
        $info = json_decode($info, true);
        $info['pic']['value'] = $data['image'];
        $info['title']['value'] = $data['title'];
        $info['info']['value'] = $data['info'];
        $info['select_id']['value'] = $data['select_id'];
        $info['select_id']['type'] = 'select';
        $info['type']['type'] = 'radio';
        $info['type']['value'] = $data['type'];
        if ($data['type']) {
            $info['wap_link']['value'] = '/wap/special/special_cate?cate_id=' . $data['select_id'];
        } else {
            $info['wap_link']['value'] = '/wap/special/details?id=' . $data['select_id'];
        }
        if (isset($data['id']) && $data['id']) {
            $res = GroupDataModel::update([
                'gid' => $gid,
                'value' => json_encode($info),
                'sort' => $data['sort'],
                'status' => $data['status'],
            ],['id' => $data['id']]);
        }else{
            $res = GroupDataModel::set([
                'gid' => $gid,
                'value' => json_encode($info),
                'add_time' => time(),
                'sort' => $data['sort'],
                'status' => $data['status'],
            ]);
        }
        if ($res) {
            return Json::successful('编辑成功');
        } else {
            return Json::fail('编辑失败');
        }
    }
}
