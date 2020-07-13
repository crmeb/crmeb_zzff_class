{extend name="public/container"}
{block name="head_top"}
<link href="{__ADMIN_PATH}plug/umeditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
<link href="{__ADMIN_PATH}module/wechat/news/css/style.css" type="text/css" rel="stylesheet">
<link href="{__FRAME_PATH}css/plugins/chosen/chosen.css" rel="stylesheet">
<script type="text/javascript" src="{__ADMIN_PATH}plug/umeditor/third-party/template.min.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/umeditor/umeditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/umeditor/umeditor.min.js"></script>
<script src="{__ADMIN_PATH}frame/js/ajaxfileupload.js"></script>
<script src="{__ADMIN_PATH}plug/validate/jquery.validate.js"></script>
<script src="{__FRAME_PATH}js/plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" charset="utf-8"
        src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}plug/ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/aliyun-oss-sdk-4.4.4.min.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/lib/plupload-2.1.2/js/plupload.full.min.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/OssUpload.js"></script>
<style>
    .wrapper-content {
        padding: 0 !important;
    }
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
    .layui-input-block{line-height: 36px;}
    .layui-form-select dl {z-index: 1000;}
</style>
{/block}
{block name="content"}
<div class="row">
    <div class="col-sm-12 panel panel-default" id="app">
        <div class="panel-body" style="padding: 30px">
            <form class="form-horizontal" id="signupForm">
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon">标题</span>
                            <input maxlength="64" placeholder="请在这里输入标题" name="title" class="layui-input" id="title"  v-model="formData.title">
                        </div>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">活动报名时间</label>
                    <div class="layui-input-inline" style="width: 200px;">
                        <input type="text" name="signup_start_time" placeholder="报名开始时间" id="signup_start_time" class="layui-input"  v-model="formData.signup_start_time">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 200px;">
                        <input type="text" name="signup_end_time" placeholder="报名结束时间" id="signup_end_time" class="layui-input"  v-model="formData.signup_end_time">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">活动时间</label>
                    <div class="layui-input-inline" style="width: 200px;">
                        <input type="text" name="start_time" placeholder="活动开始时间" id="start_time" class="layui-input"  v-model="formData.start_time">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 200px;">
                        <input type="text" name="end_time" placeholder="活动结束时间" id="end_time" class="layui-input"  v-model="formData.end_time">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon">人数</span>
                            <input maxlength="8" placeholder="人数" name="number" class="layui-input" id="number"  v-model="formData.number">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="form-control" style="height:auto">
                            <label style="color:#ccc">图文封面大图片设置</label>
                            <div class="row nowrap">
                                <div class="col-xs-3" style="width:160px">
                                    <img :src="formData.image" alt="" style="width:100px">
                                </div>
                                <div class="col-xs-6"  @click="upload('image')">
                                    <br>
                                    <a class="btn btn-sm add_image upload_span">上传图片</a>
                                    <br>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="form-control" style="height:auto">
                            <label style="color:#ccc">群聊图片设置</label>
                            <div class="row nowrap">
                                <div class="col-xs-3" style="width:160px">
                                    <img :src="formData.qrcode_img" alt="" style="width:100px">
                                </div>
                                <div class="col-xs-6"  @click="upload('qrcode_img')">
                                    <br>
                                    <a class="btn btn-sm add_image upload_span">上传图片</a>
                                    <br>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">

                    <div class="col-md-12">
                        <label style="color:#aaa">地址</label>
                        <div class="col-md-12">
                            <div class="layui-form">
                            <div class="layui-form-item col-md-6" id="area-picker">
                                <div class="layui-input-inline col-md-4">
                                    <select name="province" v-model="formData.province" class="province-selector" :data-value="formData.province" lay-filter="province-1" >
                                        <option value="">请选择省</option>
                                    </select>
                                </div>
                                <div class="layui-input-inline col-md-4">
                                    <select name="city" v-model="formData.city"  class="city-selector" :data-value="formData.city" lay-filter="city-1">
                                        <option value="">请选择市</option>
                                    </select>
                                </div>
                                <div class="layui-input-inline col-md-4">
                                    <select name="county" v-model="formData.district" class="county-selector" :data-value="formData.district" lay-filter="county-1">
                                        <option value="">请选择区</option>
                                    </select>
                                </div>
                            </div>
                            <input  id="address"  class="layui-input col-md-6" v-model="formData.detail" placeholder="详细地址" style="height:30px;resize:none;line-height:20px;color:#333;width:auto;">
                        </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <label style="color:#aaa">活动规则</label>
                        <textarea  type="text/plain" id="myEditor1" style="width:100%;">{{formData.activity_rules}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <label style="color:#aaa">活动详情</label>
                        <textarea type="text/plain" id="myEditor" style="width:100%;">{{formData.content}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label style="display:block"><span style="color:#aaa;">排序</span>
                            <input maxlength="5" type="number" v-model="formData.sort" name="sort" class="layui-input" id="sort" >
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label style="display:block"><span style="color:#aaa;">限购(设置每人可购买的次数，0为不限购)</span>
                            <input  type="text" v-model="formData.restrictions" name="restrictions" class="layui-input" id="restrictions" >
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label style="color:#aaa">活动状态</label>
                            <br/>
                            <input type="radio" name="is_show" class="layui-radio" value="0" v-model="formData.is_show" >不显示
                            <input type="radio" name="is_show" class="layui-radio" value="1" v-model="formData.is_show" >显示
                        </div>
                        <div class="col-md-6">
                            <label style="color:#aaa">是否填写资料</label>
                            <br/>
                            <input type="radio" name="is_fill" class="layui-radio" value="0" v-model="formData.is_fill" >不填写
                            <input type="radio" name="is_fill" class="layui-radio" value="1" v-model="formData.is_fill" >填写
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="layui-card">
                            <div class="layui-card-header">付费信息</div>
                            <div class="layui-card-body" style="padding: 10px 150px;">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">付费方式</label>
                                    <div class="layui-input-block">
                                        <input type="radio" name="pay_type"
                                               v-model="formData.pay_type" value="1" title="付费">付费
                                        <input type="radio" name="pay_type"
                                               v-model="formData.pay_type" value="0" title="免费">免费
                                    </div>
                                </div>
                                <div class="layui-form-item" v-show="formData.pay_type == 1">
                                    <label class="layui-form-label">购买金额</label>
                                    <div class="layui-input-block">
                                        <input style="width: 20%" type="number" name="money" lay-verify="number"
                                               v-model="formData.price" autocomplete="off" class="layui-input" min="0">
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">会员付费方式</label>
                                        <div class="layui-input-block">
                                            <input type="radio" name="member_pay_type"
                                                   v-model="formData.member_pay_type" value="1" title="付费">付费
                                            <input type="radio" name="member_pay_type"
                                                   v-model="formData.member_pay_type" value="0" title="免费">免费
                                        </div>
                                    </div>
                                    <div class="layui-form-item" v-show="formData.member_pay_type == 1">
                                        <label class="layui-form-label">会员购买金额</label>
                                        <div class="layui-input-block">
                                            <input style="width: 20%" type="number" name="member_money" lay-verify="number"
                                                   v-model="formData.member_price" autocomplete="off" class="layui-input" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-4 col-md-9">
                            <button type="button" class="btn btn-w-m btn-info save_news" @click="save">{$id ?
                                '确认修改':'立即提交'}</button>
                            <button class="layui-btn layui-btn-primary clone" type="button" @click="clone_form">取消
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script src="/static/plug/reg-verify.js"></script>
{/block}
{block name="script"}
<script>
    var id = {$id},
        news ={$news};
    require(['vue'], function (Vue) {
        new Vue({
            el: "#app",
            data: {
                formData: {
                    id:id,
                    title: news.title || '',
                    image: news.image || '{__ADMIN_PATH}images/empty.jpg',
                    qrcode_img: news.qrcode_img || '{__ADMIN_PATH}images/empty.jpg',
                    start_time: news.start_time || '',
                    end_time: news.end_time || '',
                    signup_start_time: news.signup_start_time || '',
                    signup_end_time: news.signup_end_time || '',
                    province: news.province || '',
                    city: news.city || '',
                    district: news.district || '',
                    detail: news.detail || '',
                    number: news.number || 0,
                    activity_rules: news.activity_rules || '',
                    content: news.content || '',
                    sort: news.sort || 0,
                    restrictions: news.restrictions || 0,
                    is_show: news.is_show || 0,
                    is_fill: news.is_fill,
                    pay_type: news.pay_type || 0,
                    price: news.price || 0,
                    member_pay_type: news.member_pay_type == 1 ? 1 : 0,
                    member_price: news.member_price || '',
                },
                host: ossUpload.host + '/',
                mask: {
                    poster_image: false,
                    image: false,
                    service_code: false,
                },
                ue: null,
                //上传类型
                mime_types: {
                    Image: "jpg,gif,png,JPG,GIF,PNG",
                    Video: "mp4,MP4",
                },
            },
            methods: {
                //取消
                cancelUpload: function () {
                    this.uploader.stop();

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
                    ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count},{w:800,h:550});
                },
                delLabel: function (index) {
                    this.formData.label.splice(index, 1);
                    this.$set(this.formData, 'label', this.formData.label);
                },
                save: function () {
                    var that = this;
                    that.formData.content = that.ue.getContent();
                    that.formData.activity_rules = that.ue1.getContent();
                    if (!that.formData.title) return layList.msg('请输入专题标题');
                    if (!that.formData.image) return layList.msg('请上传专题封面');
                    if (!that.formData.qrcode_img) return layList.msg('请上传群聊二维码');
                    if (!that.formData.number) return layList.msg('请填写活动人数');
                    if (!that.formData.start_time) return layList.msg('请选择活动开始时间');
                    if (!that.formData.end_time) return layList.msg('请选择活动结束时间');
                    if (!that.formData.signup_start_time) return layList.msg('请填写活动报名开始时间');
                    if (!that.formData.signup_end_time) return layList.msg('请填写活动报名结束时间');
                    if (!that.formData.province || !that.formData.city || !that.formData.district ||!that.formData.detail) return layList.msg('请输入地址信息');
                    if (!that.formData.activity_rules) return layList.msg('请输入规则');
                    if (!that.formData.content) return layList.msg('请编辑内容在进行保存');
                    if (that.formData.pay_type == 1) {
                        if (!that.formData.price || that.formData.price == 0.00) return layList.msg('请填写购买金额');
                    }
                    if (that.formData.member_pay_type == 1) {
                        if (!that.formData.member_price || that.formData.member_price == 0.00) return layList.msg('请填写会员购买金额');
                    }
                    layList.loadFFF();
                    layList.basePost(layList.U({
                        a: 'add_new',
                    }), that.formData, function (res) {
                        layList.loadClear();
                        if (parseInt(id) == 0) {
                            layList.layer.confirm('添加成功,您要继续添加活动吗?', {
                                btn: ['继续添加', '立即提交'] //按钮
                            }, function () {
                                window.location.reload();
                            }, function () {
                                parent.layer.closeAll();
                            });
                        } else {
                            layList.msg('修改成功', function () {
                                parent.layer.closeAll();
                                window.location.reload();
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
                        if (that.formData.image) return layList.msg('请先删除上传的图片在尝试取消');
                        parent.layer.closeAll();
                    }
                    parent.layer.closeAll();
                },
            },
            mounted: function () {
                var that = this;
                var layer = layui.layer, form = layui.form;
                layui.config({
                    base: '{__ADMIN_PATH}mods/'
                    , version: '1.0'
                }).extend({
                    layarea:'layarea'
                });
                layui.use(['layarea'], function () {
                    var layarea = layui.layarea;
                    layarea.render({
                        elem: '#area-picker',
                        change: function (res) {
                            //选择结果
                            that.formData.province= res.province;
                            that.formData.city= res.city;
                            that.formData.district= res.county;
                        }
                    });
                });
                window.changeIMG = that.changeIMG;
                layList.date({
                    elem: '#signup_start_time',
                    theme: '#393D49',
                    type: 'datetime',
                    done: function (value) {
                        that.formData.signup_start_time = value;
                    }
                });
                layList.date({
                    elem: '#start_time',
                    theme: '#393D49',
                    type: 'datetime',
                    done: function (value) {
                        that.formData.start_time = value;
                    }
                });
                layList.date({
                    elem: '#end_time',
                    theme: '#393D49',
                    type: 'datetime',
                    done: function (value) {
                        that.formData.end_time = value;
                    }
                });
                layList.date({
                    elem: '#signup_end_time',
                    theme: '#393D49',
                    type: 'datetime',
                    done: function (value) {
                        that.formData.signup_end_time = value;
                    }
                });

                //选择图片
                function changeIMG(index, pic) {
                    $(".image_img").css('background-image', "url(" + pic + ")");
                    $(".active").css('background-image', "url(" + pic + ")");
                    $('#image_input').val(pic);
                }

                //选择图片插入到编辑器中
                window.insertEditor = function (list,fodder) {
                    if(fodder=='editor'){
                        that.ue.execCommand('insertimage', list);
                    }else if(fodder=='editors'){
                        that.ue1.execCommand('insertimage', list);
                    }

                }

                this.$nextTick(function () {
                    layList.form.render();
                    //实例化编辑器
                    UE.registerUI('imagenone', function (editor, name) {
                        var $btn = new UE.ui.Button({
                            name: 'image',
                            onclick: function () {
                                ossUpload.createFrame('选择图片', {fodder: 'editor'},{w:800,h:550});
                            },
                            title: '选择图片'
                        });

                        return $btn;

                    });
                    that.ue = UE.getEditor('myEditor');
                });
                this.$nextTick(function () {
                    layList.form.render();
                    //实例化编辑器
                    UE.registerUI('imagenone', function (editor, name) {
                        var $btn = new UE.ui.Button({
                            name: 'image',
                            onclick: function () {
                                ossUpload.createFrame('选择图片', {fodder: 'editors'},{w:800,h:550});
                            },
                            title: '选择图片'
                        });

                        return $btn;

                    });
                    that.ue1 = UE.getEditor('myEditor1');
                });
                //图片上传和视频上传

                layList.form.on('radio(pay_type)', function (data) {
                    that.formData.pay_type = parseInt(data.value);
                    if (that.formData.pay_type != 1) {
                        that.formData.member_pay_type = 0;
                        that.formData.member_price = 0;
                        that.formData.price = 0;
                    };
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
                layList.form.on('radio(member_pay_type)', function (data) {
                    that.formData.member_pay_type = parseInt(data.value);
                    if (that.formData.member_pay_type != 1) {
                        that.formData.member_price = 0;
                    };
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
                layList.form.render();
                that.$nextTick(function () {
                    that.uploader = ossUpload.upload({
                        id: 'ossupload',
                        FilesAddedSuccess: function () {
                        },
                        uploadIng: function (file) {
                            that.videoWidth = file.percent;
                        },
                        success: function (res) {
                            layList.msg('上传成功');
                            that.videoWidth = 0;
                            that.setContent(res.url);
                        },
                        fail: function (err) {
                            that.videoWidth = 0;
                            layList.msg(err);
                        }
                    })
                });
            }
        })
    })

</script>
{/block}


