{extend name="public/container"}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <div class="layui-tab layui-tab-brief" lay-filter="tab">
        <ul class="layui-tab-title">
            <li lay-id="list" {eq name='type' value='1'}class="layui-this" {/eq} >
            <a href="{eq name='type' value='1'}javascript:;{else}{:Url('recemmend_banner',['type'=>1,'id'=>$id])}{/eq}">列表</a>
            </li>
            <li lay-id="list" class="layui-this">
            <a href="javascript:;">添加</a>
            </li>
        </ul>
    </div>
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form" action="">
                        <div class="layui-form-item">
                            <label class="layui-form-label">跳转链接</label>
                            <div class="layui-input-block">
                                <input type="hidden" name="id" value="{$id}">
                                <input type="text" name="url"  value="{if isset($banner)}{$banner.url}{/if}" autocomplete="off" placeholder="跳转链接" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">排序</label>
                            <div class="layui-input-block">
                                <input type="number" name="sort" lay-verify="sort" value="{if isset($banner)}{$banner.sort}{/if}" autocomplete="off" placeholder="排序" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">隐藏显示</label>
                            <div class="layui-input-block">
                                <input type="checkbox"  {if isset($banner) && $banner.is_show}checked {/if} name="is_show" lay-skin="switch" lay-filter="switchTest" lay-text="显示|隐藏">
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">封面图</label>
                            <div class="layui-input-block" id="image">
                                {if isset($banner) && $banner.pic}
                                <div class="upload-image-box">
                                    <img src="{$banner.pic}" alt="">
                                    <input type="hidden" name="pic" value="{$banner.pic}">
                                    <div class="mask">
                                        <p><i class="fa fa-eye open_image" data-url="{$banner.pic}"></i><i class="fa fa-trash-o delete_image" data-url="{$banner.pic_key}"></i></p>
                                    </div>
                                </div>
                                {/if}
                                <div class="upload-image" id="file_image" {if isset($banner) && $banner.pic} style="display: none" {/if}>
                                <div class="fiexd"><i class="fa fa-plus"></i></div>
                                <p>上传图片</p>
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <div class="layui-input-block">
                                {if isset($banner)}
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
</div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/OssUpload.js"></script>
{/block}
{block name="script"}
<script>
    var id={$id},banner_id={$banner_id},file_image = $('#file_image');
    //实例化form
    layList.form.render();
    //初始化

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
        file_image.parents('.layui-input-block').prepend(ossUpload.getImageHtml(url,'pic',''));
        file_image.hide();
        ossUpload.LoadEvent();
        deleteImage();
    }

    //提交
    layList.search('save',function(data){
        if(!data.pic || data.pic==undefined) return layList.msg('请上传图标');
        layList.basePost(layList.U({a:'save_recemmend_banner',q:{id:id,banner_id:banner_id}}),data,function (res) {
            layList.msg(res.msg,function () {
                location.href = getUrl({a:'recemmend_banner',q:{type:1},p:{id:id}});
            })
        },function (res) {
            layList.msg(res.msg);
        });
    });

    function deleteImage (){
        $('#image .delete_image').on('click',function () {
            $(this).parents('.upload-image-box').remove();
            file_image.show();
        })
    }

    deleteImage();

</script>
{/block}
