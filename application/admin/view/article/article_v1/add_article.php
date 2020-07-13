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
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/Zeroclipboard.js"></script>
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
    <div class="layui-tab layui-tab-brief" lay-filter="tab">
        <ul class="layui-tab-title">
            <li lay-id="list" {eq name='type' value='1'}class="layui-this" {/eq} >
            <a href="{eq name='type' value='1'}javascript:;{else}{:Url('index',['type'=>1])}{/eq}">新闻列表</a>
            </li>
            <li lay-id="list" {eq name='type' value='2'}class="layui-this" {/eq}>
            <a href="{eq name='type' value='2'}javascript:;{else}{:Url('add_special',['type'=>2])}{/eq}">新闻添加</a>
            </li>
        </ul>
    </div>
    <div class="layui-row layui-col-space15"  id="app">
        <form action="" class="layui-form">
            <div class="layui-col-md12">
                <div class="layui-card" v-cloak="">
                    <div class="layui-card-body" style="padding: 10px 150px;">
                        <div class="layui-form-item">
                            <label class="layui-form-label">新闻名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" v-model="formData.title" autocomplete="off" placeholder="请输入新闻名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">新闻简介</label>
                            <div class="layui-input-block">
                                <textarea placeholder="请输入新闻简介" v-model="formData.synopsis" class="layui-textarea"></textarea>
                            </div>
                        </div>
                       <div class="layui-form-item">
                            <label class="layui-form-label">文章分类</label>
                            <div class="layui-input-block">
                               <select class="chosen-select"  style="width:100%;" lay-filter="getSelect">
                                   <option value="0" >选择分类</option>
                                    <option v-for="(item,idx) in all"  :value="idx" :key="idx">{{item}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">新闻排序</label>
                            <div class="layui-input-block">
                                <input type="number" style="width: 20%" name="sort" v-model="formData.sort" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5" v-cloak="">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="margin-right: 28px">新闻标签</label>
                                <div class="layui-input-inline" style="width: 300px;">
                                    <input type="text" v-model="label" name="price_min" placeholder="最多4个字" autocomplete="off" class="layui-input" style="float: left;width: 200px">
                                    <p class="special-label" @click="addLabrl"><i class="fa fa-plus" aria-hidden="true"></i></p>
                                </div>
                            </div>
                            <div class="layui-input-block">
                                <div class="label-box" v-for="(item,index) in formData.label" @click="delLabel(index)">
                                    <p>{{item}}</p>
                                </div>
                            </div>
                            <div class="layui-form-mid layui-word-aux">输入标签名称点击添加+号进行添加;最多写入4个字;点击标签可删除</div>
                        </div>
                        <div class="layui-form-item m-t-5" v-cloak="">
                            <label class="layui-form-label">新闻封面</label>
                            <div class="layui-input-block">
                                <div class="upload-image-box" v-if="formData.image_input" @mouseenter="enter()" @mouseleave="leave()">
                                    <img :src="formData.image_input" alt="">
                                    <div class="mask" v-show="mask" style="display: block">
                                        <p><i class="fa fa-eye" @click="look(formData.image_input)"></i><i class="fa fa-trash-o" @click="formData.image_input = ''"></i></p>
                                    </div>
                                </div>
                                <div class="upload-image"  v-show="!formData.image_input" @click="upload('image_input')">
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>上传图片</p>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">插入视频</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" v-model="link" style="width:50%;display:inline-block;margin-right: 10px;" autocomplete="off" placeholder="请输入视频链接" class="layui-input">
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" @click="uploadVideo()">确认添加</button>
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="ossupload">上传视频</button>
                            </div>
                            <div class="layui-input-block" style="width: 50%;margin-top: 20px" v-show="is_video">
                                <div class="layui-progress" style="margin-bottom: 10px">
                                    <div class="layui-progress-bar layui-bg-blue" :style="'width:'+videoWidth+'%'"></div>
                                </div>
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" @click="cancelUpload">取消</button>
                            </div>
                            <div class="layui-form-mid layui-word-aux">输入链接将视为添加视频直接添加,请确保视频链接的正确性</div>
                        </div>
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">新闻内容</label>
                            <div class="layui-input-block">
                                <textarea id="myEditor" style="width:100%;height: 500px">{{formData.content}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item submit" style="margin-bottom: 10px">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" type="button" @click="save">{$id ? '确认修改':'立即提交'}</button>
                                <button class="layui-btn layui-btn-primary clone" @click="clone_form">取消</button>
                            </div>
                        </div>
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
    var id={$id};
    var article=<?=isset($article) ? $article : "{}"?>;
    var all={$all};

    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                formData: {
                    title:article.title || '',
                    cid:article.cid || 0,
                    synopsis:article.synopsis || '',
                    sort:article.sort || 0,
                    image_input:article.image_input || '',
                    label:article.label || [],
                    content:article.profile ? (article.profile.content || '') : '',
                },
                all:all,
                but_title:'上传视频',
                link:'',
                label:'',
                mask:false,
                ue:null,
                uploader:null,
                is_video:false,
                videoWidth:0,
            },
            methods:{
                uploadVideo:function(){
                    if(this.link.substr(0,7).toLowerCase() == "http://" || this.link.substr(0,8).toLowerCase() == "https://"){
                        this.setContent(this.link);
                    }else{
                        layList.msg('请输入正确的视频链接');
                    }
                },
                setContent:function(link){
                    this.ue.setContent('<div><video style="width: 100%" src="'+link+'" class="video-ue" controls="controls">\n' +
                        'your browser does not support the video tag\n' +
                        '</video></div><br>',true);
                },
                getSelect:function(e){
                    console.log(e);
                },
                save:function(){
                    var that=this;
                    that.formData.content = that.ue.getContent();
                    if(!that.formData.title) return layList.msg('请输入新闻标题!');
                   if(!that.formData.cid) return layList.msg('请选择分类!');
                    if(!that.formData.synopsis) return layList.msg('请输入新闻简介!');
                    if(that.formData.label.length < 1) return layList.msg('请输入标签!');
                    if(!that.formData.image_input) return layList.msg('请上传新闻封面图');
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'save_article',q:{id:id}}),that.formData,function (res) {
                        layList.loadClear();
                        if(parseInt(id)==0) {
                            layList.layer.confirm('添加成功,您要继续添加图文吗?', {
                                btn: ['继续添加', '立即提交'] //按钮
                            }, function () {
                                window.location.reload();
                            }, function () {
                                window.location.href = layList.U({a: 'index', p: {type: 1}});
                            });
                        }else{
                            layList.msg('修改成功',function () {
                                window.location.href = layList.U({a: 'index', p: {type: 1}});
                            })
                        }
                    },function (res) {
                        layList.loadClear();
                        layList.msg(res.msg);
                    });
                },
                clone_form:function(){
                    if(parseInt(id)==0){
                        if(that.formData.image_input) return layList.msg('请先删除上传的图片在尝试取消');
                    }
                    window.location.href=layList.U({a:'index',p:{type:1}});
                },
                //取消
                cancelUpload:function(){
                    this.uploader.stop();
                    this.is_video = false;
                    this.videoWidth = 0;
                },
                //上传图片
                upload:function(key,count){
                    ossUpload.createFrame('请选择图片',{fodder:key,max_count:count === undefined ? 0 : count},{w:800,h:550});
                },
                //删除图片
                delect:function(act,key,index){
                    var that=this;
                    switch (act){
                        case 1:
                            Ks3.delObject({Key: key},function () {
                                that.formData.image_input={};
                            },function () {
                                that.formData.image_input={};
                            });
                            break;
                    }
                },
                delLabel:function (index) {
                    this.formData.label.splice(index,1);
                    this.$set(this.formData,'label',this.formData.label);
                },
                addLabrl:function () {
                    if(this.label){
                        if(this.label.length > 4) return layList.msg('您输入的标签字数太长');
                        var length=this.formData.label.length;
                        if(length >= 2) return layList.msg('标签最多添加2个');
                        for(var i=0;i<length;i++){
                            if(this.formData.label[i]==this.label) return layList.msg('请勿重复添加');
                        }
                        this.formData.label.push(this.label);
                        this.$set(this.formData,'label',this.formData.label);
                        this.label='';
                    }
                },
                //查看图片
                look:function(pic){
                    $eb.openImage(pic);
                },
                //鼠标移入事件
                enter:function(item){
                    if(item){
                        item.is_show=true;
                    }else{
                        this.mask=true;
                    }
                },
                //鼠标移出事件
                leave:function(item){
                    if(item){
                        item.is_show=false;
                    }else{
                        this.mask=false;
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
                }
            },
            mounted:function () {
                var that = this;

                this.$nextTick(function () {
                    layList.form.render();
                    layList.form.on('select(getSelect)',function (data) {
                        that.formData.cid = data.value;
                    });
                    //实例化编辑器
                    UE.registerUI('imagenone',function(editor,name){
                        var $btn = new UE.ui.Button({
                            name : 'image',
                            onclick : function(){
                                ossUpload.createFrame('选择图片',{fodder:'editor'},{w:800,h:550});
                            },
                            title: '选择图片'
                        });
                        return $btn;
                    });
                    this.ue = UE.getEditor('myEditor');

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
                }.bind(this));
                window.changeIMG = that.changeIMG;
                //选择图片插入到编辑器中
                window.insertEditor = function(list){
                    console.log(list);
                    that.ue.execCommand('insertimage', list);
                }

            },
            updated:function(){
                layList.form.render();
            }
        })
    })
</script>
{/block}