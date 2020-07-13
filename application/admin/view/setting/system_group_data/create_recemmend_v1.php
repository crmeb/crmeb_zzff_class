{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form" action="">
                        <div class="layui-form-item">
                            <label class="layui-form-label">列表名称</label>
                            <div class="layui-input-block">
                                <input type="hidden" name="is_fixed" value="{if isset($recemmend)}{$recemmend.is_fixed}{else}{$is_fixed}{/if}">
                                <input type="hidden" name="is_show" value="{if isset($recemmend)}{$recemmend.is_show}{else}1{/if}">
                                <input type="text" name="title" lay-verify="title" value="{if isset($recemmend)}{$recemmend.title}{/if}" autocomplete="off" placeholder="列表名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">排序</label>
                            <div class="layui-input-block">
                                <input type="number" name="sort" lay-verify="sort" value="{if isset($recemmend)}{$recemmend.sort}{/if}" autocomplete="off" placeholder="排序" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label" style="margin-right: 28px">排版选择</label>
                                <div class="layui-input-inline">
                                    <select name="typesetting" lay-verify="typesetting">
                                        <option value="">请选择排版类型</option>
                                        <option value="1" {if isset($recemmend) && $recemmend.typesetting==1}selected{/if}>大图</option>
                                        <option value="2" {if isset($recemmend) && $recemmend.typesetting==2}selected{/if}>宫图</option>
                                        <option value="3" {if isset($recemmend) && $recemmend.typesetting==3}selected{/if}>小图</option>
                                        <option value="4" {if isset($recemmend) && $recemmend.typesetting==4}selected{/if}>左右切换</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label" style="margin-right: 28px">类型选择</label>
                                <div class="layui-input-inline">
                                    <select name="type" lay-verify="type">
                                        <option value="">请选择类型</option>
                                        <option value="0" {if isset($recemmend) && $recemmend.type==0}selected{/if}>专题</option>
                                        <!--<option value="1" {if isset($recemmend) && $recemmend.type==1}selected{/if}>资讯</option>-->
<!--                                        <option value="2" {if isset($recemmend) && $recemmend.type==2}selected{/if}>直播</option>-->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">一级分类</label>
                            <div class="layui-input-block">
                                <select name="grade_id" lay-verify="grade_id">
                                    <option value="0" {if isset($recemmend) && $recemmend.grade_id==0}selected{/if}>全部</option>
                                    {volist name='grade_list' id='item'}
                                    <option value="{$item.id}" {if isset($recemmend) && $recemmend.grade_id==$item.id}selected{/if}>{$item.name}</option>
                                    {/volist}
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">列表展示</label>
                            <div class="layui-input-block">
                                <input type="number" name="show_count" lay-verify="show_count" value="{if isset($recemmend)}{$recemmend.show_count}{/if}" autocomplete="off" placeholder="列表展示最大个数,超过后不显示" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <div class="layui-input-block">
                                {if isset($recemmend)}
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
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
<script type="text/javascript" src="{__PC_KS3}src/plupload.full.min.js"></script>
<script type="text/javascript" src="{__PC_KS3}src/ks3jssdk.js"></script>
<script type="text/javascript" src="{__PC_KS3}ks3.js"></script>
{/block}
{block name="script"}
<script>
    var id={$id};
    var mime_types='jpg,gif,png,JPG,GIF,PNG';
    //实例化form
    layList.form.render();
    //初始化
    JSY.Config();
    var file_image=$('#file_image'),windowindex =parent.layer.getFrameIndex(window.name);
    $('.clone').click(function () {
        parent.layer.close(windowindex);
    });
    file_image.on('click',function () {
        $('input[name="file_image"]').click();
    });
    $('input[name="file_image"]').change(function () {
        if(this.files.length > 1) return layList.msg('您上传的图片不能大与1张');
        var file=this.files[0];
        if(file){
            var extension = file.name.split('.').pop(),timestamp = new Date().getTime(),key='image/' + timestamp+'.'+extension;
            if(mime_types.indexOf(extension)===-1) return layList.msg('您上传的图片格式不正确');
            Ks3.putObject({
                Key: key,
                File: file,
                ACL: 'public-read',
            }, function (err) {
                if(!err){
                    file_image.parents('.layui-input-block').prepend(JSY.getImgBoxHtml(key));
                    file_image.hide();
                    JSY.LoadEvent();
                    $('#image .delete_image').on('click',function () {
                        var that=this;
                        Ks3.delObject({Key: $(this).data('url')},function () {
                            $(that).parents('.upload-image-box').remove();
                            file_image.show();
                        },function () {
                            $(that).parents('.upload-image-box').remove();
                            file_image.show();
                        });
                    })
                }else{
                    layList.msg(JSON.stringify(err));
                }
            });
        }
    });
    //提交
    layList.search('save',function(data){
        delete data.file_image;
        if(!data.title) return layList.msg('请输入标题');
        if(!data.type) return layList.msg('请选择类型');
        // if(!data.grade_id) return layList.msg('请选择年级部');
        if(!data.show_count) return layList.msg('请填写展示几个内容板块');
        if(!data.typesetting) return layList.msg('请选择排版');
        layList.basePost(layList.U({a:'save_recemmend',q:{id:id}}),data,function (res) {
            layList.msg(res.msg,function () {
                parent.layer.close(windowindex);
                parent.$(".J_iframe:visible")[0].contentWindow.location.reload();
            })
        },function (res) {
            layList.msg(res.msg);
        });
    });
    $('#image .delete_image').on('click',function () {
        var that=this;
        Ks3.delObject({Key: $(this).data('url')},function () {
            $(that).parents('.upload-image-box').remove();
            file_image.show();
        },function () {
            $(that).parents('.upload-image-box').remove();
            file_image.show();
        });
    })
</script>
{/block}
