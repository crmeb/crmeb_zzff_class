{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-item .special-label {
        width: 50px;
        float: left;
        height: 30px;
        line-height: 38px;
        margin-left: 10px;
        margin-top: 5px;
        border-radius: 5px;
        background-color: #0092DC;
        text-align: center;
    }

    .layui-form-item .special-label i {
        display: inline-block;
        width: 18px;
        height: 18px;
        font-size: 18px;
        color: #fff;
    }

    .layui-form-item .label-box {
        border: 1px solid;
        border-radius: 10px;
        position: relative;
        padding: 10px;
        height: 30px;
        color: #fff;
        background-color: #393D49;
        text-align: center;
        cursor: pointer;
        display: inline-block;
        line-height: 10px;
    }

    .layui-form-item .label-box p {
        line-height: inherit;
    }

    .layui-form-mid {
        margin-left: 18px;
    }

    .m-t-5 {
        margin-top: 5px;
    }

    .edui-default .edui-for-image .edui-icon {
        background-position: -380px 0px;
    }
</style>
<script type="text/javascript" charset="utf-8"
        src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}plug/ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/aliyun-oss-sdk-4.4.4.min.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/lib/plupload-2.1.2/js/plupload.full.min.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/OssUpload.js"></script>
{/block}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <div class="layui-row layui-col-space15" id="app">
        <form action="" class="layui-form">
            <div class="layui-col-md12">
                <div class="layui-card" v-cloak="">
                    <div class="layui-card-header">基本信息</div>
                    <div class="layui-card-body" style="padding: 10px 150px;">
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">专题名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" v-model="formData.title" autocomplete="off"
                                       placeholder="请输入专题名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">分类选择</label>
                            <div class="layui-input-block">
                                <select name="subject_id" v-model="formData.subject_id" lay-search=""
                                        lay-filter="subject_id">
                                    <option value="0">请选分类</option>
                                    <option :value="item.id" v-for="item in subject_list">{{item.name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">专题简介</label>
                            <div class="layui-input-block">
                                <textarea placeholder="请输入专题简介" v-model="formData.abstract"
                                          class="layui-textarea"></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">专题短语</label>
                            <div class="layui-input-block">
                                <textarea placeholder="请输入专题短语" v-model="formData.phrase"
                                          class="layui-textarea"></textarea>
                            </div>
                        </div>
                        <!--<div class="layui-form-item m-t-5" v-if="is_live">
                            <label class="layui-form-label" v-text="'自动回复'"></label>
                            <div class="layui-input-block">
                                <textarea placeholder="用户首次进入直播间的欢迎语" v-model="formData.auto_phrase" class="layui-textarea"></textarea>
                            </div>
                        </div>-->
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">专题排序</label>
                            <div class="layui-input-block">
                                <input type="number" style="width: 20%" name="sort" v-model="formData.sort"
                                       autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5" v-cloak="">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="margin-right: 28px">专题标签</label>
                                <div class="layui-input-inline" style="width: 300px;">
                                    <input type="text" v-model="label" name="price_min" placeholder="最多4个字"
                                           autocomplete="off" class="layui-input" style="float: left;width: 200px">
                                    <p class="special-label" @click="addLabrl"><i class="fa fa-plus"
                                                                                  aria-hidden="true"></i></p>
                                </div>
                            </div>
                            <div class="layui-input-block">
                                <div class="label-box" v-for="(item,index) in formData.label" @click="delLabel(index)">
                                    <p>{{item}}</p>
                                </div>
                            </div>
                            <div class="layui-form-mid layui-word-aux">输入标签名称点击添加+号进行添加;最多写入6个字;点击标签可删除</div>
                        </div>
                        <div class="layui-form-item m-t-5" v-cloak="">
                            <label class="layui-form-label">专题封面</label>
                            <div class="layui-input-block">
                                <div class="upload-image-box" v-if="formData.image" @mouseenter="mask.image = true"
                                     @mouseleave="mask.image = false">
                                    <img :src="formData.image" alt="">
                                    <div class="mask" v-show="mask.image" style="display: block">
                                        <p><i class="fa fa-eye" @click="look(formData.image)"></i><i
                                                    class="fa fa-trash-o" @click="delect('image')"></i></p>
                                    </div>
                                </div>
                                <div class="upload-image" v-show="!formData.image" @click="upload('image')">
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>选择图片</p>
                                </div>
                            </div>
                            <div class="layui-form-item m-t-5" v-cloak="">
                                <label class="layui-form-label">专题banner</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.banner.length"
                                         v-for="(item,index) in formData.banner" @mouseenter="enter(item)"
                                         @mouseleave="leave(item)">
                                        <img :src="item.pic" alt="">
                                        <div class="mask" v-show="item.is_show" style="display: block">
                                            <p><i class="fa fa-eye" @click="look(item)"></i><i class="fa fa-trash-o"
                                                                                               @click="delect('banner',index)"></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="upload-image" v-show="formData.banner.length <= 3"
                                         @click="upload('banner',5)">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item m-t-5" v-cloak="">
                                <label class="layui-form-label">推广海报</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.poster_image"
                                         @mouseenter="mask.poster_image = true" @mouseleave="mask.poster_image = false">
                                        <img :src="formData.poster_image" alt="">
                                        <div class="mask" v-show="mask.poster_image" style="display: block">
                                            <p><i class="fa fa-eye" @click="look(formData.poster_image)"></i><i
                                                        class="fa fa-trash-o" @click="delect('poster_image')"></i></p>
                                        </div>
                                    </div>
                                    <div class="upload-image" v-show="!formData.poster_image"
                                         @click="upload('poster_image')">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item m-t-5" v-cloak="">
                                <label class="layui-form-label">客服二维码</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.service_code"
                                         @mouseenter="mask.service_code = true" @mouseleave="mask.service_code = false">
                                        <img :src="formData.service_code" alt="">
                                        <div class="mask" v-show="mask.service_code" style="display: block">
                                            <p><i class="fa fa-eye" @click="look(formData.service_code)"></i><i
                                                        class="fa fa-trash-o" @click="delect('service_code')"></i></p>
                                        </div>
                                    </div>
                                    <div class="upload-image" v-show="!formData.service_code"
                                         @click="upload('service_code')">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>

                            <div class="layui-form-item m-t-5">
                                <label class="layui-form-label">插入视频</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" v-model="link"
                                           style="width:50%;display:inline-block;margin-right: 10px;" autocomplete="off"
                                           placeholder="请输入视频链接" class="layui-input">
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal"
                                            @click="uploadVideo()">确认添加
                                    </button>
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal"
                                            id="ossupload">上传视频
                                    </button>
                                </div>
                                <input type="file" name="video" v-show="" ref="video">
                                <div class="layui-input-block" style="width: 50%;margin-top: 20px" v-show="is_video">
                                    <div class="layui-progress" style="margin-bottom: 10px">
                                        <div class="layui-progress-bar layui-bg-blue"
                                             :style="'width:'+videoWidth+'%'"></div>
                                    </div>
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-danger"
                                            @click="cancelUpload">取消
                                    </button>
                                </div>
                                <div class="layui-form-mid layui-word-aux">输入链接将视为添加视频直接添加,请确保视频链接的正确性</div>
                            </div>
                            <div class="layui-form-item m-t-5">
                                <label class="layui-form-label">专题内容</label>
                                <div class="layui-input-block">
                                    <textarea id="myEditor"
                                              style="width:100%;height: 500px">{{formData.content}}</textarea>
                                </div>
                            </div>
                            <div class="layui-form-item m-t-5">
                                <label class="layui-form-label">素材选择</label>
                                <div class="layui-input-block">
                                    <input type="hidden" id="check_source_tmp" name="check_source_tmp"/>
                                    <button type="button" class="layui-btn layui-btn-fluid" @click='search_task'>
                                    点击选择素材
                                    </button>
                                    <table class="layui-hide"  id="List" lay-filter="List"></table>
                                    <script type="text/html" id="toolbarDemo" >
                                        <div class="layui-btn-container">
                                            <a class="layui-btn layui-btn-xs" lay-event="getCheckData">确定</a>
                                        </div>
                                    </script>
                                </div>
                            </div>
                            <div class="layui-form-item m-t-5">
                                <label class="layui-form-label">素材展示</label>
                                <div class="layui-input-block">
                                    <input type="hidden" id="check_source_sure" name="check_source_sure"/>
                                    <table class="layui-hide" id="showSourceList" lay-filter="showSourceList"></table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">商品信息</div>
                        <div class="layui-card-body" style="padding: 10px 150px;">
                            <div class="layui-form-item">
                                <label class="layui-form-label">付费方式</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="pay_type" lay-filter="pay_type"
                                           v-model="formData.pay_type" value="1" title="付费">
                                    <input type="radio" name="pay_type" lay-filter="pay_type"
                                           v-model="formData.pay_type" value="0" title="免费">
                                    <!--<input type="radio" name="pay_type" lay-filter="pay_type" v-model="formData.pay_type" value="2" title="加密" >-->
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.pay_type == 1">
                                <label class="layui-form-label">购买金额</label>
                                <div class="layui-input-block">
                                    <input style="width: 20%" type="number" name="money" lay-verify="number"
                                           v-model="formData.money" autocomplete="off" class="layui-input" min="0">
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">会员付费方式</label>
                                    <div class="layui-input-block">
                                        <input type="radio" name="member_pay_type" lay-filter="member_pay_type"
                                               v-model="formData.member_pay_type" value="1" title="付费">
                                        <input type="radio" name="member_pay_type" lay-filter="member_pay_type"
                                               v-model="formData.member_pay_type" value="0" title="免费">
                                    </div>
                                </div>
                                <div class="layui-form-item" v-show="formData.member_pay_type == 1">
                                    <label class="layui-form-label">会员购买金额</label>
                                    <div class="layui-input-block">
                                        <input style="width: 20%" type="number" name="member_money" lay-verify="number"
                                               v-model="formData.member_money" autocomplete="off" class="layui-input" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="layui-form-item" v-show="formData.pay_type == 1">
                                <label class="layui-form-label">拼团是否开启</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_pink" lay-filter="is_pink" v-model="formData.is_pink"
                                           value="0" title="关闭" checked="">
                                    <input type="radio" name="is_pink" lay-filter="is_pink" v-model="formData.is_pink"
                                           value="1" title="开启">
                                </div>
                            </div>

                            <div class="layui-form-item" v-show="formData.is_pink">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="margin-right: 28px">拼团金额</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="pink_money" v-model="formData.pink_money"
                                               autocomplete="off" class="layui-input" min="0">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <label class="layui-form-label">拼团人数</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="pink_number" v-model="formData.pink_number"
                                               autocomplete="off" class="layui-input" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_pink">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="margin-right: 28px">开始时间</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="pink_strar_time" v-model="formData.pink_strar_time"
                                               id="start_time" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <label class="layui-form-label">结束时间</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="pink_end_time" v-model="formData.pink_end_time"
                                               id="end_time" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_pink">
                                <label class="layui-form-label">拼团时间</label>
                                <div class="layui-input-block">
                                    <input style="width: 20%" type="number" v-model="formData.pink_time"
                                           autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_pink">
                                <label class="layui-form-label">模拟成团</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_fake_pink" lay-filter="is_fake_pink"
                                           v-model="formData.is_fake_pink" value="1" title="开启" checked="">
                                    <input type="radio" name="is_fake_pink" lay-filter="is_fake_pink"
                                           v-model="formData.is_fake_pink" value="0" title="关闭">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_fake_pink && formData.is_pink">
                                <label class="layui-form-label">补齐比例</label>
                                <div class="layui-input-block">
                                    <input style="width: 20%" type="number" v-model="formData.fake_pink_number"
                                           autocomplete="off" class="layui-input" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-form-item submit" style="margin-bottom: 10px">
                        <div class="layui-input-block">
                            <button class="layui-btn layui-btn-normal" type="button" @click="save">{$id ?
                                '确认修改':'立即提交'}
                            </button>
                            <button class="layui-btn layui-btn-primary clone" type="button" @click="clone_form">取消
                            </button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var id = {$id},
        special =<?=isset($special) ? $special : "{}"?>,
    sourceCheckList =<?= isset($sourceCheckList) ? $sourceCheckList : "{}"?>;
    var table_date=new Array();//用于保存当前页数据

    var ids=new Array();    //用于保存选中的数据
        require(['vue'], function (Vue) {
            new Vue({
                el: "#app",
                data: {
                    subject_list: [],
                    source_tmp_list: [],//用于子页父业选中素材传值的临时变量
                    source_list: [],
                    formData: {
                        phrase: special.phrase || '',
                        label: special.label || [],
                        abstract: special.abstract || '',
                        title: special.title || '',
                        subject_id: special.subject_id || 0,
                        task_id: 0,
                        image: special.image || '',
                        banner: special.banner || [],
                        poster_image: special.poster_image || '',
                        service_code: special.service_code || '',
                        money: special.money || '',
                        pink_money: special.pink_money || '',
                        pink_number: special.pink_number || 0,
                        pink_strar_time: special.pink_strar_time || '',
                        pink_end_time: special.pink_end_time || '',
                        fake_pink_number: special.fake_pink_number || 0,
                        sort: special.sort || 0,
                        is_pink: special.is_pink || 0,
                        is_fake_pink: special.is_fake_pink || 1,
                        fake_sales: special.fake_sales || 0,
                        browse_count: special.browse_count || 0,
                        pink_time: special.pink_time || 0,
                        content: special.profile ? (special.profile.content || '') : '',
                        pay_type: special.pay_type == 1 ? 1 : 0,
                        check_source_sure: sourceCheckList ? sourceCheckList : "",
                        member_pay_type: special.member_pay_type == 1 ? 1 : 0,
                        member_money: special.member_money || '',
                    },
                    but_title: '上传视频',
                    link: '',
                    label: '',
                    host: ossUpload.host + '/',
                    mask: {
                        poster_image: false,
                        image: false,
                        service_code: false,
                    },
                    ue: null,
                    is_video: false,
                    //上传类型
                    mime_types: {
                        Image: "jpg,gif,png,JPG,GIF,PNG",
                        Video: "mp4,MP4",
                    },
                    videoWidth: 0,
                    //is_live:is_live,
                    uploader: null,
                },
                methods: {
                    //取消
                    cancelUpload: function () {
                        this.uploader.stop();
                        this.is_video = false;
                        this.videoWidth = 0;
                    },
                    //删除图片
                    delect: function (key, index) {
                        var that = this;
                        if (index != undefined) {
                            that.formData[key].splice(index, 1);
                            that.$set(that.formData, key, that.formData[key]);
                        } else {
                            that.$set(that.formData, key, '');
                        }
                    },
                    //查看图片
                    look: function (pic) {
                        $eb.openImage(pic);
                    },
                    //鼠标移入事件
                    enter: function (item) {
                        if (item) {
                            item.is_show = true;
                        } else {
                            this.mask = true;
                        }
                    },
                    //鼠标移出事件
                    leave: function (item) {
                        if (item) {
                            item.is_show = false;
                        } else {
                            this.mask = false;
                        }
                    },
                    changeIMG: function (key, value, multiple) {
                        if (multiple) {
                            var that = this;
                            value.map(function (v) {
                                that.formData[key].push({pic: v, is_show: false});
                            });
                            this.$set(this.formData, key, this.formData[key]);
                        } else {
                            this.$set(this.formData, key, value);
                        }
                    },
                    uploadVideo: function () {
                        if (this.link.substr(0, 7).toLowerCase() == "http://" || this.link.substr(0, 8).toLowerCase() == "https://") {
                            this.setContent(this.link);
                        } else {
                            layList.msg('请输入正确的视频链接');
                        }
                    },
                    setContent: function (link) {
                        this.ue.setContent('<div><video style="width: 100%" src="' + link + '" class="video-ue" controls="controls"><source src="' + link + '"></source></video></div><br>', true);
                    },
                    //上传图片
                    upload: function (key, count) {
                        ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count});
                    },
                    //获取分类
                    get_subject_list: function () {
                        var that = this;
                        layList.baseGet(layList.U({a: 'get_subject_list'}), function (res) {
                            that.$set(that, 'subject_list', res.data);
                            that.$nextTick(function () {
                                layList.form.render('select');
                            })
                        });
                    },
                    /*  callbackdd:function (id){
                  $('#layui-layer-iframe1').contents().find('#guid').val(id);
              },*/
                    delLabel: function (index) {
                        this.formData.label.splice(index, 1);
                        this.$set(this.formData, 'label', this.formData.label);
                    },
                    addLabrl: function () {
                        if (this.label) {
                            if (this.label.length > 6) return layList.msg('您输入的标签字数太长');
                            var length = this.formData.label.length;
                            if (length >= 2) return layList.msg('标签最多添加2个');
                            for (var i = 0; i < length; i++) {
                                if (this.formData.label[i] == this.label) return layList.msg('请勿重复添加');
                            }
                            this.formData.label.push(this.label);
                            this.$set(this.formData, 'label', this.formData.label);
                            this.label = '';
                        }
                    },
                    save: function () {
                        var that = this, banner = new Array();
                        that.formData.content = that.ue.getContent();
                        if (!that.formData.subject_id) return layList.msg('请选择分类');
                        if (Object.keys(that.formData.check_source_sure).length == 0) return layList.msg('请选择素材');
                        if (!that.formData.title) return layList.msg('请输入专题标题');
                        if (!that.formData.abstract) return layList.msg('请输入专题简介');
                        if (!that.formData.phrase) return layList.msg('请输入专题短语');
                        if (!that.formData.label.length) return layList.msg('请输入标签');
                        if (!that.formData.image) return layList.msg('请上传专题封面');
                        if (!that.formData.banner.length) return layList.msg('请上传banner图,最少1张');
                        if (!that.formData.poster_image) return layList.msg('请上传推广海报');
                        if (!that.formData.service_code) return layList.msg('请上传客服二维码');
                        if (!that.formData.content) return layList.msg('请编辑内容在进行保存');
                        if (that.formData.is_pink) {
                            if (!that.formData.pink_money) return layList.msg('请填写拼团金额');
                            if (!that.formData.pink_number) return layList.msg('请填写拼团人数');
                            if (!that.formData.pink_strar_time) return layList.msg('请选择拼团开始时间');
                            if (!that.formData.pink_end_time) return layList.msg('请选择拼团结束时间');
                            if (!that.formData.pink_time) return layList.msg('请填写拼团时间');
                            if (that.formData.is_fake_pink && !that.formData.fake_pink_number) return layList.msg('请填写补齐比例');
                        }
                        if (that.formData.pay_type == 1) {
                            if (!that.formData.money || that.formData.money == 0.00) return layList.msg('请填写购买金额');
                        }
                        if (that.formData.member_pay_type == 1) {
                            if (!that.formData.member_money || that.formData.member_money == 0.00) return layList.msg('请填写会员购买金额');
                        }
                        layList.loadFFF();
                        layList.basePost(layList.U({
                            a: 'save_special',
                            q: {id: id, special_type: '{$special_type}'}
                        }), that.formData, function (res) {
                            layList.loadClear();
                            if (parseInt(id) == 0) {
                                layList.layer.confirm('添加成功,您要继续添加专题吗?', {
                                    btn: ['继续添加', '取消'] //按钮
                                }, function () {
                                    window.location.reload();
                                }, function () {
                                    parent.layer.closeAll();
                                });
                            } else {
                                layList.msg('修改成功', function () {
                                    window.location.href = layList.U({
                                        a: 'index',
                                        p: {type: 1, special_type: '{$special_type}'}
                                    });
                                })
                            }
                        }, function (res) {
                            layList.msg(res.msg);
                            layList.loadClear();
                        });
                    }
                    ,
                    clone_form: function () {
                        var that = this;
                        if (parseInt(id) == 0) {
                            if (that.formData.image.pic) return layList.msg('请先删除上传的图片在尝试取消');
                            if (that.formData.poster_image.pic) return layList.msg('请先删除上传的图片在尝试取消');
                            if (that.formData.banner.length) return layList.msg('请先删除上传的图片在尝试取消');
                            if (that.formData.service_code.pic) return layList.msg('请先删除上传的图片在尝试取消');
                            parent.layer.closeAll();
                        }
                        parent.layer.closeAll();
                        // window.location.href = layList.U({a: 'index', p: {type: 1, special_type: '{$special_type}'}});
                    }
                    ,
                    //素材
                    search_task: function () {
                        var that = this;
                        var table = layui.table;
                        table.render({
                            elem: '#List'
                            ,url:"{:Url('source_list')}?special_id="+id+"&special_type={$special_type}"
                            ,toolbar: '#toolbarDemo' //开启头部工具栏，并为其绑定左侧模板
                            ,defaultToolbar: ['filter', 'exports', 'print', { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
                                title: '提示'
                                ,layEvent: 'LAYTABLE_TIPS'
                                ,icon: 'layui-icon-tips'
                            }]
                            ,cols: [[
                                {type: 'checkbox'},
                                {field: 'id', title: '编号', sort: true,event:'id'},
                                {field: 'title', title: '素材标题'},
                                {field: 'image', title: '封面',templet:'<div><img src="{{ d.image }}"></div>'},
                            ]]
                            ,page: true
                            ,done:function (res,curr,count) {
                                table_date=res.data;
                                    console.log(res);
                                for(var i=0;i< res.data.length;i++){
                                    for (var j = 0; j < ids.length; j++) {
                                        if(res.data[i].id == ids[j].id) {
                                            res.data[i]["LAY_CHECKED"]='true';/*设置勾选*/
                                            /*找到对应数据改变勾选样式*/
                                            var index= res.data[i]['LAY_TABLE_INDEX'];
                                            $('tr[data-index=' + index + '] input[type="checkbox"]').prop('checked', true);
                                            $('tr[data-index=' + index + '] input[type="checkbox"]').next().addClass('layui-form-checked');
                                        }
                                    }
                                }
                                var checkStatus = table.checkStatus('List');/*获得选中的值 和判断是否是全选 isAll true全选 isAlL false 没有全选*/
                                if(checkStatus.isAll){
                                    $('.layui-table-header th[data-field="0"] input[type="checkbox"]').prop('checked', true);
                                    $('.layui-table-header th[data-field="0"] div[class="layui-unselect layui-form-checkbox"]').addClass('layui-form-checked');
                                }
                            }
                        });
                    },
                    show_source_list: function () {
                        var that = this;
                        var table = layui.table, form = layui.form;
                        table.render({
                            elem: '#showSourceList'
                            , cols: [[
                                {field: 'id', title: '编号', sort: true, event: 'id'},
                                {field: 'title', title: '素材标题', edit: 'title'},
                                {field: 'image', title: '封面', templet: '<div><img src="{{ d.image }}"></div>'},
                                {
                                    field: 'pay_status', title: '是否免费', width: '10%', templet: function (d) {
                                        var is_checked = d.pay_status == 0 ? "checked" : "";
                                        return "<input type='checkbox' name='pay_status' lay-skin='switch' value='" + d.id + "' lay-filter='pay_status' lay-text='免费|收费' " + is_checked + ">";
                                    }
                                },
                            ]]
                            , data: that.formData.check_source_sure
                            , page: true
                        });
                        //监听素材是否免费操作
                        form.on('switch(pay_status)', function (obj) {
                            if (that.formData.check_source_sure) {
                                $.each(that.formData.check_source_sure, function (index, value) {
                                    if (value.id == obj.value) {
                                        that.formData.check_source_sure[index].pay_status = obj.elem.checked == true ? 0 : 1;
                                    }
                                })
                            }
                        });
                    },
                    removeArrayRepElement:function(arr) {
                        for (var i = 0; i < arr.length; i++) {
                            for (var j = 0; j < arr.length; j++) {
                                if (arr[i].id == arr[j].id && i != j) {
                                    arr.splice(j, 1);
                                }
                            }
                        }
                        return arr;
                    }
                },
                mounted: function () {
                    var that = this;
                    window.changeIMG = that.changeIMG;
                    layList.date({
                        elem: '#start_time',
                        theme: '#393D49',
                        type: 'datetime',
                        done: function (value) {
                            that.formData.pink_strar_time = value;
                        }
                    });
                    layList.date({
                        elem: '#end_time',
                        theme: '#393D49',
                        type: 'datetime',
                        done: function (value) {
                            that.formData.pink_end_time = value;
                        }
                    });
                    var table = layui.table;
                    table.on('toolbar(List)', function(obj){
                        var checkStatus = table.checkStatus(obj.config.id);
                        switch(obj.event){
                            case 'getCheckData':
                                var data = checkStatus.data;
                                // $("#check_source_tmp",window.parent.document).val(JSON.stringify(data));
                                var source_tmp =JSON.stringify(ids);
                                that.source_tmp_list = JSON.parse(source_tmp);
                                that.formData.check_source_sure = JSON.parse(source_tmp);
                                that.show_source_list();
                                break;
                        };
                    });
                    table.on('checkbox(List)', function (obj) {
                        if(obj.checked==true){
                            if(obj.type=='one'){
                                ids.push(obj.data);
                            }else{
                                for(var i=0;i<table_date.length;i++){
                                    ids.push(table_date[i]);
                                }
                            }
                            ids=that.removeArrayRepElement(ids);
                        }else{
                            if(obj.type=='one'){
                                for(var i=0;i<ids.length;i++){
                                    if(ids[i].id==obj.data.id){
                                        ids.remove(i);
                                    }
                                }
                            }else{
                                for(var i=0;i<ids.length;i++){
                                    for(var j=0;j<table_date.length;j++){
                                        if(ids[i].id==table_date[j].id){
                                            ids.remove(i);
                                        }
                                    }
                                }
                            }
                        }
                    });
                    Array.prototype.remove=function(dx){
                        if(isNaN(dx)||dx>this.length){return false;}
                        for(var i=0,n=0;i<this.length;i++)
                        {
                            if(this[i]!=this[dx]){
                                this[n++]=this[i];
                            }
                        }
                        this.length-=1;
                    };

                    //选择图片
                    function changeIMG(index, pic) {
                        $(".image_img").css('background-image', "url(" + pic + ")");
                        $(".active").css('background-image', "url(" + pic + ")");
                        $('#image_input').val(pic);
                    }

                    //选择图片插入到编辑器中
                    window.insertEditor = function (list) {
                        that.ue.execCommand('insertimage', list);
                    }

                    this.$nextTick(function () {
                        layList.form.render();
                        //实例化编辑器
                        UE.registerUI('imagenone', function (editor, name) {
                            var $btn = new UE.ui.Button({
                                name: 'image',
                                onclick: function () {
                                    ossUpload.createFrame('选择图片', {fodder: 'editor'});
                                },
                                title: '选择图片'
                            });

                            return $btn;

                        });
                        that.ue = UE.getEditor('myEditor');
                    });
                    //获取科目
                    that.get_subject_list();
                    //获取素材展示
                    that.show_source_list();
                    //图片上传和视频上传
                    layList.form.on('radio(is_pink)', function (data) {
                        that.formData.is_pink = parseInt(data.value);
                    });
                    layList.form.on('radio(is_remind)', function (data) {
                        that.formData.is_remind = parseInt(data.value);
                    });
                    layList.form.on('radio(is_recording)', function (data) {
                        that.formData.is_recording = parseInt(data.value);
                    });
                    layList.form.on('radio(pay_type)', function (data) {
                        that.formData.pay_type = parseInt(data.value);
                        if (that.formData.pay_type != 1) {
                            that.formData.is_pink = 0;
                            that.formData.member_pay_type = 0;
                            that.formData.member_money = 0;
                        };
                        that.$nextTick(function () {
                            layList.form.render('radio');
                        });
                    });
                    layList.form.on('radio(member_pay_type)', function (data) {
                        that.formData.member_pay_type = parseInt(data.value);
                        if (that.formData.member_pay_type != 1) {
                            that.formData.member_money = 0;
                        };
                        that.$nextTick(function () {
                            layList.form.render('radio');
                        });
                    });
                    layList.select('subject_id', function (obj) {
                        that.formData.subject_id = obj.value;
                    });
                    layList.form.on('radio(is_fake_pink)', function (data) {
                        that.formData.is_fake_pink = parseInt(data.value);
                    });

                    that.$nextTick(function () {
                        that.uploader = ossUpload.upload({
                            id: 'ossupload',
                            FilesAddedSuccess: function () {
                                that.is_video = true;
                            },
                            uploadIng: function (file) {
                                that.videoWidth = file.percent;
                            },
                            success: function (res) {
                                layList.msg('上传成功');
                                that.videoWidth = 0;
                                that.is_video = false;
                                that.setContent(res.url);
                            },
                            fail: function (err) {
                                that.videoWidth = 0;
                                that.is_video = false;
                                layList.msg(err);
                            }
                        })
                    });
                }
            })
        })

</script>
{/block}