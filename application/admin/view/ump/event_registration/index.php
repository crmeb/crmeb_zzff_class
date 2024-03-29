{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">活动管理</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">活动名称</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="title" class="layui-input" placeholder="请输入活动名称">
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
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="action.open_add('{:Url('create',['id'=>''])}','添加活动')"><i class="layui-icon layui-icon-add-1"></i>添加活动</button>
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i>刷新</button>
                            </div>
                            <table id="List" lay-filter="List"></table>
                            <script type="text/html" id="is_show">
                                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                            </script>
                            <script type="text/html" id="image">
                                <img style="cursor: pointer;width: 80px;height: 40px;" lay-event='open_image' src="{{d.image}}">
                            </script>
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                                    <li>
                                        <a href="javascript:void(0)"  onclick="action.open_add('{:Url('viewStaff')}?id={{d.id}}','报名人员')" >
                                            <i class="fa fa-paste"></i> 报名人员
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)"  onclick="action.open_add('{:Url('create')}?id={{d.id}}','编辑')" >
                                            <i class="layui-icon layui-icon-edit"></i>编辑
                                        </a>
                                    </li>
                                    <li>
                                        <a lay-event='delect' href="javascript:void(0)">
                                            <i class="fa fa-trash"></i> 删除
                                        </a>
                                    </li>
                                </ul>
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
<script>
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({
        o:'List',
        done:function () {

        }
    },"{:Url('event_registration_list',[])}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'5%',align: 'center'},
            {field: 'title', title: '活动名称',align: 'center'},
            {field: 'image', title: '图片', templet:'#image',align: 'center'},
            {field: 'address', title: '地址', align: 'center'},
            {field: 'number', title: '活动人数',align: 'center'},
            {field: 'is_show', title: '状态',templet:'#is_show',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'},
        ];
    });
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    })
    function dropdown(that){
        var oEvent = arguments.callee.caller.arguments[0] || event;
        oEvent.stopPropagation();
        var offset = $(that).offset();
        var top=offset.top-$(window).scrollTop();
        var index = $(that).parents('tr').data('index');
        $('.layui-nav-child').each(function (key) {
            if (key != index) {
                $(this).hide();
            }
        })
        if($(document).height() < top+$(that).next('ul').height()){
            $(that).next('ul').css({
                'padding': 10,
                'top': - ($(that).parent('td').height() / 2 + $(that).height() + $(that).next('ul').height()/2),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }else{
            $(that).next('ul').css({
                'padding': 10,
                'top':$(that).parent('td').height() / 2 + $(that).height(),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }
    }
    //自定义方法
    var action= {
        set_value: function (field, id, value, model_type) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value, model_type:model_type}
            }), function (res) {
                console.log(res);
                layList.msg(res.msg);
            }, function (err) {
                layList.msg(err.msg);
            });
        },
        //打开新添加页面
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['100%', '100%']
                ,title: title
                ,shade: 0.6 //遮罩透明度
                ,maxmin: true //允许全屏最小化
                ,anim: 1 //0-6的动画形式，-1不开启
                ,content: url
                ,end:function() {
                    location.reload();
                }
            });
        }
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
                action.set_value('title',id,value,'member_card_batch');
                break;
            case 'number':
                action.set_value('remark',id,value,'member_card_batch');
                break;
        }
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
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

