{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-input-block .layui-video-box{
        width: 50%;
        height: 180px;
        border-radius: 10px;
        background-color: #707070;
        margin-top: 10px;
        position: relative;
        overflow: hidden;
    }
    .layui-input-block .layui-video-box i{
        color: #fff;
        line-height: 180px;
        margin: 0 auto;
        width: 50px;
        height: 50px;
        display: inherit;
        font-size: 50px;
    }
    .layui-input-block .layui-video-box .mark{
        position: absolute;
        width: 100%;
        height: 30px;
        top: 0;
        background-color: rgba(0,0,0,.5);
        text-align: center;
    }
</style>
<script type="text/javascript" src="{__ADMIN_PATH}js/aliyun-oss-sdk-4.4.4.min.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/lib/plupload-2.1.2/js/plupload.full.min.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/OssUpload.js"></script>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">{if isset($task)}修改任务{else}新增任务{/if}</div>
                <div class="layui-card-body">
                    <form class="layui-form" action="">
                        <div class="layui-form-item">
                            <label class="layui-form-label">是否收费</label>
                            <div class="layui-input-block">
                                <input type="radio" name="is_pay" value="1" title="收费" {if isset($task)}{if $task.is_pay==1}checked{/if}{else}checked{/if}>
                                <input type="radio" name="is_pay" value="0" title="免费" {if isset($task)}{if $task.is_pay==0}checked{/if}{/if} >
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">请选专题</label>
                            <div class="layui-input-block">
                                <select name="special_id" lay-search="" lay-filter="special_id">
                                    <option value="0">请选专题</option>
                                    {volist name='specialList' id='item'}
                                    <option value="{$item.id}" {if isset($special_id) && $special_id == $item.id} selected {/if}>
                                    {if condition = "$item.is_live eq 1"}
                                    直播----
                                    {else/}
                                    普通----
                                    {/if}
                                    {$item.title}
                                    </option>
                                    {/volist}
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">任务名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" value="{if isset($task)}{$task.title}{/if}" lay-verify="title" autocomplete="off" placeholder="请输入任务名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">排序</label>
                            <div class="layui-input-block">
                                <input type="number" style="width: 30%" name="sort" value="{if isset($task)}{$task.sort}{/if}" lay-verify="sort" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">浏览量</label>
                            <div class="layui-input-block">
                                <input type="number" style="width: 30%" name="play_count" value="{if isset($task)}{$task.play_count}{/if}" lay-verify="play_count" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">封面图</label>
                            <div class="layui-input-block" id="image">
                                {if isset($task) && $task.image}
                                <div class="upload-image-box">
                                    <img src="{$task.image.pic}" alt="">
                                    <input type="hidden" name="image" value="{$task.image.pic}">
                                    <div class="mask">
                                        <p><i class="fa fa-eye open_image" data-url="{$task.image.pic}"></i><i class="fa fa-trash-o delete_image" data-url="{$task.image.key}"></i></p>
                                    </div>
                                </div>
                                {/if}
                                <div class="upload-image" id="file_image" {if isset($task) && $task.image} style="display: none" {/if}>
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>上传图片</p>
                                </div>
                            </div>
                            <input type="file" name="file_image" style="display:none;">
                        </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">插入视频</label>
                            <div class="layui-input-block">
                                <input type="text" name="link_key" style="width:50%;display:inline-block;margin-right: 10px;" autocomplete="off" placeholder="请输入视频链接" class="layui-input">
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal but_title">插入视频</button>
                                <button type="button" id="ossUpload" class="layui-btn layui-btn-sm layui-btn-normal">上传视频</button>
                            </div>
                            <div class="layui-input-block video_show" style="width: 50%;margin-top: 20px;display:none">
                                <div class="layui-progress" style="margin-bottom: 10px">
                                    <div class="layui-progress-bar layui-bg-blue"></div>
                                </div>
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-danger cancel">取消</button>
                            </div>
                            <div class="layui-input-block">
                                <div class="layui-video-box">
                                    {if isset($task) && $task.link}
                                    <video style="width:100%;height: 100%!important;border-radius: 10px;" src="{$task.link.pic}" controls="controls">
                                        您的浏览器不支持 video 标签。
                                    </video>
                                    <input type="hidden" name="link" value="{$task.link.pic}">
                                    <div class="mark"><span class="layui-icon layui-icon-delete" data-key="{$task.link.key}" style="font-size: 30px; color: #1E9FFF;"></span><div>
                                    {else}
                                    <i class="layui-icon layui-icon-play"></i>
                                    {/if}
                                </div>
                            </div>
                            <div class="layui-form-mid layui-word-aux">输入链接将视为添加视频直接添加,请确保视频链接的正确性</div>
                        </div>
                        <div class="layui-form-item submit">
                            <div class="layui-input-block">
                                {if isset($task)}
                                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="save">立即修改</button>
                                {else}
                                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="save">立即提交</button>
                                <button class="layui-btn layui-btn-primary clone">取消</button>
                                {/if}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    layList.form.render();
    //初始化
    var file_image = $('#file_image'),
        windowindex = parent.layer.getFrameIndex(window.name),
        link = $('input[name="link_key"]'),
        bark_name = ossUpload.host,
        Help = {},
        id = {$id};

    /**
     *
     * 获取视频HTML
     * @param url
     * @param key
     * @return string
     * */
    Help.videoHtml=function(url,key) {
        return '<video style="width:100%;height: 100%!important;border-radius: 10px;" src="'+url+'" controls="controls">\n' +
            '您的浏览器不支持 video 标签。\n' +
            '</video>' +
            '<input type="hidden" name="link" value="'+url+'">' +
            '<div class="mark"><span class="layui-icon layui-icon-delete" data-key="'+key+'" style="font-size: 30px; color: #1E9FFF;"></span><div>';
    }

    /**
     * 视频播放事件
     *
     * */
    Help.video_read = function(){
        $('.layui-video-box .mark .layui-icon-delete').on('click',function () {
            var key_name=$(this).data('key');
            if(key_name) {
                ossUpload.delete(key_name).then(function (res) {
                    Help.addPayIocn('layui-video-box');
                }).catch(function (res) {
                    Help.addPayIocn('layui-video-box');
                })
            }else{
                Help.addPayIocn('layui-video-box');
            }
        });
    }

    /**
     * 进度条事件
     * */
    Help.percentHide = function(){
        $('.video_show').hide();
        $('.layui-progress-bar').css('width',0);
    }

    /**
     * 添加播放图标事件
     * */
    Help.addPayIocn = function(className){
        $('.'+className).html('<i class="layui-icon layui-icon-play"></i>');
    }

    Help.show = function() {
        $('#image .delete_image').on('click',function () {
            $(this).parents('.upload-image-box').remove();
            file_image.show();
        })
    }

    $('.but_title').click(function () {
        if(link.val()){
            var url=link.val();
            if(url.substr(0,7).toLowerCase() == "http://" || url.substr(0,8).toLowerCase() == "https://"){
                var keyIndex = url.indexOf(bark_name);
                var key = keyIndex===-1 ? '':url.substr(keyIndex+bark_name.length+1);
                $('.layui-video-box').html(Help.videoHtml(url,key));
                Help.video_read();
            }else{
                layList.msg('请输入正确的视频链接');
                link.val('');
                $('.layui-video-box').html('<i class="layui-icon layui-icon-play"></i>');
            }
        }
    })

    ossUpload.upload({
        id: 'ossUpload',
        FilesAddedSuccess:function(file){
            $('.video_show').show();
        },
        uploadIng:function (file) {
            $('.layui-progress-bar').css('width',file.percent+'%');
        },
        init:function (uploader) {
            $('.stop').on('click',function () {
                uploader.stop();
                Help.percentHide();
            })
        },
        success:function (res) {
            console.log(res);
            layList.msg('上传成功');
            Help.percentHide();
            $('.layui-video-box').html(Help.videoHtml(res.url,res.key));
            Help.video_read();
        },
        fail:function (err) {
            layList.msg(err);
            $('.video_show').hide();
            $('.layui-progress-bar').css('width',0);
        }
    });

    /**
     * 选择图片回调事件
     * */
    var changeIMG = function(res,url){
        file_image.parents('.layui-input-block').prepend(ossUpload.getImageHtml(url,'image',''));
        file_image.hide();
        ossUpload.LoadEvent();
        Help.show();
    }

    /**
     * 选择图片
     */
    file_image.on('click',function () {
        ossUpload.createFrame('选择任务封面图',{},{w:700});
    });

    Help.show();
    Help.video_read();

    layList.search('save',function (data) {
        delete data.file_image;
        delete data.link_key;
        delete data.video;
        console.log(data);
        if(data.special_id == 0) return layList.msg('请选择专题');
        if(!data.title) return layList.msg('请填写任务标题');
        if(!data.image) return layList.msg('请上传任务封面');
        if(!data.link) return layList.msg('请上传视频或者插入链接(输入连接后点击确认添加)');
        layList.basePost(layList.U({a:'save_task',q:{id:id}}),data,function (res) {
            layList.msg(res.msg,function () {
                parent.layer.close(windowindex);
            })
        },function (res) {
            layList.msg(res.msg);
        });
    });
</script>
{/block}