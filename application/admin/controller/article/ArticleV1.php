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

namespace app\admin\controller\article;

use app\admin\controller\AuthController;
use app\admin\model\article\ArticleCategory as ArticleCategoryModel;
use app\admin\model\article\ArticleContent;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\article\Article;
use app\admin\model\system\Recommend;
use app\admin\model\system\RecommendRelation;
use think\Url;
use service\FormBuilder as Form;

/**
 * 图文管理
 * Class WechatNews
 * @package app\admin\controller\wechat
 */
class ArticleV1 extends AuthController
{
    public function index($cid = 0)
    {
        $this->assign('cid',$cid);
        $this->assign('cate',ArticleCategoryModel::getTierList());
        $this->assign('type', $this->request->param('type', 1));
        return $this->fetch();
    }


    public function article_list()
    {
        $where = Util::getMore([
            ['limit', 20],
            ['page', 1],
            ['cid', $this->request->param('cid')],
            ['store_name', ''],
            ['order', ''],
            ['is_show', ''],
        ]);
        return Json::successlayui(Article::getArticleLayList($where));
    }

    /**
     * 设置单个产品上架|下架
     *
     * @return json
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && Json::fail('缺少参数');
        $res = Article::where(['id' => $id])->update(['is_show' => (int)$is_show]);
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
        if (Article::where(['id' => $id])->update([$field => $value]))
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    public function add_article($id=0)
    {
        $this->assign('id', $id);
        if ($id) {
            $article = Article::get($id);
            $article->profile->content = htmlspecialchars_decode($article->profile->content);
            $this->assign('article', $article->toJson());
        }
        if(empty($all)){
            $list = ArticleCategoryModel::getTierList();
            $all = [];
            foreach ($list as $menu){
                $all[$menu['id']] = $menu['html'].$menu['title'];
            }
        }
        $this->assign('all', json_encode($all));
        $this->assign('type', $this->request->param('type', 2));
        return $this->fetch();
    }

    public function save_article($id = 0)
    {
        $data = Util::postMore([
            ['title', ''],
            ['synopsis', ''],
            ['sort', 0],
            ['cid', 0],
            ['content', ''],
            ['image_input', ''],
            ['label', []],
        ]);
        if (!$data['title']) return Json::fail('请输入图文标题');
        if (!$data['synopsis']) return Json::fail('请输入图文简介');
        if (count($data['label']) < 1) return Json::fail('请输入标签');
        if (!$data['content']) return Json::fail('请输入图文内容');
        $data['label'] = json_encode($data['label']);
        $content = htmlspecialchars($data['content']);
        Article::beginTrans();
        try {
            if ($id) {
                Article::update($data, ['id' => $id]);
                ArticleContent::update(['content' => $content], ['nid' => $id]);
                Article::commitTrans();
                return Json::successful('修改成功');
            } else {
                $data['add_time'] = time();
                $data['is_show'] = 1;
                $res1 = Article::set($data);
                $res2 = ArticleContent::set(['nid' => Article::getLastInsID(), 'content' => $content]);
                if ($res1 && $res2) {
                    Article::commitTrans();
                    return Json::successful('添加成功');
                } else {
                    Article::rollbackTrans();
                    return Json::fail('添加失败');
                }
            }
        } catch (\Exception $e) {
            Article::rollbackTrans();
            return Json::fail($e->getMessage());
        }
    }

    public function delete($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        $article = Article::get($id);
        if (!$article) return Json::fail('没有查找到图文');
        Article::beginTrans();
        try {
            $res = $article->delete();
            $res1 = ArticleContent::where('nid', $id)->delete();
            if ($res1 && $res) {
                Article::commitTrans();
                return Json::successful('删除成功');
            } else {
                Article::rollbackTrans();
                return Json::fail('删除失败');
            }
        } catch (\Exception $e) {
            Article::rollbackTrans();
            return Json::fail($e->getMessage());
        }
    }

    /*
    * 添加推荐
    * */
    public function recommend($article_id = 0)
    {
        if (!$article_id) $this->failed('缺少参数');
        $article = Article::get($article_id);
        if (!$article) $this->failed('没有查到此专题');
        $form = Form::create(Url::build('save_recommend', ['article_id' => $article_id]), [
            Form::select('recommend_id', '推荐')->setOptions(function () {
                $list = Recommend::where(['is_show' => 1, 'type' => 1])->field('title,id')->order('sort desc,add_time desc')->select();
                $menus = [['value' => 0, 'label' => '顶级菜单']];
                foreach ($list as $menu) {
                    $menus[] = ['value' => $menu['id'], 'label' => $menu['title']];
                }
                return $menus;
            })->filterable(1),
            Form::number('sort', '排序'),
        ]);
        $form->setMethod('post')->setTitle('推荐设置')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload(); setTimeout(function(){parent.layer.close(parent.layer.getFrameIndex(window.name));},800);');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /*
     * 保存推荐
     * */
    public function save_recommend($article_id = 0)
    {
        if (!$article_id) $this->failed('缺少参数');
        $data = Util::postMore([
            ['recommend_id', 0],
            ['sort', 0],
        ]);
        if (!$data['recommend_id']) return Json::fail('请选择推荐');
        $data['add_time'] = time();
        $data['type'] = 1;
        $data['link_id'] = $article_id;
        if (RecommendRelation::be(['type' => 1, 'link_id' => $article_id, 'recommend_id' => $data['recommend_id']])) return Json::fail('已推荐,请勿重复推荐');
        if (RecommendRelation::set($data))
            return Json::successful('推荐成功');
        else
            return Json::fail('推荐失败');
    }
}