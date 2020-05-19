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
                                <label class="layui-form-label">会员名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" lay-verify="title" class="layui-input" placeholder="请输入会员名称">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">是否显示</label>
                                <div class="layui-input-block">
                                    <select name="is_show" lay-verify="is_show">
                                        <option value="">全部</option>
                                        <option value="1">显示</option>
                                        <option value="0">不显示</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">是否永久</label>
                                <div class="layui-input-block">
                                    <select name="is_forever" lay-verify="is_forever">
                                        <option value="">全部</option>
                                        <option value="1">永久</option>
                                        <option value="0">非永久</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">时间范围</label>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="start_time" value="" placeholder="开始时间" id="start_time" class="layui-input">
                                </div>
                                <div class="layui-form-mid">-</div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="end_time" value="" placeholder="结束时间" id="end_time" class="layui-input">
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
                <div class="layui-card-header">会员设置列表</div>
                <div class="layui-card-body">
                    <script type="text/html" id="too">
                        <div class="layui-btn-container">
                            <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">添加会员设置</button>
                        </div>
                    </script>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="money">
                        <span class="layui-badge">￥{{d.money}}</span>
                    </script>
                    <script type="text/html" id="is_publish">
                        {{# if(d.is_publish==0){ }}
                        <button class="layui-btn layui-btn-warm layui-btn-xs" lay-event="is_publish">立即发布</button>
                        {{# }else{ }}
                        <button class="layui-btn layui-btn-xs">已发布</button>
                        {{# } }}
                    </script>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</button>
                        <button type="button" class="layui-btn layui-btn-xs" lay-event="gift"><i class="layui-icon layui-icon-edit"></i>礼品</button>
                        <button type="button" class="layui-btn layui-btn-xs" lay-event="delete"><i class="layui-icon layui-icon-edit"></i>删除</button>
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
    layList.date({elem:'#start_time',theme:'#393D49',type:'datetime'});
    layList.date({elem:'#end_time',theme:'#393D49',type:'datetime'});
    layList.tableList({o:'List',t:'too'},layList.U({a:'system_vip_list'}),function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id'},
            {field: 'title', title: '会员名'},
            //{field: 'valid_date', title: '有效期'},
            {field: 'money', title: '购买金额(￥)',templet:'#money'},
            //{field: 'discount', title: '享受商品折扣(%)'},
            {field: 'is_show', title: '是否显示',templet:'#is_show'},
            //{field: 'is_forever', title: '是否永久'},
            {field: 'is_publish', title: '是否发布',templet:'#is_publish'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'15%'},
        ];
    });
    layList.search('search',function(where){
        if(where.start_time!='' && where.end_time=='') return layList.msg('请选择结束时间');
        if(where.end_time!='' && where.start_time=='') return layList.msg('请选择开始时间');
        layList.reload(where,true);
    });
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    layList.toolbar(function (layEvent) {
        switch (layEvent){
            case 'add':
                $eb.createModalFrame('新增会员设置',layList.U({a:'add_sytem_vip'}));
                break;
        }
    });
    layList.tool(function (layEvent,data,obj) {
        switch (layEvent){
            case "gift":
                $eb.createModalFrame('会员礼品管理',layList.U({a:'add_sytem_vip_gift',q:{id:data.id}}),{w:1000});
                break;
            case 'add':
                $eb.createModalFrame('新增会员设置',layList.U({a:'add_sytem_vip'}));
                break;
            case 'edit':
                $eb.createModalFrame('修改会员设置',layList.U({a:'add_sytem_vip',q:{id:data.id}}));
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
            case 'is_publish':
                var url=layList.U({a:'publish',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            obj.update({is_publish:1});
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{title:"确认发布该会员吗?",text:'发布后无法修改会员时效和会员金额',confirm:'确认发布'});
                break;
        }
    });
</script>
{/block}