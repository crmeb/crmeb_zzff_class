<?php

namespace app\admin\controller\special;

use app\admin\controller\AuthController;
//use app\admin\controller\setting\SystemConfig;
use app\admin\model\special\SpecialBarrage;
use app\admin\model\system\SystemConfig;
use app\admin\model\order\StoreOrder as StoreOrderModel;
use app\admin\model\live\LiveBarrage;
use app\admin\model\live\LiveHonouredGuest;
use app\admin\model\live\LiveStudio;
use app\admin\model\live\LiveUser;
use app\admin\model\special\Grade;
use app\admin\model\special\Special as SpecialModel;
use app\admin\model\special\Special;
use app\admin\model\special\SpecialContent;
use app\admin\model\special\SpecialCourse;
use app\admin\model\special\SpecialSource;
use app\admin\model\special\SpecialSubject;
use app\admin\model\special\SpecialTask;
use app\admin\model\system\Recommend;
use app\admin\model\system\RecommendRelation;
use app\admin\model\user\User;
use app\wap\model\user\WechatUser;
use service\JsonService as Json;
use service\JsonService;
use service\SystemConfigService;
use service\UtilService;
use service\WechatTemplateService;
use think\Db;
use think\Exception;
use service\FormBuilder as Form;
use Api\AliyunLive as ApiAliyunLive;
use think\Url;
use \GatewayWorker\Lib\Gateway;

/**课程管理-图文专题控制器
 * Class Special
 * @package app\admin\controller\special
 */
class SpecialType extends AuthController
{

    /** 图文专题列表模板渲染
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($subject_id = 0)
    {
        $special_type = $this->request->param('special_type');
        $this->assign([
            'activity_type' => $this->request->param('activity_type', 1),
            'subject_id' => $subject_id,
            'special_title' => SPECIAL_TYPE[$special_type],
            'special_type' => $special_type,
            'subject_list' => SpecialSubject::getSubjectAll()
        ]);
        $template = $this->switch_template($special_type, request()->action());
        if (!$template) $template = "";
        return $this->fetch($template);
    }

    /**
     * 获取图文专题列表数据
     */
    public function list($special_type = 6)
    {

        $where = UtilService::getMore([
            ['subject_id', 0],
            ['page', 1],
            ['limit', 20],
            ['title', ''],
            ['start_time', ''],
            ['end_time', ''],
            ['order', ''],
            ['is_show', ''],
        ]);
        $where['type'] = $special_type;
        return Json::successlayui(SpecialModel::getSpecialList($where));
    }

    /**
     * 添加页面
     * @param int $id
     * @param int $is_live
     * @return mixed|void
     */
    public function add($id = 0)
    {
        $special_type = $this->request->param('special_type');
        if ($id) {
            $special = SpecialModel::getOne($id, $special_type == SPECIAL_LIVE ? $special_type : 0);

            if ($special === false) {
                return $this->failed(SpecialModel::getErrorInfo('您修改的专题不存在'));
            }
            $specialSourceId = SpecialSource::getSpecialSource($id)->toArray();
            $sourceCheckList = array();
            if ($specialSourceId) {
                foreach ($specialSourceId as $k => $v) {
                    if ($special_type == SPECIAL_COLUMN) {
                        $task_list = Special::where(['id' => $v['source_id']])->find();
                    }else{
                        $task_list = SpecialTask::where(['id' => $v['source_id']])->find();
                    }
                    if(count($task_list)>0){
                        $task_list['is_check'] = 1;
                        $task_list['pay_status'] = $v['pay_status'];
                        $sourceCheckList[$k] = $task_list;
                    }

                }
            }
            /* if ($specialSourceId = array_column($specialSourceId, 'source_id')) {
                 $where['id'] = $specialSourceId;
                 $specialSourceList = SpecialTask::where($where)->field('id, title')->order('sort desc')->select();
             }*/

            list($specialInfo, $liveInfo) = $special;
            $this->assign('liveInfo', json_encode($liveInfo));
            $this->assign('special', json_encode($specialInfo));
            $this->assign('sourceCheckList', json_encode($sourceCheckList));
        }
        $this->assign('special_type', $special_type);
        $this->assign('id', $id);
        $template = $this->switch_template($special_type, request()->action());
        if (!$template) $template = "";
        return $this->fetch($template);
    }

