{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-input-block .layui-video-box {
        width: 50%;
        height: 180px;
        border-radius: 10px;
        background-color: #707070;
        margin-top: 10px;
        position: relative;
        overflow: hidden;
    }

    .layui-input-block .layui-video-box i {
        color: #fff;
        line-height: 180px;
        margin: 0 auto;
        width: 50px;
        height: 50px;
        display: inherit;
        font-size: 50px;
    }

    .layui-input-block .layui-video-box .mark {
        position: absolute;
        width: 100%;
        height: 30px;
        top: 0;
        background-color: rgba(0, 0, 0, .5);
        text-align: center;
    }
</style>
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/OssUpload.js"></script>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">{if isset($data)}修改数据{else}新增数据{/if}</div>
                <div class="layui-card-body">
                    <form class="layui-form" action="">
                        {if condition ="isset($data.id)"}
                        <input type="hidden" id ="id" name="id" lay-filter="id" value="{$data.id}" lay-verify="sort" autocomplete="off" class="layui-input">
                        {/if}
                        <div class="layui-form-item">
                            <label class="layui-form-label">类型</label>
                            <div class="layui-input-block">
                                <input type="radio" name="type" lay-filter="type" value="0" title="专题" {if isset($data)}{if
                                       $data.type==0}checked{/if}{else}checked{/if}>
                                <input type="radio" name="type" lay-filter="type" value="1" title="分类" {if isset($data)}{if
                                       $data.type==1}checked{/if}{/if}>
                            </div>
                        </div>
                        <div class="layui-form-item" id="select">

                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">标题</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" value="{if isset($data)}{$data.title}{/if}"
                                       lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">简介</label>
                            <div class="layui-input-block">
                                <input type="text" name="info"
                                       value="{if isset($data)}{$data.info}{/if}" lay-verify="sort" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">排序</label>
                            <div class="layui-input-block">
                                <input type="number" name="sort" value="{if isset($data)}{$data.sort}{/if}"
                                       lay-verify="sort" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">显示</label>
                            <div class="layui-input-block">
                                <input type="radio" name="status" lay-filter="type" value="1" title="显示" {if isset($data)}{if
                                       $data.status==1}checked{/if}{else}checked{/if}>
                                <input type="radio" name="status" lay-filter="type" value="0" title="隐藏" {if isset($data)}{if
                                       $data.status==0}checked{/if}{/if}>
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">封面图</label>
                            <div class="layui-input-block" id="image">
                                {if isset($data) && $data.pic}
                                <div class="upload-image-box">
                                    <img src="{$data.pic}" alt="">
                                    <input type="hidden" name="image" value="{$data.pic}">
                                    <div class="mask">
                                        <p><i class="fa fa-eye open_image" data-url="{$data.pic}"></i><i class="fa fa-trash-o delete_image" data-url="{$data.pic}"></i></p>
                                    </div>
                                </div>
                                {/if}
                                <div class="upload-image" id="file_image" {if isset($data) && $data.pic} style="display: none" {/if}>
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>上传图片</p>
                                </div>
                            </div>
                        </div>
                        <div class="layui-input-block">
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
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    var specialList = {$specialList}, cateList = {$cateList},select_id = <?=isset($data['select_id']) ? (int)$data['select_id']: 0;?>;
    var dataType = <?=isset($data['type']) ? (int)$data['type']: 0;?>;
    layList.form.render();
    //初始化
    var file_image = $('#file_image'), windowindex = parent.layer.getFrameIndex(window.name), Help = {};

    Help.show = function () {
        $('#image .delete_image').on('click', function () {
            $(this).parents('.upload-image-box').remove();
            file_image.show();
        })
    }

    Help.getTypeHtml = function (type) {
        $('#select').html('');
        var name = 'select_id';
        var html = '<label class="layui-form-label">请选' + (type == 1 ? '分类' : '专题') + '</label>\n' +
            '                            <div class="layui-input-block">\n' +
            '                                <select name="' + name + '" lay-filter="' + name + '">\n';
        if (type == 1) {
            $.each(cateList, function (key,item) {
                html += '<option value="' + item.id + '" '+(select_id == item.id ? 'selected': '')+'>' + item.title + '</option>\n'
            })
        } else {
            $.each(specialList, function (key,item) {
                html += '<option value="' + item.id + '" '+(select_id == item.id ? 'selected': '')+'>' + item.title + '</option>\n'
            })
        }
        html += '                                </select>\n' +
            '                            </div>';

        $('#select').html(html);
        layList.form.render('select');
    }

    layList.form.on('radio(type)', function (data) {
        Help.getTypeHtml(data.value);
        console.log(data.value)
    });

    /**
     * 选择图片回调事件
     * */
    var changeIMG = function (res, url) {
        file_image.parents('.layui-input-block').prepend(ossUpload.getImageHtml(url, 'image', ''));
        file_image.hide();
        ossUpload.LoadEvent();
        Help.show();
    }

    /**
     * 选择图片
     */
    file_image.on('click', function () {
        ossUpload.createFrame('选择任务封面图', {}, {w: 700});
    });

    Help.show();
    Help.getTypeHtml(dataType);

    layList.search('save', function (data) {
        console.log(data);
        layList.basePost(layList.U({a: 'save_group_data',p:{ name : "home_activity" }}), data, function (res) {
            layList.msg(res.msg, function () {
                parent.layer.close(windowindex);
            })
        }, function (res) {
            layList.msg(res.msg);
        });
    });
</script>
{/block}