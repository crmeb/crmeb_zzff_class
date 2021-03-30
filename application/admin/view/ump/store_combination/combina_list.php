{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">拼团列表</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">状态</label>
                                        <div class="layui-input-inline">
                                            <select name="status">
                                                <option value="">全部</option>
                                                <option value="1">进行中</option>
                                                <option value="2">已完成</option>
                                                <option value="3">未完成</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">时间范围</label>
                                        <div class="layui-input-inline" style="width: 200px;">
                                            <input type="text" name="start_time" placeholder="开始时间" autocomplete="off" id="start_time" class="layui-input">
                                        </div>
                                        <div class="layui-form-mid">-</div>
                                        <div class="layui-input-inline" style="width: 200px;">
                                            <input type="text" name="end_time" placeholder="结束时间" autocomplete="off" id="end_time" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">搜索</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="nickname" lay-verify="nickname" style="width: 100%" autocomplete="off" placeholder="请输入ID、订单号、用户昵称" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">搜索</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                {if $cid}
                                {if condition = "$special_type eq 4"}
                                <a type="button" class="layui-btn layui-btn-sm layui-btn-warm" href="{:Url('live.aliyun_live/special_live',['special_type' => $special_type])}">
                                    <i class="layui-icon layui-icon-return"></i> 返回专题</a>
                                {else/}
                                <a type="button" class="layui-btn layui-btn-sm layui-btn-warm" href="{:Url('special.special_type/index',['special_type' => $special_type])}">
                                    <i class="layui-icon layui-icon-return"></i> 返回专题</a>
                                {/if}
                                {/if}
                                <button type="button" class="layui-btn layui-btn-sm layui-hide" onclick="$eb.createModalFrame(this.innerText,'{:Url('create_pink_false')}')">
                                    <i class="layui-icon layui-icon-add-1"></i> 新增虚拟拼团</button>
                            </div>
                            <table id="List" lay-filter="List"></table>
                            <script type="text/html" id="uid">
                                <spen>{{d.nickname}}</spen>/<spen>{{d.uid}}</spen>
                            </script>
                            <script type="text/html" id="title">
                                <spen>{{d.title}}</spen>/<spen>{{d.cid}}</spen>
                            </script>
                            <script type="text/html" id="people">
                                <spen>{{d.people}}</spen>/<spen>{{d.count_people}}</spen>
                            </script>
                            <script type="text/html" id="status">
                                {{# if(d.status==1) { }}
                                <button class="layui-btn layui-btn-normal layui-btn-sm">进行中</button>
                                {{# }else if(d.status==2){ }}
                                <button class="layui-btn layui-btn-warm layui-btn-sm">已完成</button>
                                {{# }else if(d.status==3){ }}
                                <button class="layui-btn layui-btn-danger layui-btn-sm">未完成</button>
                                {{# } }}
                            </script>
                            <script type="text/html" id="info">
                                <button class="layui-btn layui-btn-xs" onclick="$eb.createModalFrame('查看详情','{:Url('order_pink')}?id={{d.id}}')">
                                    <i class="fa fa-eye"></i> 查看详情
                                </button>
                            </script>
                            <script type="text/html" id="act">
                                {{# if(d.status==1){ }}
                                <button class="layui-btn layui-btn-xs layui-btn-warm" lay-event='down'>
                                    <i class="fa fa-level-down" aria-hidden="true"></i> 下架
                                </button>
                                <button class="layui-btn layui-btn-xs layui-btn-normal" lay-event='helpe'>
                                    <i class="fa fa-users" aria-hidden="true"></i> 助力
                                </button>
                                {{# }else{ }}
                                <button class="layui-btn layui-btn-xs layui-btn-disabled">
                                    <i class="fa fa-level-down" aria-hidden="true"></i> 下架
                                </button>
                                <button class="layui-btn layui-btn-xs layui-btn-disabled">
                                    <i class="fa fa-users" aria-hidden="true"></i> 助力
                                </button>
                                {{# }}}
                                <button class="layui-btn layui-btn-xs layui-btn-danger" lay-event='delete'>
                                    <i class="fa fa-trash" aria-hidden="true"></i> 删除
                                </button>
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script type="text/javascript">
    //实例化form
    layList.form.render();

    layList.date({elem:'#start_time',theme:'#393D49',type:'datetime'});
    layList.date({elem:'#end_time',theme:'#393D49',type:'datetime'});
    //加载列表
    layList.tableList('List',"{:Url('get_pink_list')}?cid={$cid}",function (){
        return [
            {field: 'uid', title: '开团团长',templet:'#uid',align: 'center'},
            {field: 'order_id', title: '订单号',align: 'center'},
            {field: 'add_time', title: '开团时间',align: 'center'},
            {field: 'stop_time', title: '结束时间',align: 'center'},
            {field: 'title', title: '拼团产品',templet:'#title',align: 'center'},
            {field: 'people', title: '拼团情况',templet:'#people',width:'5%',align: 'center'},
            {field: 'people_true', title: '真实人数',width:'5%',align: 'center'},
            {field: 'status', title: '状态',templet:'#status',width:'5%',align: 'center'},
            {field: 'info', title: '查看详情',templet:'#info',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'13%'},
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }
    //查询
    layList.search('search',function(where){
        where.data=where.start_time+'-'+where.end_time;
        delete where.start_time;
        delete where.end_time;
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
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'title':
                action.set_value('title',id,value);
                break;
            case 'sort':
                action.set_value('sort',id,value);
                break;
        }
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delete':
                var url=layList.U({a:'delete_pink',q:{id:data.id}});
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
                })
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
            case 'down':
                var url=layList.U({a:'down_pink',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200){
                            $eb.$swal('success',res.data.msg);
                            window.location.reload();
                        }else
                            return Promise.reject(res.data.msg || '删除失败');
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{title:'您确定要下架此条拼团吗?',text:'下架后将此团的所有参团者下架。下架后将自动执行退款,退款操作请前往订单管理',confirm:'是的我要下架'})
                break;
            case 'helpe':
                $eb.createModalFrame('助力拼团',layList.U({a:'helpe_pink',q:{id:data.id}}),{h:360});
                break;
        }
    })
</script>
{/block}
