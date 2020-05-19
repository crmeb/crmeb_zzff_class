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
                        用户禁言填写时间为有效时间内禁止发言，不填写时间将永久禁止发言，禁言时间单位为：分钟
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="layui-btn-container">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i>  刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="is_ban">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_ban' lay-text='是|否'  {{ d.is_ban == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="is_open_ben">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_open_ben' lay-text='是|否'  {{ d.is_open_ben == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="time">
                       <p>首次：{{d._add_time}}</p>
                       <p>历史：{{d._last_time}}</p>
                    </script>
                    <script type="text/html" id="is_online">
                        {{# if(d.is_online){ }}
                        <span class="layui-badge layui-bg-green">在线</span>
                        {{# }else{ }}
                        <span class="layui-badge layui-bg-gray">下线</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="avatar">
                        <img style="cursor: pointer" lay-event='open_image' src="{{d.avatar}}">
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
            {field: 'avatar', title: '头像',align:"center",templet:'#avatar'},
            {field: 'nickname', title: '昵称',align:"center"},
            {field: 'last_ip', title: '访问IP',align:'center'},
            {field: 'visit_num', title: '访问次数',align:'center'},
            {field: 'is_online', title: '是否在线',align:'center',templet:'#is_online'},
            {field: 'is_ban', title: '是否禁言',align:'center',templet:'#is_ban'},
            {field: 'ban_time', title: '禁言时间',align:'center',edit:'ban_time'},
            {field: 'is_open_ben', title: '是否禁止进入',align:'center',templet:'#is_open_ben'},
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
            case 'ban_time':
                action.set_value('ban_time',id,value);
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
                var url=layList.U({a:'del_guest',q:{id:data.id}});
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
            case 'look':
                $eb.createModalFrame('直播间号【'+stream_name+'】的直播回放,回放时间：'+data.StartTime+' - '+data.EndTime,layList.U({a:'live_record_look',q:{record_url:data.RecordUrl}}),{w:890,h:450});
                break;
            case 'download':
                $eb.createModalFrame('直播间号【'+stream_name+'】的直播下载',layList.U({a:'download',q:{record_url:data.RecordUrl}}),{w:900});
                break;
            case 'set_record':
                var url = layList.U({a:'set_value',q:{stream_name:stream_name,field:'playback_record_id',value:data.RecordId}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{title:'设置为直播间回放',text:'设置成功后，没有开启直播将自动播放此回放',confirm:'是的，我要设置'});
                break;
            case 'del_record':
                var url = layList.U({a:'set_value',q:{stream_name:stream_name,field:'playback_record_id',value:''}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{title:'取消直播间回放',text:'设置成功后，没有开启直播将不会回放',confirm:'是的，我要取消'});
                break;
        }
    })
</script>
{/block}
