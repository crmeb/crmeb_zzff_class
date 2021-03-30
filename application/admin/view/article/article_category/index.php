{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">新闻分类</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <select name="status">
                                                <option value="">是否显示</option>
                                                <option value="1" {eq name="$where.status" value="1"}selected="selected"{/eq}>显示</option>
                                                <option value="0" {eq name="$where.status" value="0"}selected="selected"{/eq}>不显示</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <input type="text" name="title" value="{$where.title}" placeholder="请输入关键词" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <button class="layui-btn layui-btn-normal layui-btn-sm">搜索</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                <button type="button" class="layui-btn layui-btn-normal" data-type="add">添加新闻分类</button>
                            </div>
                            <table class="layui-table" lay-filter="table">
                                <thead>
                                    <tr>
                                        <th lay-data="{field:'id',align:'center'}">编号</th>
                                        <th lay-data="{field:'name',align:'center'}">分类昵称</th>
                                        <th lay-data="{field:'status',align:'center'}">状态</th>
                                        <th lay-data="{align:'center',toolbar:'#toolbar'}">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {volist name="list" id="vo"}
                                    <tr>
                                        <td>{$vo.id}</td>
                                        <td>{$vo.title}</td>
                                        <td>
                                            {if condition="$vo['status'] eq 1"}
                                            <i class="fa fa-check text-navy"></i>
                                            {else/}
                                            <i class="fa fa-close text-danger"></i>
                                            {/if}
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
<script type="text/html" id="toolbar">
    <a class="layui-btn layui-btn-normal layui-btn-xs" href="{:Url('article.article_v1/index',array('cid'=>2))}">查看</a>
    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
{/block}
{block name="script"}
<script>
    var $ = layui.jquery;
    var form = layui.form;
    var table = layui.table;

    form.render();
    table.init('table');

    table.on('tool(table)', function (obj) {
        var data = obj.data;
        var layEvent = obj.event;

        if ('edit' === layEvent) {
            $eb.createModalFrame('编辑','{:url('edit')}?id=' + data.id);
        } else if ('del' === layEvent) {
            $eb.$swal('delete',function(){
                $eb.axios.get('{:url('delete')}?id=' + data.id).then(function(res){
                    if(res.status == 200 && res.data.code == 200) {
                        $eb.$swal('success',res.data.msg);
                        obj.del();
                    }else
                        return Promise.reject(res.data.msg || '删除失败');
                }).catch(function(err){
                    $eb.$swal('error',err);
                });
            });
        }
    });

    $('.layui-btn').on('click', function () {
        var type = $(this).data('type');
        if ('add' === type) {
            $eb.createModalFrame('添加新闻分类',"{:Url('create')}");
        }
    });
</script>
{/block}