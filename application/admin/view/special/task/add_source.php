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
    .edui-default .edui-for-image .edui-icon{
        background-position: -380px 0px;
    }
    .file {
        position: relative;
        background: #0092DC;
        border: 1px solid #99D3F5;
        border-radius: 4px;
        padding: 7px 12px;
        overflow: hidden;
        color: #fff;
        text-decoration: none;
        text-indent: 0;
        line-height: 20px;
        width: 120px;
    }
    .file input {
        width: 100%;
        position: absolute;
        font-size: 5px;
        right: 0;
        top: 0;
        opacity: 0;
    }
    .file:hover {
        background: #AADFFD;
        border-color: #78C3F3;
        color: #004974;
        text-decoration: none;
    }
    .layui-form-select dl {
        z-index: 1000;
    }
</style>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
<script src="{__ADMIN_PATH}plug/aliyun-upload-sdk/aliyun-upload-sdk-1.5.0.min.js"></script>
<script src="{__ADMIN_PATH}plug/aliyun-upload-sdk/lib/es6-promise.min.js"></script>
<script src="{__ADMIN_PATH}plug/aliyun-upload-sdk/lib/aliyun-oss-sdk-5.3.1.min.js"></script>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div v-cloak class="layui-row layui-col-space15" id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">
                    <div style="font-weight: bold;">基本信息</div>
                </div>
                <div class="layui-card-body">
                    <form action="" class="layui-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label" >素材类型：</label>
                            <div class="layui-input-block">
                                <input type="radio" name="source_type" lay-filter="source_type" v-model="source_type" value="1" title="图文">
                                <input type="radio" name="source_type" lay-filter="source_type" v-model="source_type" value="2" title="音频">
                                <input type="radio" name="source_type" lay-filter="source_type" v-model="source_type" value="3" title="视频">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label" >素材名称：</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" v-model="formData.title" autocomplete="off" placeholder="请输入素材名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">素材分类：</label>
                            <div class="layui-input-block">
                                <select name="pid" v-model="formData.pid" lay-search="" lay-filter="pid" >
                                    <option v-for="item in cateList"  :value="item.id" >{{item.html}}{{item.title}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">素材排序：</label>
                            <div class="layui-input-block">
                                <input type="number" style="width: 300px;" name="sort" v-model="formData.sort" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">
                                <div>素材封面：</div>
                                <div>(710*400px)</div>
                            </label>
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
                            <div class="layui-form-item"  v-show="source_type==3">
                                <label class="layui-form-label">视频素材：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" v-model="link" style="width:50%;display:inline-block;margin-right: 10px;" autocomplete="off" placeholder="请输入视频链接" class="layui-input">
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" @click="confirmAdd()" v-show="is_upload==false">确认添加</button>
                                    <label style="display: inline;" class="file" v-show="is_upload==false">
                                        <input style="display: none;" type="file" id="ossupload_video" class="ossupload layui-btn layui-btn-sm layui-btn-normal" >上传视频
                                    </label>
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" v-show="is_upload" @click="delVideo()">删除</button>
                                </div>
                                <div class="layui-input-block" style="width: 50%;margin-top: 20px" v-show="is_video">
                                    <div class="layui-progress" style="margin-bottom: 10px">
                                        <div class="layui-progress-bar layui-bg-blue" :style="'width:'+videoWidth+'%'"></div>
                                    </div>
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-danger"
                                            @click="cancelUpload" v-show="demand_switch==2 && is_video">取消
                                    </button>
                                    <button type="button" class="authUpload layui-btn layui-btn-sm layui-btn-danger" v-show="demand_switch==1 && is_video">开始上传
                                    </button>
                                    <button type="button" class="pauseUpload layui-btn layui-btn-sm layui-btn-danger"
                                            v-show="demand_switch==1 && is_video">暂停
                                    </button>
                                    <button type="button" class="resumeUpload layui-btn layui-btn-sm layui-btn-danger" v-show="is_suspend"
                                            >恢复上传
                                    </button>
                                </div>
                                <div class="layui-form-mid layui-word-aux" style="margin-left: 0;">输入链接将视为添加视频直接添加,请确保视频链接的正确性</div>
                            </div>
                            <div class="layui-form-item"  v-show="source_type==2">
                                <label class="layui-form-label">音频素材：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" v-model="link" style="width:50%;display:inline-block;margin-right: 10px;" autocomplete="off" placeholder="请输入音频链接" class="layui-input">
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" @click="confirmAdd()" v-show="is_upload==false">确认添加</button>
                                    <label style="display: inline;" class="file" v-show="is_upload==false">
                                        <input style="display: none;" type="file" id="ossupload_audio" class="ossupload layui-btn layui-btn-sm layui-btn-normal" >上传音频
                                    </label>
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" v-show="is_upload" @click="delVideo()">删除</button>
                                </div>
                                <div class="layui-input-block" style="width: 50%;margin-top: 20px" v-show="is_video">
                                    <div class="layui-progress" style="margin-bottom: 10px">
                                        <div class="layui-progress-bar layui-bg-blue" :style="'width:'+videoWidth+'%'"></div>
                                    </div>
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-danger"
                                            @click="cancelUpload" v-show="demand_switch==2 && is_video">取消
                                    </button>
                                    <button type="button" class="authUpload layui-btn layui-btn-sm layui-btn-danger" v-show="demand_switch==1 && is_video">开始上传
                                    </button>
                                    <button type="button" class="pauseUpload layui-btn layui-btn-sm layui-btn-danger"
                                            v-show="demand_switch==1 && is_video">暂停
                                    </button>
                                    <button type="button" class="resumeUpload layui-btn layui-btn-sm layui-btn-danger" v-show="is_suspend"
                                            >恢复上传
                                    </button>
                                </div>
                                <div class="layui-form-mid layui-word-aux" style="margin-left: 0;">输入链接将视为添加音频直接添加,请确保音频链接的正确性</div>
                            </div>
                        </div>
                        <div class="layui-form-item" >
                            <label class="layui-form-label" v-show="source_type==1">素材内容：</label>
                            <div class="layui-input-block">
                                <textarea id="myEditorContent"  style="width:100%;height: 500px">{{formData.content}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">素材简介：</label>
                            <div class="layui-input-block">
                                <textarea id="myEditorDetail" style="width:100%;height: 500px">{{formData.detail}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" type="button" @click="save">{$id ? '确认修改':'立即提交'}</button>
                                <button class="layui-btn layui-btn-primary clone" type="button" @click="clone_form">取消</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var id={$id},alicloud_account_id="{$alicloud_account_id}",configuration_item_region="{$configuration_item_region}",demand_switch="{$demand_switch}",
        special=<?=isset($special) ? $special : "{}"?>;
    require(['vue','zh-cn','request','aliyun-oss','plupload','OssUpload'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                formData:{
                    title:special.title || '',
                    image:special.image ?  special.image['pic'] : '',
                    sort:special.sort || 0,
                    pid:special.pid || 0,
                    content:special.content,
                    detail:special.detail ? (special.detail || '') : '',
                    link:special.link ? (special.link || '') : '',
                    videoId:special.videoId ? (special.videoId || '') : '',
                    file_type:special.file_type ? (special.file_type || '') : '',
                    file_name:special.file_name ? (special.file_name || '') : '',
                },
                source_type:special.type ? special.type : 1,
                but_title:'上传音频',
                link:'',
                host: ossUpload.host + '/',
                mask:{
                    image:false,
                },
                ue:null,
                is_video:false,
                is_suspend:false,
                is_upload:false,
                demand_switch:demand_switch,
                //上传类型
                mime_types:{
                    Image:"jpg,gif,png,JPG,GIF,PNG",
                    Video:"mp4,MP4,mp3,MP3",
                },
                videoWidth:0,
                uploader:null,
                cateList:[]
            },
            methods:{
                //取消
                cancelUpload:function(){
                    this.uploader.stop();
                    this.is_video = false;
                    this.videoWidth = 0;
                    this.is_upload = false;
                },
                //删除图片
                delect:function(key,index){
                    var that = this;
                    if(index != undefined){
                        that.formData[key].splice(index,1);
                        that.$set(that.formData,key,that.formData[key]);
                    }else{
                        that.$set(that.formData,key,'');
                    }
                },
                //查看图片
                look:function(pic){
                    parent.$eb.openImage(pic);
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
                confirmAdd:function(){
                    var that = this;
                    if(that.link.substr(0,7).toLowerCase() == "http://" || that.link.substr(0,8).toLowerCase() == "https://"){
                        that.is_upload=true;
                        that.uploadVideo();
                    }else{
                        if(this.source_type==2){
                            layList.msg('请输入正确的音频链接');
                        }else if(this.source_type==3){
                            layList.msg('请输入正确的视频链接');
                        }
                    }
                },
                uploadVideo:function(){
                    if(this.link.substr(0,7).toLowerCase() == "http://" || this.link.substr(0,8).toLowerCase() == "https://"){
                        this.setContent(this.link);
                    }else{
                        if(this.source_type==2){
                            layList.msg('请输入正确的音频链接');
                        }else if(this.source_type==3){
                            layList.msg('请输入正确的视频链接');
                        }
                    }
                },
                setContent:function(link){
                    switch(this.source_type){
                        case 2://音频
                            this.ueC.setContent('<div><audio style="width: 100%" src="'+link+'" class="video-ue" controls="controls"><source src="'+link+'" type="audio/mpeg"></source></audio></div><span style="color:white">.</span>',true);
                            this.formData.link = link;
                            break;
                        case 3://视频
                            this.ueC.setContent('<div><video style="width: 100%" src="'+link+'" class="video-ue" controls="controls"><source src="'+link+'"></source></video></div><span style="color:white">.</span>',true);
                            this.formData.link = link;
                            break;
                    }
                },
                //上传图片
                upload:function(key,count){
                    ossUpload.createFrame('请选择图片',{fodder:key,max_count:count === undefined ? 0 : count},{w:800,h:550});
                },
                save:function () {
                    var that=this;
                    that.formData.content = that.ueC.getContent();
                    that.formData.detail = that.ueD.getContent();
                    if(!that.formData.title) return layList.msg('请输入素材标题');
                    if(!that.formData.image) return layList.msg('请输入素材封面');
                    if(!that.formData.content) return layList.msg('请编辑素材内容再进行保存');
                    if(!that.formData.detail) return layList.msg('请编辑素材简介再进行保存');
                    if(that.demand_switch=='1' && that.formData.videoId && that.source_type!=1){
                        that.formData.link ='';
                    }else if(that.demand_switch=='1' && that.formData.link && that.source_type!=1){
                        that.formData.videoId='';
                        that.formData.file_type='';
                        that.formData.file_name='';
                    }else if(that.demand_switch=='2' && that.formData.videoId=='' && that.source_type!=1){
                        if(!that.formData.link) return layList.msg('请上传素材');
                        that.formData.videoId='';
                        that.formData.file_type='';
                        that.formData.file_name='';
                    }else if(that.demand_switch=='2' && that.formData.videoId && that.source_type!=1){
                        that.formData.link ='';
                    }
                    if(that.source_type!=1 && that.formData.link =='' && that.formData.videoId=='') return layList.msg('上传音视频素材');
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'save_source',q:{id:id,special_type:that.source_type}}),that.formData,function (res) {
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
                            layer.msg('修改成功',{icon:1},function () {
                               parent.layer.closeAll();
                            });
                        }
                    },function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                },
                clone_form:function () {
                    if(parseInt(id) == 0){
                        var that = this;
                        if(that.formData.image) return layList.msg('请先删除上传的图片在尝试取消');
                        parent.layer.closeAll();
                    }
                    parent.layer.closeAll();
                },
                //获取素材分类
                get_subject_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({c:'special.special_task_category',a: 'get_cate_list'}), function (res) {
                        that.$set(that, 'cateList', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                createUploader:function () {
                    var that=this;
                    var uploader = new AliyunUpload.Vod({
                        timeout: 60000,//请求过期时间（配置项 timeout, 默认 60000）
                        partSize: 1048576,//分片大小（配置项 partSize, 默认 1048576）
                        parallel: 5,//上传分片数（配置项 parallel, 默认 5）
                        retryCount:3,//网络失败重试次数（配置项 retryCount, 默认 3）
                        retryDuration:2,//网络失败重试间隔（配置项 retryDuration, 默认 2）
                        region: configuration_item_region,//配置项 region, 默认 cn-shanghai
                        userId: alicloud_account_id,//阿里云账号ID
                        // 添加文件成功
                        addFileSuccess: function (uploadInfo) {
                            if (alicloud_account_id=='') {
                                return layList.msg('请配置阿里云账号ID！');
                            }
                            var type=uploadInfo.file.type;
                            var arr=type.split('/');
                            if(that.source_type==2 && arr[0]!='audio'){
                                that.is_video=false;
                                that.videoWidth = 0;
                                return layList.msg('请上传音频');
                            }else if(that.source_type==3 && arr[0]!='video'){
                                that.is_video=false;
                                that.videoWidth = 0;
                                return layList.msg('请上传视频');
                            }else{
                                that.is_video=true;
                                that.videoWidth = 0;
                            }
                        },
                        // 开始上传
                        onUploadstarted: function (uploadInfo) {
                            var videoId='';
                            if(uploadInfo.videoId){
                                videoId= uploadInfo.videoId;
                            }
                            layList.basePost(layList.U({a: 'video_upload_address_voucher'}),
                            {
                                FileName:uploadInfo.file.name,type:1,image:that.formData.image,videoId:videoId
                            }, function (res) {
                                var url=res.msg;
                                $.ajax({
                                    url:url,
                                    data:{},
                                    type:"GET",
                                    dataType:'json',
                                    success:function (data) {
                                        if(data.RequestId){
                                            var uploadAuth = data.UploadAuth;
                                            var uploadAddress = data.UploadAddress;
                                            var videoId = data.VideoId;
                                            uploader.setUploadAuthAndAddress(uploadInfo, uploadAuth, uploadAddress,videoId)
                                        }
                                    },
                                    error:function (err) {
                                        return layList.msg(err.responseJSON.Message);
                                    }
                                });
                            });
                        },
                        // 文件上传成功
                        onUploadSucceed: function (uploadInfo) {
                            that.formData.videoId=uploadInfo.videoId;
                            that.formData.file_name=uploadInfo.file.name;
                            that.formData.file_type=uploadInfo.file.type;
                            that.videoWidth = 0;
                            that.is_video = false;
                            that.is_suspend = false;
                            that.is_upload = true;
                            that.playbackAddress(uploadInfo.videoId);
                        },
                        // 文件上传失败
                        onUploadFailed: function (uploadInfo, code, message) {
                        },
                        // 取消文件上传
                        onUploadCanceled: function (uploadInfo, code, message) {
                            that.formData.file_name='';
                            that.is_suspend = false;
                        },
                        // 文件上传进度，单位：字节, 可以在这个函数中拿到上传进度并显示在页面上
                        onUploadProgress: function (uploadInfo, totalSize, progress) {
                            that.videoWidth = Math.ceil(progress * 100);
                        },
                        // 上传凭证超时
                        onUploadTokenExpired: function (uploadInfo) {
                            var videoId='';
                            if(uploadInfo.videoId){
                                videoId= uploadInfo.videoId;
                            }
                            layList.basePost(layList.U({a: 'video_upload_address_voucher'}),{
                                FileName:uploadInfo.file.name,type:1,image:that.formData.image,videoId:videoId
                            }, function (res) {
                                var url=res.msg;
                                $.ajax({
                                    url:url,
                                    data:{},
                                    type:"GET",
                                    dataType:'json',
                                    success:function (data) {
                                        if(data.RequestId){
                                            var uploadAuth = data.UploadAuth;
                                            uploader.resumeUploadWithAuth(uploadAuth);
                                        }
                                    },
                                    error:function (err) {
                                        return layList.msg(err.responseJSON.Message);
                                    }
                                });
                            });
                        },
                        // 全部文件上传结束
                        onUploadEnd: function (uploadInfo) {
                            that.videoWidth = 0;
                            that.is_video = false;
                            that.is_suspend = false;
                            that.is_upload = true;
                            console.log("onUploadEnd: uploaded all the files")
                        }
                    });
                    return uploader;
                },
                delVideo:function(){
                    var that=this;
                    if(that.demand_switch=='1' && that.formData.videoId){
                        layList.basePost(layList.U({a: 'video_upload_address_voucher'}),{
                            FileName:'',type:4,image:'',videoId:that.formData.videoId
                        }, function (res) {
                            var url=res.msg;
                            $.ajax({
                                url:url,
                                data:{},
                                type:"GET",
                                dataType:'json',
                                success:function (data) {
                                if(data.RequestId){
                                    that.link='';
                                    that.formData.content='';
                                    that.formData.videoId='';
                                    that.formData.file_type='';
                                    that.formData.file_name='';
                                    that.ueC.setContent('');
                                    $("input[type='file']").val('');
                                    that.is_upload = false;
                                }
                                },
                                error:function (err) {
                                    return layList.msg(err.responseJSON.Message);
                                }
                            });
                        });
                    }else{
                        that.formData.videoId='';
                        that.link='';
                        that.ueC.setContent('');
                        that.is_upload = false;
                    }
                },
                playbackAddress:function (videoId) {
                       var that=this;
                       if(videoId=='') return false;
                        layList.basePost(layList.U({a: 'video_upload_address_voucher'}), {
                            FileName: '', type: 3, image: '', videoId: videoId
                        }, function (res) {
                        var url = res.msg;
                        $.ajax({
                            url: url,
                            data: {},
                            type: "GET",
                            dataType: 'json',
                            success: function (data) {
                                that.link = data.PlayInfoList.PlayInfo[0].PlayURL;
                                that.uploadVideo();
                            },
                            error: function (err) {
                                that.link = '';
                                that.formData.content = '';
                                that.formData.videoId = '';
                                that.formData.file_type = '';
                                that.formData.file_name = '';
                                that.is_upload = false;
                                return layList.msg(err.responseJSON.Message);
                            }
                        });
                    });
                },
                audio_video_upload:function () {
                    var that=this;
                    var id='ossupload_video';
                    if(that.source_type==2){
                        id='ossupload_audio'
                    }
                    that.uploader = ossUpload.upload({
                        id: id,
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
                            that.formData.videoId='';
                            that.is_upload = true;
                            that.link = res.url;
                            that.uploadVideo();
                        },
                        fail: function (err) {
                            that.videoWidth = 0;
                            that.is_video = false;
                            that.is_upload = false;
                            layList.msg(err);
                        }
                    })
                }
            },
            mounted:function () {
                var that=this;
                that.get_subject_list();
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
                layList.select('pid', function (obj) {
                    that.formData.pid = obj.value;
                });
                layList.form.on('radio(source_type)', function (data) {
                    that.source_type = parseInt(data.value);
                    if(that.demand_switch=='2') {
                        that.audio_video_upload();
                    }
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
                //选择图片
                function changeIMG(index,pic){
                    $(".image_img").css('background-image',"url("+pic+")");
                    $(".active").css('background-image',"url("+pic+")");
                    $('#image_input').val(pic);
                }
                //选择图片插入到编辑器中
                window.insertEditor = function(list,fodder){
                    that.editorActive.execCommand('insertimage', list);
                };
                if(that.formData.link && that.formData.videoId=='' && that.source_type!=1){
                    that.is_upload=true;
                    that.link = that.formData.link;
                }else if(that.formData.videoId && that.formData.link== '' && that.source_type!=1){
                    that.is_upload=true;
                    that.playbackAddress(that.formData.videoId);
                }

                this.$nextTick(function () {
                    layList.form.render();
                    //实例化编辑器
                    UE.registerUI('imagenone',function(editor,name){
                        var $btn = new UE.ui.Button({
                            name : 'image',
                            onclick : function(){
                                console.log(editor);
                                that.editorActive = editor;
                                ossUpload.createFrame('选择图片',{fodder:'editor'},{w:800,h:550});
                            },
                            title: '选择图片'
                        });
                        return $btn;
                    });
                    that.ueC = UE.getEditor('myEditorContent');
                    that.ueD = UE.getEditor('myEditorDetail');
                });
                //图片上传和视频上传
                var uploader = null;
                if(that.demand_switch=='1'){
                    $('.ossupload').on('change', function (e) {
                        var file = e.target.files[0];
                        if (!file) {
                            return layList.msg('请先选择需要上传的文件！');
                        }
                        var Title = file.name;
                        var userData = '{"Vod":{}}';
                        uploader = that.createUploader();
                        uploader.addFile(file, null, null, null, userData);
                    });
                    // 第一种方式 UploadAuth 上传
                    $('.authUpload').on('click', function () {
                        if (uploader !== null) {
                            uploader.startUpload();
                        }
                    });
                    // 暂停上传
                    $('.pauseUpload').on('click', function () {
                        if (uploader !== null) {
                            uploader.stopUpload();
                            that.is_suspend = true;
                            that.formData.file_name='';
                            layList.msg('暂停上传！');
                        }
                    });
                    //恢复上传
                    $('.resumeUpload').on('click', function () {
                        if (uploader !== null) {
                            uploader.startUpload();
                            that.is_suspend = false;
                            layList.msg('恢复上传成功！');
                        }
                    });
                }else if(that.demand_switch=='2' && id>0){
                    that.audio_video_upload();
                }
            }
        })
    })
</script>
{/block}
