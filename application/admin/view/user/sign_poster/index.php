{extend name="public/container"}
{block name="head_top"}

{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">签到海报列表</div>
                <div class="layui-card-body">
                        <div class="layui-btn-container">
                            <button type="button" class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame('新增海报','{:Url('create')}',{w:800,h:600})" ><i class="layui-icon layui-icon-add-1"></i>新增海报</button>
                        </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</button>
                        <button type="button" class="layui-btn layui-btn-xs" lay-event="delete"><i class="fa fa-trash"></i>删除</button>
                    </script>
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

    layList.tableList({o:'List'},layList.U({a:'getSignPosterList'}),function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'10%',align:'center'},
            {field: 'sign_time', title: '签到时间',align:'center'},
            {field: 'poster', title: '海报', event:'open_image', width: '30%',align: 'center', templet: '<p><img class="avatar" style="cursor: pointer" class="open_image" data-image="{{d.poster}}" src="{{d.poster}}" ></p>'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'20%'},
        ];
    });
    layList.tool(function (layEvent,data,obj) {
        switch (layEvent){
            case 'delete':
                var url=layList.U({a:'delete',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            obj.del();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                });
                break;
            case 'edit':
                $eb.createModalFrame('编辑',layList.Url({a:'edit',p:{id:data.id}}));
                break;
            case 'open_image':
                $eb.openImage(data.poster);
                break;
        }
    });
</script>
{/block}