    /**
     * 素材页面渲染
     * @return
     * */
    public function source_index($coures_id = 0)
    {
        $special_type = $this->request->param('special_type');
        $this->assign('coures_id', $coures_id);
        $this->assign('special_title', SPECIAL_TYPE[$special_type]);
        $this->assign('special_type', $special_type);//图文专题
        $this->assign('activity_type', $this->request->param('activity_type', 1));
        $this->assign('special_id', SpecialCourse::where('id', $coures_id)->value('special_id'));
        $this->assign('specialList', \app\admin\model\special\Special::PreWhere()->field(['id', 'title'])->select());
        $template = $this->switch_template($special_type, request()->action());
        if (!$template) $template = "";
        return $this->fetch($template);
    }

    /**
     * 图文专题素材列表获取
     * @return json
     * */
    public function source_list()
    {
        $where = UtilService::getMore([
            ['page', 1],
            ['is_show', ''],
            ['limit', 20],
            ['title', ''],
            ['order', ''],
            ['special_id', 0],
            ['special_type', 0],
            ['check_source_sure', '']
        ]);
        $special_source = array();
        if (isset($where['special_id']) && $where['special_id']) {
            $special_source = SpecialSource::where(['special_id' => $where['special_id']])->select()->toArray();
            $special_source = array_column($special_source, 'pay_status', 'source_id');
        }
        /* if ($where['special_type'] == SPECIAL_COLUMN) {//专栏
             $sourceList = Special::where($where)->whereIn('type',[SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO])->field('id, title, type')->order('type desc, sort desc')->select();
             if ($sourceList) {}
         }else{

         }*/

        $special_task = SpecialTask::getTaskList2($where);
        if (isset($special_task['data']) && $special_task['data']) {
            foreach ($special_task['data'] as $k => $v) {
                if (array_key_exists($v['id'], $special_source)) {
                    $special_task['data'][$k]['is_check'] = 1;
                    $special_task['data'][$k]['LAY_CHECKED'] = true;
                    if ($special_source[$v['id']] && $special_source[$v['id']] == PAY_MONEY) {
                        $special_task['data'][$k]['pay_status'] = PAY_MONEY;
                    } else {
                        $special_task['data'][$k]['pay_status'] = PAY_NO_MONEY;
                    }
                } else {
                    $special_task['data'][$k]['is_check'] = 0;
                    $special_task['data'][$k]['pay_status'] = PAY_NO_MONEY;
                }
            }
        }

        $special_task['source'] = $special_source;
        return JsonService::successlayui($special_task);

    }

    /**
     * 添加和修改素材
     * @param int $id 修改
     * @return
     * */
    public function add_source($id = 0)
    {
        $special_type = $this->request->param("special_type");
        $this->assign('id', $id);
        if ($id) {
            $task = SpecialTask::get($id);
            $task->detail = htmlspecialchars_decode($task->detail);
            $task->content = htmlspecialchars_decode($task->content);
            $task->image = get_key_attr($task->image);
            //$task->link = get_key_attr($task->link);
            // $this->assign('special_id', $task->special_id);
            //print_r($task->image);die;
            $this->assign('special', $task);
        }
        $this->assign('special_type', $special_type);
        // $specialList = \app\admin\model\special\Special::PreWhere()->field(['id', 'title', 'is_live'])->select();
        //$this->assign('specialList', $specialList);
        $template = $this->switch_template($special_type, request()->action());
        if (!$template) $template = "";
        return $this->fetch($template);
    }

