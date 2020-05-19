{extend name="public/container"}
{block name="head_top"}

{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">会员等级列表</div>
                <div class="layui-card-body">
<!--                    <script type="text/html" id="too">-->
<!--                        <div class="layui-btn-container">-->
<!--                            <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">添加会员等级</button>-->
<!--                        </div>-->
<!--                    </script>-->
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
                    <script type="text/html" id="is_publish">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_publish' lay-text='开启|关闭'  {{ d.is_publish == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="is_free">
                        {{# if(d.is_free==0){ }}
                        <button class="layui-btn layui-btn-xs">收费</button>
                        {{# }else{ }}
                        <button class="layui-btn layui-btn-xs">免费</button>
                        {{# } }}
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</button>
<!--                        <button type="button" class="layui-btn layui-btn-xs" lay-event="delete"><i class="layui-icon layui-icon-edit"></i>删除</button>-->
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
    layList.tableList({o:'List',t:'too'},layList.U({a:'membership_vip_list'}),function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',align: 'center'},
            {field: 'title', title: '会员名',align: 'center'},
            {field: 'vip_day', title: '有效期/天',align: 'center'},
            {field: 'original_price', title: '原价',align: 'center'},
            {field: 'price', title: '优惠价',templet:'#money',align: 'center'},
            {field: 'is_publish', title: '状态',templet:'#is_publish',align: 'center'},
            {field: 'sort', title: '排序/倒序',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'15%'},
        ];
    });
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('is_publish',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_publish',p:{is_publish:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_publish',p:{is_publish:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    layList.toolbar(function (layEvent) {
        switch (layEvent){
            case 'add':
                $eb.createModalFrame('新增会员设置',layList.U({a:'add_vip'}));
                break;
        }
    });
    layList.tool(function (layEvent,data,obj) {
        switch (layEvent){
            case 'add':
                $eb.createModalFrame('新增会员设置',layList.U({a:'add_vip'}));
                break;
            case 'edit':
                $eb.createModalFrame('修改会员设置',layList.U({a:'add_vip',q:{id:data.id}}));
                break;
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