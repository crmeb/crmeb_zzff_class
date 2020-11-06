{extend name="public/container"}
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
                                <label class="layui-form-label">时间范围</label>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="start_time" placeholder="开始时间" id="start_time" class="layui-input">
                                </div>
                                <div class="layui-form-mid">-</div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="end_time" placeholder="结束时间" id="end_time" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">用户名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="nickname" class="layui-input" placeholder="请输入用户名称">
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
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">直播间用户列表</div>
                <div class="layui-card-body">
                    <div class="alert alert-info" role="alert">
                        用户禁言填写时间为有效时间内禁止发言，不填写时间将永久禁止发言，禁言时间单位为：分钟；最终显示的是到期时间
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="layui-btn-container">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i>  刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="is_ban">
                        {{# if(d.is_ban){ }}
                        <span class="layui-badge layui-bg-green">禁言</span>
                        {{# }else{ }}
                        <span class="layui-badge layui-bg-gray">未禁言</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="is_open_ben">
                        {{# if(d.is_open_ben){ }}
                        <span class="layui-badge layui-bg-green">禁止</span>
                        {{# }else{ }}
                        <span class="layui-badge layui-bg-gray">未禁止</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="is_online">
                        {{# if(d.is_online){ }}
                        <span class="layui-badge layui-bg-green">在线</span>
                        {{# }else{ }}
                        <span class="layui-badge layui-bg-gray">下线</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="avatar">
                        <img style="cursor: pointer;width: 60px;" lay-event='open_image' src="{{d.avatar}}">
                    </script>
                    <script type="text/html" id="act">
                        <button class="layui-btn layui-btn-xs" lay-event='no_speaking'>
                            禁言
                        </button>
                        <button class="layui-btn layui-btn-xs" lay-event='no_entry'>
                            禁止进入
                        </button>
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
    var live_id='{$live_id}';
    //实例化form
    layList.form.render();
    layList.date({elem:'#start_time',theme:'#393D49',type:'datetime'});
    layList.date({elem:'#end_time',theme:'#393D49',type:'datetime'});
    //加载列表
    layList.tableList('List',"{:Url('get_live_user_list')}?live_id={$live_id}",function (){
        return [
            {field: 'avatar', title: '头像',align:"center",templet:'#avatar',width:'8%'},
            {field: 'nickname', title: '昵称',align:"center",width:'10%'},
            {field: 'visit_num', title: '访问次数',align:'center',width:'6%'},
            {field: 'is_online', title: '是否在线',align:'center',templet:'#is_online',width:'6%'},
            {field: 'is_ban', title: '是否禁言',align:'center',templet:'#is_ban',width:'8%'},
            {field: 'ban_time', title: '禁言到期时间',align:'center'},
            {field: 'is_open_ben', title: '是否禁止进入',align:'center',templet:'#is_open_ben',width:'8%'},
            {field: 'open_ben_time', title: '禁止进入直播间到期时间',align:'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'14%'},
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_live_user_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }
    //查询
    layList.search('search',function(where){
        if(where.end_time && !where.start_time) return layList.msg('请选择开始时间');
        if(where.start_time && !where.end_time) return layList.msg('请选择结束时间');
        layList.reload(where,true);
    });
    layList.switch('is_ban',function (odj,value) {
        action.set_value('is_ban',value,odj.elem.checked ? 1 : 0);
    });
    layList.switch('is_open_ben',function (odj,value) {
        action.set_value('is_open_ben',value,odj.elem.checked ? 1 : 0);
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
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
            case 'no_speaking':
                $eb.createModalFrame('禁止发言',layList.U({a:'live_no_speaking',q:{id:data.id}}),{w:890,h:450});
                break;
            case 'no_entry':
                $eb.createModalFrame('禁止进入',layList.U({a:'live_no_entry',q:{id:data.id}}),{w:890,h:450});
                break;
        }
    })
</script>
{/block}