    /**
     * 添加和修改素材
     * @param int $id 修改
     * @return json
     * */
    public function save_source($id = 0)
    {
        $special_type = $this->request->param('special_type');
        if (!$special_type) return JsonService::fail('专题类型参数缺失');
        //print_r($_POST);die;
        $data = UtilService::postMore([
            ['title', ''],
            ['image', ''],
            ['content', ''],
            ['detail', ''],
            ['image', ''],
             ['link', ''],
            //['play_count', 0],
            // ['special_id', 0],
            ['sort', 0],
            // ['is_pay', 0],
            ['is_show', 1],
        ]);
        $data['type'] = $special_type;//图文素材
        // if ($data['special_id'] === 0) return JsonService::fail('请选择课程再尝试添加');
        if (!$data['title']) return JsonService::fail('请输入课程标题');
        if (!$data['image']) return JsonService::fail('请上传封面图');
        // if (!$data['link']) return JsonService::fail('请上传或者添加视频');
        if ($id) {
            unset($data['is_show']);
            SpecialTask::update($data, ['id' => $id]);
            return JsonService::successful('修改成功');
        } else {
            $data['add_time'] = time();
            if (SpecialTask::set($data))
                return JsonService::successful('添加成功');
            else
                return JsonService::fail('添加失败');
        }
    }

    /**
     * 快速编辑
     * @param string $field 字段名
     * @param int $id 修改的主键
     * @param string value 修改后的值
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '', $model_type)
    {
        $field == '' || $id == '' || $value == '' || $model_type == '' && JsonService::fail('缺少参数');
        $model_type = $this->switch_model($model_type);
        if (!$model_type) JsonService::fail('缺少参数');
        $res = $model_type::where(['id' => $id])->update([$field => $value]);
        if ($res)
            return JsonService::successful('保存成功');
        else
            return JsonService::fail('保存失败');
    }

    /**根据标识选着模型对象
     * @param $model_type 表名
     * @return Special|SpecialTask|bool
     */
    protected function switch_model($model_type)
    {
        if (!$model_type) {
            return false;
        }
        switch ($model_type) {
            case 'task':
                return new SpecialTask();
                break;
            case 'special':
                return new Special();
                break;
            case 'source':
                return new SpecialSource();
            default:
                return false;
        }
    }

    /**
     * 编辑详情
     * @return mixed
     */
    public function update_content($id = 0)
    {
        $field = $this->request->param('field');
        $special_type = $this->request->param('special_type');
        if (!$special_type) {
            return $this->failed('专题类型丢失 ');
        }
        if (!$id) {
            return $this->failed('缺少id ');
        }
        if (!$field) {
            return $this->failed('缺少要修改的字段参数 ');
        }
        try {
            $this->assign([
                'action' => Url::build('save_content', ['id' => $id, 'field' => $field]),
                'field' => $field,
                'contentOrDetail' => htmlspecialchars_decode(SpecialTask::where('id', $id)->value($field))
            ]);
            $template = $this->switch_template($special_type, request()->action());
            if (!$template) $this->failed('模板查询异常 ');
            return $this->fetch($template);
        } catch (\Exception $e) {
            return $this->failed('异常错误 ');
        }

    }

    /**
     * @param $id
     * @throws \think\exception\DbException
     */
    public function save_content($id, $field)
    {
        $content = $this->request->post($field, '');
        $task = SpecialTask::get($id);
        if (!$field) return JsonService::fail('修改项缺失');
        if (!$task) {
            return JsonService::fail('修改得素材不存在');
        }
        $task->$field = htmlspecialchars($content);

        if ($task->save()) {
            return JsonService::successful('保存成功');
        } else {

            return JsonService::fail('保存失败或者您没有修改什么');
        }
    }


