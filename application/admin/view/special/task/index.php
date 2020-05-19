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
                                <label class="layui-form-label">是否显示</label>
                                <div class="layui-input-block">
                                    <select name="is_show">
                                        <option value="">是否显示</option>
                                        <option value="1">显示</option>
                                        <option value="0">不显示</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">课程名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" class="layui-input" placeholder="请输入课程名称">
                                    <input type="hidden" name="coures_id" value="{$coures_id}">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">专题</label>
                                <div class="layui-input-block">
                                    <select name="special_id" lay-search="" lay-filter="special_id">
                                        <option value="0">请选专题</option>
                                        {volist name='specialList' id='item'}
                                        <option value="{$item.id}">{$item.title}</option>
                                        {/volist}
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
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">课程列表</div>
                <div class="layui-card-body">
                    <div class="alert alert-info" role="alert">
                        注:课程名称和排序可进行快速编辑;
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('add_task')}',{w:800})"><i class="layui-icon layui-icon-add-1"></i> 新增课程</button>
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i>  刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="course_name">
                        <p>{{d.course_name}} {{# if(d.live_id){ }}<span class="layui-badge layui-bg-green">直播</span> {{# } }}</p>
                    </script>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;width: 80px;" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        {{# if(d.live_id==0){ }}
                        <button class="layui-btn layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('add_task')}?id={{d.id}}',{w:800})">
                            <i class="fa fa-paste"></i> 编辑
                        </button>
                        {{# } }}
                        <button class="layui-btn layui-btn-xs" onclick="$eb.createModalFrame('编辑详情','{:Url('update_content')}?id={{d.id}}',{w:800})">
                            <i class="fa fa-paste"></i> 详情
                        </button>
                        <button class="layui-btn layui-btn-xs layui-btn-danger" lay-event='delete'>
                            <i class="fa fa-warning"></i> 删除
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
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('task_list')}?coures_id={$coures_id}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id'},
            {field: 'course_name', title: '专题名称',templet:'#course_name'},
            {field: 'title', title: '任务标题',edit:'title'},
            {field: 'play_count', title: '浏览量',edit:'play_count'},
            {field: 'image', title: '任务封面',templet:'#image'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',width:'7%'},
            {field: 'is_show', title: '是否显示',templet:'#is_show',width:'10%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'30%'},
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
            case 'play_count':
                action.set_value('play_count',id,value);
                break;
        }
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
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
                })
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })
</script>
{/block}
