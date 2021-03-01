{extend name="public/container"}
{block name="head_top"}

{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">用户昵称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" lay-verify="title" class="layui-input" placeholder="请输入用户uid｜昵称">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">类别</label>
                                <div class="layui-input-block">
                                    <select name="type" lay-verify="type">
                                        <option value="">全部</option>
                                        <option value="6">免费</option>
                                        <option value="1">月卡</option>
                                        <option value="2">季卡</option>
                                        <option value="3">年卡</option>
                                        <option value="4">终身卡</option>
                                        <option value="5">卡密</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">会员获取列表</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="money">
                        <span class="layui-badge">￥{{d.price}}</span>
                    </script>
                    <script type="text/html" id="is_permanent">
                        {{# if(d.is_permanent==0){ }}
                        <button class="layui-btn layui-btn-xs">非永久</button>
                        {{# }else{ }}
                        <button class="layui-btn layui-btn-xs">永久</button>
                        {{# } }}
                    </script>
                    <script type="text/html" id="is_free">
                        {{# if(d.is_free==0){ }}
                        <button class="layui-btn layui-btn-xs">收费</button>
                        {{# }else{ }}
                        <button class="layui-btn layui-btn-xs">免费</button>
                        {{# } }}
                    </script>
<!--                    <script type="text/html" id="act">-->
<!--                        <button type="button" class="layui-btn layui-btn-xs" lay-event="delete"><i class="layui-icon layui-icon-edit"></i>删除</button>-->
<!--                    </script>-->
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
    layList.tableList({o:'List',t:'too'},layList.U({a:'member_record_list'}),function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',align: 'center'},
            {field: 'uid', title: '昵称/UID',align: 'center'},
            {field: 'title', title: '类别',align: 'center'},
            {field: 'validity', title: '有期期',align: 'center'},
            {field: 'price', title: '优惠价',align: 'center'},
            {field: 'code', title: '卡号',align: 'center'},
        ];
    });
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
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
        }
    });
</script>
{/block}