    /**
     * @param int $grade_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_subject_list($grade_id = 0)
    {
        if ($grade_id) {
            $where['grade_id'] = $grade_id;
        }
        $where['is_show'] = 1;
        $subjectlist = SpecialSubject::where($where)->order('sort desc')->select();
        return JsonService::successful($subjectlist);
    }

    /**获取素材列表
     * @param bool $type
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_special_source_list()
    {
        $special_type = $this->request->param('special_type');
        $where['is_show'] = 1;
        if ($special_type && is_numeric($special_type) && $special_type != SPECIAL_COLUMN) {
            $where['type'] = $special_type;
        }

        if ($special_type == SPECIAL_COLUMN) {//专栏
            $sourceList = Special::where($where)->whereIn('type', [SPECIAL_IMAGE_TEXT, SPECIAL_AUDIO, SPECIAL_VIDEO])->field('id, title, type')->order('type desc, sort desc')->select();
            if ($sourceList) {
                foreach ($sourceList as $k => $v) {
                    $sourceList[$k]['title'] = SPECIAL_TYPE[$v['type']] . "--" . $v['title'];
                }
            }
        } else {
            $sourceList = SpecialTask::where($where)->field('id, title')->order('sort desc')->select();
        }

        return JsonService::successful($sourceList->toArray());
    }

    /**
     * 编辑和新增
     *
     * @return json
     */
    public function save_special($id = 0)
    {
        $special_type = $this->request->param('special_type');
        if (!$special_type || !is_numeric($special_type)) return JsonService::fail('专题类型参数缺失');
        $data = UtilService::postMore([
            ['title', ''],
            ['abstract', ''],
            ['subject_id', 0],
            ['fake_sales', 0],
            ['browse_count', 0],
            ['label', []],
            ['image', ''],
            ['banner', []],
            ['poster_image', ''],
            ['service_code', ''],
            ['money', 0],
            ['content', ''],
            ['is_pink', 0],
            ['pink_money', 0],
            ['pink_number', 0],
            ['pink_time', 0],
            ['pink_strar_time', ''],
            ['pink_end_time', ''],
            ['phrase', ''],
            ['is_fake_pink', 0],
            ['sort', 0],
            ['fake_pink_number', 0],
             ['member_money', 0],
             ['member_pay_type', MEMBER_PAY_MONEY],
            ['pay_type', PAY_MONEY],//支付方式：免费、付费、密码
        ]);
        $data['type'] = $special_type;
        //$data['source_right_list'] = isset($_POST['check_source_sure']) ? $_POST['check_source_sure'] : "";
        $data['check_source_sure'] = isset($_POST['check_source_sure']) ? $_POST['check_source_sure'] : "";
        if ($special_type == SPECIAL_LIVE) {
            $liveInfo = UtilService::postMore([
                ['is_remind', 1],//开播提醒
                ['remind_time', 0],//开播提醒时间
                ['live_time', ''],//直播开始时间
                ['live_duration', 0],//直播时长 单位：分钟
                ['auto_phrase', ''],//首次进入直播间欢迎词
                ['password', ''],//密码（密码访问模式）
                ['is_recording', ''],//是否录制视频
            ]);
        }
        if (!$data['subject_id']) return Json::fail('请选择分类');
        if ($special_type != SPECIAL_LIVE) {
            if (!$data['check_source_sure']) return Json::fail('请选择素材');
        }
        if (!$data['title']) return Json::fail('请输入专题标题');
        if (!$data['abstract']) return Json::fail('请输入专题简介');
        if (!count($data['label'])) return Json::fail('请输填写标签');
        if (!count($data['banner'])) return Json::fail('请上传banner图');
        if (!$data['image']) return Json::fail('请上传专题封面图');
        if (!$data['poster_image']) return Json::fail('请上传推广海报');
        if (!$data['service_code']) return Json::fail('请上传客服二维码');
        if (!$data['phrase']) return Json::fail('请填写短语！');
        if ($data['pay_type'] == PAY_MONEY && ($data['money'] == '' || $data['money'] == 0.00 || $data['money'] < 0)) return Json::fail('购买金额未填写或者金额非法');
        if ($data['member_pay_type'] == MEMBER_PAY_MONEY && ($data['member_money'] == '' || $data['member_money'] == 0.00 || $data['member_money'] < 0)) return Json::fail('会员购买金额未填写或金额非法');
        if ($data['pay_type'] != PAY_MONEY) {
            $data['money'] = 0;
        }
        if ($data['member_pay_type'] != MEMBER_PAY_MONEY) {
            $data['member_money'] = 0;
        }

        if ($data['is_pink']) {
            if (!$data['pink_money'] || $data['pink_money'] == 0.00 || $data['pink_money'] < 0) return Json::fail('拼团金额未填写或者金额非法');
            if (!$data['pink_number'] || $data['pink_number'] <= 0) return Json::fail('拼团人数未填写或拼团人数非法');
            if (!$data['pink_strar_time']) return Json::fail('请填选择拼团开始时间');
            if (!$data['pink_end_time']) return Json::fail('请填选择拼团结束时间');
            if ($data['pink_end_time'] < $data['pink_strar_time']) return Json::fail('拼团时间范围非法');
            if (!$data['pink_time'] || $data['pink_time'] < 0) return Json::fail('拼团时间未填写或时间非法');
            if (($data['is_fake_pink'] && !$data['fake_pink_number']) || ($data['is_fake_pink'] && $data['fake_pink_number'] < 0)) return Json::fail('虚拟拼团比例未填写或者比例非法');
        }
        $content = htmlspecialchars($data['content']);
        $data['label'] = json_encode($data['label']);
        $data['pink_strar_time'] = strtotime($data['pink_strar_time']);
        $data['pink_end_time'] = strtotime($data['pink_end_time']);
        if ($special_type == SPECIAL_LIVE) {
            $liveInfo['live_title'] = $data['title'];
            $liveInfo['studio_pwd'] = $liveInfo['password'];
            if (strlen($liveInfo['studio_pwd']) > 32) return Json::fail('密码长度不能超过32位');
            $liveInfo['start_play_time'] = $liveInfo['live_time'];
            $liveInfo['stop_play_time'] = date('Y-m-d H:i:s', bcadd(strtotime($liveInfo['live_time']), bcmul($liveInfo['live_duration'], 60)));
            $liveInfo['live_introduction'] = $data['abstract'];
            unset($liveInfo['live_time'], $liveInfo['password']);
            $cacheModel = Db::name('cache');
            $aliyunLive = \Api\AliyunLive::instance([
                'AccessKey' => SystemConfigService::get('accessKeyId'),
                'AccessKeySecret' => SystemConfigService::get('accessKeySecret'),
                'OssEndpoint' => SystemConfigService::get('aliyun_live_end_point'),
                'OssBucket' => SystemConfigService::get('aliyun_live_oss_bucket'),
                'appName' => SystemConfigService::get('aliyun_live_appName'),
                'payKey' => SystemConfigService::get('aliyun_live_play_key'),
                'key' => SystemConfigService::get('aliyun_live_push_key'),
                'playLike' => SystemConfigService::get('aliyun_live_playLike'),
                'rtmpLink' => SystemConfigService::get('aliyun_live_rtmpLink'),
            ]);
            if (!$cacheModel->where('key', 'LiveNotifyUrl')->count()) {
                try {
                    $res = $aliyunLive->setLiveNotifyUrl(SystemConfigService::get('site_url') . Url::build('live/index/serve'))->executeResponse();
                    if ($res) {
                        $cacheModel->insert(['key' => 'LiveNotifyUrl', 'add_time' => time()]);
                    }
                } catch (\Throwable $e) {
                    echo $e->getMessage();
                }
            }
            if (!$cacheModel->where('key', 'liveRecordConfig')->count()) {
                try {
                    $res = $aliyunLive->liveRecordConfig()->executeResponse();
                    if ($res) {
                        $cacheModel->insert(['key' => 'liveRecordConfig', 'add_time' => time()]);
                    }
                } catch (\Throwable $e) {
                    echo $e->getMessage();
                }
            }
        }
        $banner = [];
        $res3 = false;
        SpecialModel::beginTrans();
        try {
            foreach ($data['banner'] as $item) {
                $banner[] = $item['pic'];
            }
            $sourceCheckList = $data['check_source_sure'];
            unset($data['check_source_sure']);
            $data['banner'] = json_encode($banner);
            unset($data['content']);
            if ($id) {
                SpecialModel::update($data, ['id' => $id]);
                SpecialContent::update(['content' => $content], ['special_id' => $id]);
                SpecialModel::commitTrans();
                if ($sourceCheckList) {
                    $save_source = SpecialSource::saveSpecialSource($sourceCheckList, $id);
                    if (!$save_source) return Json::fail('添加失败');
                }
                if ($special_type == 4) {
                    LiveStudio::update($liveInfo, ['special_id' => $id]);
                }
                return Json::successful('修改成功');
            } else {
                $data['add_time'] = time();
                $data['is_show'] = 1;
                $data['is_fake_pink'] = $data['is_pink'] ? $data['is_fake_pink'] : 0;
                $res1 = SpecialModel::insertGetId($data);
                $res2 = SpecialContent::set(['special_id' => $res1, 'content' => $content, 'add_time' => time()]);
                if ($sourceCheckList) {
                    $res3 = SpecialSource::saveSpecialSource($sourceCheckList, $res1);
                }
                if ($special_type == SPECIAL_LIVE) {
                    $liveInfo['special_id'] = $res1;
                    $liveInfo['stream_name'] = LiveStudio::getliveStreamName();
                    $liveInfo['live_image'] = $data['image'];
                    $res3 = LiveStudio::set($liveInfo);
                }

                if ($res1 && $res2 && $res3) {
                    SpecialModel::commitTrans();
                    return Json::successful('添加成功');
                } else {
                    SpecialModel::rollbackTrans();
                    return Json::fail('添加失败');
                }
            }
        } catch (\Exception $e) {
            SpecialModel::rollbackTrans();
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 拼团设置
     * @param int $special_id
     * @return mixed
     * @throws \FormBuilder\exception\FormBuilderException
     * @throws \think\exception\DbException
     */
    public function pink($special_id = 0)
    {
        if (!$special_id) $this->failed('缺少参数');
        $special = SpecialModel::get($special_id);
        if (!$special) $this->failed('没有查到此专题');
        if ($special->is_del) $this->failed('此专题已删除');
        $form = [
            Form::input('title', '专题标题', $special->title)->disabled(true),
            Form::number('pink_money', '拼团金额', $special->pink_money),
            Form::number('pink_number', '拼团人数', $special->pink_number),
            Form::number('pink_time', '拼团时效', $special->pink_time ? $special->pink_time : 24),
            Form::dateTimeRange('pink_time_new', '拼团时间', $special->pink_strar_time, $special->pink_end_time),
            Form::radio('is_fake_pink', '开启虚拟拼团', $special->is_fake_pink)->options([['label' => '开启', 'value' => 1], ['label' => '关闭', 'value' => 0]]),
            Form::number('fake_pink_number', '补齐比例', $special->fake_pink_number),
        ];
        $form = Form::make_post_form('开启拼团设置', $form, Url::build('save_pink', ['special_id' => $special_id]), 2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**保存拼团
     * @param $special_id
     */
    public function save_pink($special_id)
    {
        if (!$special_id) $this->failed('缺少参数');
        $data = UtilService::postMore([
            ['pink_money', 0],
            ['pink_number', 0],
            ['pink_time', 0],
            ['pink_time_new', []],
            ['is_fake_pink', 0],
            ['fake_pink_number', 0],
        ]);
        if (!$data['pink_number']) return Json::fail('拼团人数不能为0');
        if (!$data['pink_time']) return Json::fail('拼团时效不能为0');
        if (!count($data['pink_time_new'])) return Json::fail('请设置拼团时间');
        if ($data['is_fake_pink'] && !$data['fake_pink_number']) return Json::fail('请设置虚拟拼团比例');
        if ($data['is_fake_pink'] != 1) {
            $data['fake_pink_number'] = 0;
        }
        $data['is_pink'] = 1;
        if (is_array($data['pink_time_new']) && isset($data['pink_time_new'][0]) && $data['pink_time_new'][1]) {
            $data['pink_strar_time'] = strtotime($data['pink_time_new'][0]);
            $data['pink_end_time'] = strtotime($data['pink_time_new'][1]);
        }
        unset($data['pink_time_new']);
        SpecialModel::update($data, ['id' => $special_id]);
        return Json::successful('保存成功');
    }

    /**删除指定专题和素材
     * @param int $id修改的主键
     * @param $model_type要修改的表
     * @throws \think\exception\DbException
     */

    public function delete($id = 0, $model_type = false)
    {
        if (!$id || !isset($model_type) || !$model_type) return JsonService::fail('缺少参数');
        $model_table = $this->switch_model($model_type);
        if (!$model_table) return JsonService::fail('缺少参数');
        try {
            $res_get = $model_table::get($id);
            $model_table::startTrans();
            if (!$res_get) return JsonService::fail('删除的数据不存在');
            $res_del = $res_get->delete();
            if ($model_type == 'special' && $res_del) {
                $model_source = $this->switch_model('source');
                $get_source = $model_source::where('special_id', $id)->value('id');
                if ($get_source) {
                    $del_source = $model_source::where('special_id', $id)->delete();
                }
            }
            $model_table::commit();
            return JsonService::successful('删除成功');
        } catch (\Exception $e) {
            $model_table::rollback();
            return JsonService::fail(SpecialTask::getErrorInfo('删除失败' . $e->getMessage()));
        }

    }
    /**转换专题
     * @param int $id修改的主键
     * @param $model_type要修改的表
     * @throws \think\exception\DbException
     */

    public function turnTo($id = 0, $model_type = false,$type=1)
    {
        if (!$id || !isset($model_type) || !$model_type) return JsonService::fail('缺少参数');
        $model_table = $this->switch_model($model_type);
        if (!$model_table) return JsonService::fail('缺少参数');
        try {
            $res_get = $model_table::get($id);
            $model_table::startTrans();
            if (!$res_get) return JsonService::fail('转换的数据不存在');
            $res= $model_table::where('id',$id)->update(['type'=>$type]);
            $model_table::commit();
            return JsonService::successful('转换成功');
        } catch (\Exception $e) {
            $model_table::rollback();
            return JsonService::fail(SpecialTask::getErrorInfo('转换失败' . $e->getMessage()));
        }

    }

    /**
     * 添加推荐
     * @param int $special_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function recommend($special_id = 0)
    {
        if (!$special_id) $this->failed('缺少参数');
        $special = SpecialModel::get($special_id);
        if (!$special) $this->failed('没有查到此专题');
        if ($special->is_del) $this->failed('此专题已删除');
        $form = Form::create(Url::build('save_recommend', ['special_id' => $special_id]), [
            Form::select('recommend_id', '推荐')->setOptions(function () use ($special_id) {
                $list = Recommend::where(['is_show' => 1])->where('is_fixed', 0)->field('title,id')->order('sort desc,add_time desc')->select();
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

    /**
     * 保存推荐
     * @param int $special_id
     * @throws \think\exception\DbException
     */
    public function save_recommend($special_id = 0)
    {
        if (!$special_id) $this->failed('缺少参数');
        $data = UtilService::postMore([
            ['recommend_id', 0],
            ['sort', 0],
        ]);
        if (!$data['recommend_id']) return Json::fail('请选择推荐');
        $recommend = Recommend::get($data['recommend_id']);
        if (!$recommend) return Json::fail('导航菜单不存在');
        $data['add_time'] = time();
        $data['type'] = $recommend->type;
        $data['link_id'] = $special_id;
        if (RecommendRelation::be(['type' => $recommend->type, 'link_id' => $special_id, 'recommend_id' => $data['recommend_id']])) return Json::fail('已推荐,请勿重复推荐');
        if (RecommendRelation::set($data))
            return Json::successful('推荐成功');
        else
            return Json::fail('推荐失败');
    }

    /**专题编辑内素材列表
     * @param int $coures_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function search_task($coures_id = 0)
    {

        $special_type = $this->request->param('special_type');
        $special_id = $this->request->param('special_id');
        $check_source = $this->request->param('check_source_sure');
        $this->assign('coures_id', $coures_id);
        $this->assign('special_title', SPECIAL_TYPE[$special_type]);
        $this->assign('special_type', $special_type);//图文专题
        $this->assign('activity_type', $this->request->param('activity_type', 1));
        //$this->assign('special_id', SpecialCourse::where('id', $coures_id)->value('special_id'));
        $this->assign('special_id', $special_id);
        $this->assign('specialList', \app\admin\model\special\Special::PreWhere()->field(['id', 'title'])->select());
        return $this->fetch('special/task/search_task');
    }

    /**
     * 专题弹幕列表和添加
     * */
    public function special_barrage()
    {
        $this->assign([
            'type' => $this->request->param('type', 1),
            'is_layui' => true,
            'open_barrage' => SystemConfig::getValue('open_barrage'),
        ]);
        return $this->fetch('special/barrage/special_barrage');
    }
    /**
     * 获取专题弹幕列表
     * */
    public function get_barrage_list($page = 1, $limit = 22)
    {
        $list = SpecialBarrage::where('is_show', 1)->order('sort desc,id desc')->page((int)$page, (int)$limit)->select();
        $list = count($list) ? $list->toArray() : [];
        $count = SpecialBarrage::where('is_show', 1)->count();
        return JsonService::successful(compact('list', 'count'));
    }

    /**
     * 删除某个弹幕
     * @param int $id 弹幕id
     * */
    public function del_barrage($id = 0)
    {
        if (SpecialBarrage::del($id))
            return JsonService::successful('删除成功');
        else
            return JsonService::fail('删除失败');
    }

    /**
     * 保存专题弹幕
     * */
    public function save_barrage($id = 0)
    {
        $data = UtilService::postMore([
            ['nickname', ''],
            ['avatar', ''],
            ['action', 0],
        ]);
        if (!$data['nickname']) return JsonService::fail('请填写用户昵称');
        if (!$data['avatar']) return JsonService::fail('请上传用户图像');
        if (!$data['action']) return JsonService::fail('请勾选动作类型');
        if ($id) {
            SpecialBarrage::edit($data, $id);
            return JsonService::successful('修改成功');
        } else {
            $data['add_time'] = time();
            if (SpecialBarrage::set($data))
                return JsonService::successful('添加成功');
            else
                return JsonService::fail('添加失败');
        }

    }

    /**
     * 设置虚拟用户弹幕是否开启
     * */
    public function set_barrage_show($value = 0, $key_nime = '')
    {
        if (!$key_nime) return JsonService::fail('缺少参数');
        $confing = SystemConfig::where(['menu_name' => $key_nime])->find();
        if ($confing) {
            SystemConfig::edit(['value' => json_encode($value)], $confing->id);
            return JsonService::successful('操作成功');
        } else {
            $res = SystemConfig::set([
                'menu_name' => $key_nime,
                'type' => 'radio',
                'parameter' => "1=开启\n0=关闭",
                'value' => '1',
                'config_tab_id' => 1,
                'upload_type' => 0,
                'width' => '100%',
                'info' => '虚拟用户专题弹幕开关',
                'desc' => '虚拟用户专题弹幕开关',
                'sort' => 0,
                'status' => 1
            ]);
            if ($res)
                return JsonService::successful('操作成功');
            else
                return JsonService::fail('操作失败');
        }
    }

    /**渲染模板
     * @param $special_type
     * @param $template_type
     * @return bool|string|void
     */
    protected function switch_template($special_type, $template_type)
    {
        if (!$special_type || !$template_type) {
            return false;
        }
        switch ($special_type) {
            case 1:
                return 'special/image_text/' . $template_type;
                break;
            case 2:
                return 'special/audio_video/' . $template_type;
                break;
            case 3:
                return 'special/audio_video/' . $template_type;
                break;
            case 4:
                return 'special/live/' . $template_type;
                break;
            case 5:
                return 'special/column/' . $template_type;
                break;
            default:
                return $this->failed('没有对应模板 ');
        }
    }


}