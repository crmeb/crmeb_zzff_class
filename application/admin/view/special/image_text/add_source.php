{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-item .special-label{
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
    .layui-form-item .special-label i{
        display: inline-block;
        width: 18px;
        height: 18px;
        font-size: 18px;
        color: #fff;
    }
    .layui-form-item .label-box{
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
    .layui-form-item .label-box p{
        line-height: inherit;
    }
    .layui-form-mid{
        margin-left: 18px;
    }
    .m-t-5{
        margin-top:5px;
    }
    .edui-default .edui-for-image .edui-icon{
        background-position: -380px 0px;
    }
</style>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
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
    <div class="layui-row layui-col-space15"  id="app">
        <form action="" class="layui-form">
            <div class="layui-col-md12">
                <div class="layui-card" v-cloak="">
                    <div class="layui-card-header">基本信息</div>
                    <div class="layui-card-body" style="padding: 10px 150px;">
                        <!--<div class="layui-form-item">
                            <label class="layui-form-label">分类选择</label>
                            <div class="layui-input-block">
                                <select name="subject_id" v-model="formData.subject_id" lay-search="" lay-filter="subject_id">
                                    <option value="0">请选分类</option>
                                    <option :value="item.id" v-for="item in subject_list">{{item.name}}</option>
                                </select>
                            </div>
                        </div>-->
                        <div class="layui-form-item">
                            <label class="layui-form-label" >素材名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" v-model="formData.title" autocomplete="off" placeholder="请输入素材名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">素材排序</label>
                            <div class="layui-input-block">
                                <input type="number" style="width: 20%" name="sort" v-model="formData.sort" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5" v-cloak="">
                            <label class="layui-form-label">素材封面</label>
                            <div class="layui-input-block">
                                <div class="upload-image-box" v-if="formData.image" @mouseenter="mask.image = true" @mouseleave="mask.image = false">
                                    <img :src="formData.image" alt="">
                                    <div class="mask" v-show="mask.image" style="display: block">
                                        <p><i class="fa fa-eye" @click="look(formData.image)"></i><i class="fa fa-trash-o" @click="delect('image')"></i></p>
                                    </div>
                                </div>
                                <div class="upload-image"  v-show="!formData.image" @click="upload('image')">
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>选择图片</p>
                                </div>
                            </div>
                            <div class="layui-form-item m-t-5">
                                <label class="layui-form-label">素材简介</label>
                                <div class="layui-input-block">
                                    <textarea id="myEditorDetail" style="width:100%;height: 500px">{{formData.detail}}</textarea>
                                </div>
                            </div>
                            <div class="layui-form-item m-t-5">
                                <label class="layui-form-label">素材内容</label>
                                <div class="layui-input-block">
                                    <textarea id="myEditorContent" style="width:100%;height: 500px">{{formData.content}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-form-item submit" style="margin-bottom: 10px">
                        <div class="layui-input-block">
                            <button class="layui-btn layui-btn-normal" type="button" @click="save">{$id ? '确认修改':'立即提交'}</button>
                            <button class="layui-btn layui-btn-primary clone" type="button" @click="clone_form">取消</button>
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
    var id={$id},
        special=<?=isset($special) ? $special : "{}"?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                subject_list:[],
                formData:{
                    title:special.title || '',
                    image:special.image ?  special.image['pic'] : '',
                    sort:special.sort || 0,
                    content:special.content ? (special.content || '') : '',
                    detail:special.detail ? (special.detail || '') : '',

                },

                mask:{
                    image:false,
                },
                ue:null,
                //上传类型
                mime_types:{
                    Image:"jpg,gif,png,JPG,GIF,PNG",
                },
                uploader:null,
            },
            methods:{
                //取消
                cancelUpload:function(){
                    this.uploader.stop();
                    this.is_video = false;
                    this.videoWidth = 0;
                },
                //删除图片
                delect:function(key,index){
                    var that = this;
                    if(index != undefined){
                        that.formData[key].splice(index,1);
                        that.$set( that.formData,key,that.formData[key]);
                    }else{
                        that.$set(that.formData,key,'');
                    }
                },
                //查看图片
                look:function(pic){
                    $eb.openImage(pic);
                },
                //鼠标移入事件
                enter:function(item){
                    if(item){
                        item.is_show = true;
                    }else{
                        this.mask = true;
                    }
                },
                //鼠标移出事件
                leave:function(item){
                    if(item){
                        item.is_show = false;
                    }else{
                        this.mask = false;
                    }
                },
                changeIMG:function(key,value,multiple){
                    if(multiple){
                        var that = this;
                        value.map(function (v) {
                            that.formData[key].push({pic:v,is_show:false});
                        });
                        this.$set(this.formData,key,this.formData[key]);
                    }else{
                        this.$set(this.formData,key,value);
                    }
                },

                setContent:function(link){
                    this.ueC.setContent('<div><video style="width: 100%" src="'+link+'" class="video-ue" controls="controls"><source src="'+link+'"></source></video></div><br><span style="color:white">.</span>',true);
                },
                //上传图片
                upload:function(key,count){
                    ossUpload.createFrame('请选择图片',{fodder:key,max_count:count === undefined ? 0 : count});
                },
                get_subject_list:function(){
                    var that=this;
                    layList.baseGet(layList.U({a:'get_subject_list'}),function (res) {
                        that.$set(that,'subject_list',res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },

                save:function () {
                    var that=this/*,banner=new Array()*/;
                    that.formData.content = that.ueC.getContent();
                    that.formData.detail = that.ueD.getContent();
                    if(!that.formData.title) return layList.msg('请输入素材标题');
                    //if(!that.formData.content) return layList.msg('请编辑素材内容再进行保存');
                    if(!that.formData.detail) return layList.msg('请编辑素材简介再进行保存');
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'save_source',q:{id:id,special_type:'{$special_type}'}}),that.formData,function (res) {
                        layList.loadClear();
                        if(parseInt(id) == 0) {
                            layList.layer.confirm('添加成功,您要继续添加素材吗?', {
                                btn: ['继续添加', '立即提交'] //按钮
                            }, function () {
                                window.location.reload();
                            }, function () {
                                parent.layer.closeAll();
                            });
                        }else{
                            layList.msg('修改成功',function () {
                               parent.layer.closeAll();
                            })
                        }
                    },function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                },
                clone_form:function () {
                    var that = this;
                    if(parseInt(id) == 0){
                        if(that.formData.image) return layList.msg('请先删除上传的图片在尝试取消');
                        parent.layer.closeAll();
                    }
                    parent.layer.closeAll();
                }
            },
            mounted:function () {
                var that=this;
                window.changeIMG = that.changeIMG;
                //实例化form
                layList.date({
                    elem:'#live_time',
                    theme:'#393D49',
                    type:'datetime',
                    done:function (value) {
                        that.formData.live_time = value;
                    }
                });

                //选择图片
                function changeIMG(index,pic){
                    $(".image_img").css('background-image',"url("+pic+")");
                    $(".active").css('background-image',"url("+pic+")");
                    $('#image_input').val(pic);
                }
                //选择图片插入到编辑器中
                window.insertEditor = function(list,e){
                    that.ueC.execCommand('insertimage', list);
                    that.ueD.execCommand('insertimage', list);
                }

                this.$nextTick(function () {
                    layList.form.render();
                    //实例化编辑器
                    UE.registerUI('imagenone',function(editor,name){
                        var $btn = new UE.ui.Button({
                            name : 'image',
                            onclick : function(){
                                ossUpload.createFrame('选择图片',{fodder:'editor'});
                            },
                            title: '选择图片'
                        });

                        return $btn;

                    });
                    that.ueC = UE.getEditor('myEditorContent');
                    that.ueD = UE.getEditor('myEditorDetail');
                });
                //获取科目
                that.get_subject_list();
                //图片上传和视频上传
                that.$nextTick(function () {
                    that.uploader = ossUpload.upload({
                        id:'ossupload',
                        FilesAddedSuccess:function(){
                            that.is_video = true;
                        },
                        uploadIng:function (file) {
                            that.videoWidth = file.percent;
                        },
                        success:function (res) {
                            layList.msg('上传成功');
                            that.videoWidth = 0;
                            that.is_video = false;
                            that.setContent(res.url);
                        },
                        fail:function (err) {
                            that.videoWidth = 0;
                            that.is_video = false;
                            layList.msg(err);
                        }
                    })
                })
            }
        })
    })

</script>
{/block}