{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">导航列表</div>
                <div class="layui-card-body">
                    <div class="alert alert-info" role="alert">
                        注:列表名称和排序可进行快速编辑;
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('create_recemmend')}',{w:800,h:500})">新建固定跳转导航</button>
                        <button type="button" class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('create_recemmend_custom')}')">新建自定义跳转导航</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="icon">
                        {{# if(d.icon) { }}
                        <img style="cursor: pointer" lay-event='open_image' src="{{d.icon}}">
                        {{# } }}
                    </script>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            {{# if(d.type == 3) { }}
                            <li>
                                <div onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('create_recemmend_custom')}?id={{d.id}}')">
                                    <i class="fa fa-paste"></i> 编辑
                                </div>
                            </li>
                            {{# }else{ }}
                            <li>
                                <div onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('create_recemmend')}?id={{d.id}}',{h:480})">
                                    <i class="fa fa-paste"></i> 编辑
                                </div>
                            </li>
                            {{# } }}
                            <li>
                                <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('recemmend_content')}?id={{d.id}}')">
                                    <i class="fa fa-list-ul"></i> 内容管理
                                </a>
                            </li>
                            <li>
                                <div onclick="$eb.createModalFrame(this.innerText,'{:Url('recemmend_banner')}?id={{d.id}}',{w:900})">
                                    <i class="fa fa-file-image-o"></i>  Banner
                                </div>
                            </li>
                            <li>
                                <div lay-event='delete'>
                                    <i class="fa fa-trash"></i> 删除
                                </div>
                            </li>
                        </ul>
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('recommend_list',['is_fixed' => 1])}",function (){
        return [
            {field: 'title', title: '导航名称',edit:'title'},
            {field: 'type_ting', title: '列表模式'},
            {field: 'type_name', title: '导航类型'},
            {field: 'icon', title: '图标',templet:'#icon'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort'},
            {field: 'grade_title', title: '一级分类名称'},
            {field: 'number', title: '数量',width:'5%'},
            {field: 'is_show', title: '是否显示',templet:'#is_show'},
            {field: 'add_time', title: '创建时间'},
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
    //监听并执行排序
    layList.sort(['sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delete':
                var url=layList.U({a:'delete_recomm',q:{id:data.id}});
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