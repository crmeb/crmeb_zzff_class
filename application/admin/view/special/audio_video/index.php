{extend name="public/container"}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <!--<div class="layui-tab layui-tab-brief" lay-filter="tab">
        <ul class="layui-tab-title">
            <li lay-id="list" {eq name='activity_type' value='1'}class="layui-this" {/eq} >
            <a href="{eq name='activity_type' value='1'}javascript:;{else}{:Url('index',['activity_type'=>1, 'special_type'=>$special_type])}{/eq}">{$special_title}列表</a>
            </li>
            <li lay-id="list" {eq name='activity_type' value='2'}class="layui-this" {/eq}>
            <a href="{eq name='activity_type' value='2'}javascript:;{else}{:Url('source_index',['activity_type'=>2, 'special_type'=>$special_type])}{/eq}">{$special_title}素材列表</a>
            </li>
        </ul>
    </div>-->
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">专题名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="store_name" class="layui-input" placeholder="请输入专题名称,关键字,编号">
                                    <input type="hidden" name="activity_type" value="{$activity_type}">
                                    <input type="hidden" name="subject_id" value="{$subject_id}">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">所属二级分类</label>
                                <div class="layui-input-block">
                                    <select name="subject_id" lay-search="">
                                        <option value="0">全部</option>
                                        {volist name='subject_list' id='vo'}
                                        <option value="{$vo.id}">{$vo.name}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">是否显示</label>
                                <div class="layui-input-block">
                                    <select name="is_show">
                                        <option value="">全部</option>
                                        <option value="1">显示</option>
                                        <option value="0">隐藏</option>
                                    </select>
                                </div>
                            </div>
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
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索</button>
                                    <!--                                    <button class="layui-btn layui-btn-primary layui-btn-sm export"  lay-submit="export" lay-filter="export">-->
                                    <!--                                        <i class="fa fa-floppy-o" style="margin-right: 3px;"></i>导出</button>-->
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
                <div class="layui-card-body">
                    <div class="alert alert-info" role="alert">
                        列表[{$special_title}],[排序]可进行快速修改,双击或者单击进入编辑模式,失去焦点可进行自动保存
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-sm" onclick="action.open_add('{:Url('add',['special_type' =>$special_type])}','新增{$special_title}')"><i class="layui-icon layui-icon-add-1"></i>新增{$special_title}</button>
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i>  刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="recommend">
                        {{#  layui.each(d.recommend, function(index, item){ }}
                        <span class="layui-badge layui-bg-blue">{{item}}</span>
                        {{#  }); }}
                    </script>
                    <script type="text/html" id="is_pink">
                        {{# if(d.is_pink){ }}
                        <span class="layui-badge layui-bg-green">拼团开启</span>
                        {{# }else{ }}
                        <span class="layui-badge">拼团关闭</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="is_live_goods">
                        <input type='checkbox' name='is_live_goods' lay-skin='switch' value="{{d.id}}" lay-filter='is_live_goods' lay-text='是|否'  {{ d.is_live_goods == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;width: 80px;" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a  href="javascript:void(0)" onclick="action.open_add('{:Url('add')}?id={{d.id}}&special_type={$special_type}','编辑专题')">
                                    <i class="fa fa-paste"></i> 编辑专题
                                </a>
                            </li>
                            <li>
                                <a href="{:Url('ump.store_combination/combina_list')}?cid={{d.id}}&special_type={$special_type}" >
                                    <i class="fa fa-street-view"></i> 查看拼团
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="$eb.createModalFrame('{{d.title}}-推荐管理','{:Url('recommend')}?special_id={{d.id}}',{h:300,w:400})">
                                    <i class="fa fa-check-circle"></i> 推荐至首页
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="$eb.createModalFrame('{{d.title}}-拼团管理','{:Url('pink')}?special_id={{d.id}}',{h:500})">
                                    <i class="fa fa-users"></i> 拼团设置
                                </a>
                            </li>
                            <li>
                                <a lay-event='delect' href="javascript:void(0)">
                                    <i class="fa fa-trash"></i> 删除专题
                                </a>
                            </li>
                        </ul>
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
    var special_type = {$special_type} ? {$special_type} : 6;
    //实例化form
    layList.form.render();
    layList.date({elem:'#start_time',theme:'#393D49',type:'datetime'});
    layList.date({elem:'#end_time',theme:'#393D49',type:'datetime'});
    //加载列表
    layList.tableList({
        o:'List',
        done:function () {

        }
    },"{:Url('list',['subject_id'=>$subject_id, 'special_type'=>$special_type])}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'5%',align: 'center'},
            {field: 'title', title: '专题名称',edit:'title',align: 'center'},
            {field: 'subject_name', title: '所属分类',align: 'center'},
            {field: 'image', title: '封面图',templet:'#image',align: 'center'},
            {field: 'recommend', title: '首页推荐版块',templet:'#recommend',align: 'center'},
            {field: 'task_count', title: '课程数量',align: 'center'},
            {field: 'sales', title: '实际销量'},
            {field: 'fake_sales', title: '虚拟销量',edit:'fake_sales'},
            {field: 'is_pink', title: '拼团状态',templet:'#is_pink',align: 'center'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center'},
            /*{field: 'is_live_goods', title: '直播带货',templet:'#is_live_goods',align: 'center'},*/
            {field: 'is_show', title: '是否显示',templet:'#is_show',align: 'center'},
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
                layList.msg(res.msg);
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
        var is_show_value = 0
        if(odj.elem.checked==true){
            var is_show_value = 1
        }
        action.set_value('is_show',value,is_show_value,'special');
    });
/*    layList.switch('is_live_goods',function (odj,value) {
        var is_live_goods = 0
        if(odj.elem.checked==true){
            var is_live_goods = 1
        }
        layList.baseGet(layList.Url({
            a: 'set_live_goods',
            q: {special_id: value, is_live_goods: is_live_goods}
        }), function (res) {
            layList.msg(res.msg);
        });
    });*/
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'title':
                action.set_value('title',id,value,'special');
                break;
            case 'sort':
                action.set_value('sort',id,value,'special');
                break;
            case 'fake_sales':
                action.set_value('fake_sales',id,value,'special');
                break;
        }
    });

    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({a:'delete',q:{id:data.id, model_type:'special'}});
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

