{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form" action="">
                        <div class="layui-form-item">
                            <label class="layui-form-label">导航名称</label>
                            <div class="layui-input-block">
                                <input type="hidden" name="is_fixed" value="{if isset($recemmend)}{$recemmend.is_fixed}{else}{$is_fixed}{/if}">
                                <input type="text" name="title" lay-verify="title" value="{if isset($recemmend)}{$recemmend.title}{/if}" autocomplete="off" placeholder="导航名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">排序</label>
                            <div class="layui-input-block">
                                <input type="number" name="sort" lay-verify="sort" value="{if isset($recemmend)}{$recemmend.sort}{/if}" autocomplete="off" placeholder="排序" class="layui-input">
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
                        <div class="layui-form-item">
                            <label class="layui-form-label">图标</label>
                            <div class="layui-input-block" id="icon">
                                {if isset($recemmend) && $recemmend.icon}
                                <div class="upload-image-box">
                                       <img src="{$recemmend.icon}" alt="">
                                       <input type="hidden" name="icon" value="{$recemmend.icon}">
                                       <div class="mask">
                                           <p><i class="fa fa-eye open_image" data-url="{$recemmend.icon}"></i><i class="fa fa-trash-o delete_image" data-url="{$recemmend.icon_key}"></i></p>
                                       </div>
                                    </div>
                                {/if}
                                <div class="upload-image" id="file_icon" {if isset($recemmend) && $recemmend.icon} style="display: none" {/if}>
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>上传图片</p>
                                </div>
                                <input type="file" name="file_icon" style="display:none;">
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
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/OssUpload.js"></script>
{/block}
{block name="script"}
<script>
    //实例化form
    layList.form.render();
    //初始化
    var file_image=$('#file_icon'),id={$id};


    /**
     * 选择图片
     */
    file_image.on('click',function () {
        ossUpload.createFrame('选择图片',{},{w:700});
    });

    /**
     * 选择图片回调事件
     * */
    var changeIMG = function(res,url){
        file_image.parents('.layui-input-block').prepend(ossUpload.getImageHtml(url,'icon',''));
        file_image.hide();
        ossUpload.LoadEvent();
        deleteImage();
    }

    //提交
    layList.search('save',function(data){
        if(!data.title) return layList.msg('请输入标题');
        if(!data.icon || data.icon==undefined) return layList.msg('请上传图标');
        // if(!data.type) return layList.msg('请选择类型');
        // if(!data.typesetting) return layList.msg('请选择排版');
        layList.basePost(layList.U({a:'save_recemmend',q:{id:id}}),data,function (res) {
            layList.msg(res.msg,function () {
                parent.layer.close(windowindex);
                parent.$(".J_iframe:visible")[0].contentWindow.location.reload();
            })
        },function (res) {
            layList.msg(res.msg);
        });
    });

    function deleteImage (){
        $('#icon .delete_image').on('click',function () {
            $(this).parents('.upload-image-box').remove();
            file_image.show();
        })
    }

    deleteImage();
</script>
{/block}
