{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">状态</label>
                                        <div class="layui-input-inline">
                                            <select name="status">
                                                <option value=""></option>
                                                <option value="1" {eq name="where.status" value="1"}selected="selected"{/eq}>显示</option>
                                                <option value="2" {eq name="where.status" value="2"}selected="selected"{/eq}>不显示</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm">搜索</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('create',array('gid'=>$gid))}')">添加数据</button>
                            </div>
                            <table class="layui-table">
                                <thead>
                                    <tr>
                                        <th>编号</th>
                                        {volist name="fields" id="vo"}
                                        <th>{$vo.name}</th>
                                        {/volist}
                                        <th>状态</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {volist name="list" id="vo"}
                                    <tr>
                                        <td>{$vo.id}</td>
                                        {volist name="fields" id="item"}
                                        <td>
                                            {notempty name="$vo.value[$item['title']]['value']"}
                                            {$vo.value[$item['title']]['value']}
                                            {/notempty}
                                        </td>
                                        {/volist}
                                        <td>
                                            {if condition="$vo.status eq 1"}
                                            <i class="layui-icon layui-icon-ok layui-bg-blue"></i>
                                            {elseif condition="$vo.status eq 2"/}
                                            <i class="layui-icon layui-icon-close layui-bg-red"></i>
                                            {/if}
                                        </td>
                                        <td>
                                            <button type="button" class="layui-btn layui-btn-normal layui-btn-xs"  onclick="$eb.createModalFrame('编辑','{:Url('edit',array('gid'=>$gid,'id'=>$vo['id']))}')"> 
                                                <i class="layui-icon layui-icon-edit"></i>编辑
                                            </button>
                                            <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" data-url="{:Url('delete',array('id'=>$vo['id']))}">
                                                <i class="layui-icon layui-icon-delete"></i>删除
                                            </button>
                                        </td>
                                    </tr>
                                    {/volist}
                                </tbody>
                            </table>
                            {include file="public/inner_page"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    var form = layui.form;

    form.render();

    $('.layui-btn-danger').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                console.log(res);
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                    _this.parents('tr').remove();
                }else
                    return Promise.reject(res.data.msg || '删除失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        })
    });
    $(".image").on('click',function (e) {
        var images = $(this).data('image');
        $eb.openImage(images);
    })
</script>
{/block}
