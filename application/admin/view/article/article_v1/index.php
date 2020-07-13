{extend name="public/container"}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <div class="layui-tab layui-tab-brief" lay-filter="tab">
        <ul class="layui-tab-title">
            <li lay-id="list" {eq name='type' value='1'}class="layui-this" {/eq} >
            <a href="{eq name='type' value='1'}javascript:;{else}{:Url('index',['type'=>1])}{/eq}">新闻列表</a>
            </li>
            <li lay-id="list" {eq name='type' value='2'}class="layui-this" {/eq}>
            <a href="{eq name='type' value='2'}javascript:;{else}{:Url('add_article',['type'=>2])}{/eq}">新闻添加</a>
            </li>
        </ul>
    </div>
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">新闻名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="store_name" class="layui-input" placeholder="请输入新闻名称,关键字,编号">
                                    <input type="hidden" name="type" value="{$type}">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">分类</label>
                                <div class="layui-input-block">
                                <select name="cid" aria-controls="editable" class="form-control input-sm">
                                    <option value="">所有分类</option>
                                    {volist name="cate" id="vo"}
                                    <option value="{$vo.id}" {eq name="cid" value="$vo.id"}selected="selected"{/eq}>{$vo.html}{$vo.title}</option>
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
                <div class="layui-card-body">
                    <div class="alert alert-info" role="alert">
                        列表[新闻名称],[排序]可进行快速修改,双击或者单击进入编辑模式,失去焦点可进行自动保存
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="recommend">
                        {{#  layui.each(d.recommend, function(index, item){ }}
                        <span class="layui-badge layui-bg-blue">{{item}}</span>
                        {{#  }); }}
                    </script>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="image_input">
                        <img style="cursor: pointer" lay-event='open_image' src="{{d.image_input}}">
                    </script>
                    <script type="text/html" id="act">
                        <a class="layui-btn layui-btn-xs" href="{:Url('add_article',['type'=>2])}?id={{d.id}}">
                            <i class="fa fa-paste"></i> 编辑
                        </a>
                        <!--<button class="layui-btn layui-btn-xs layui-btn-normal" onclick="$eb.createModalFrame('{{d.title}}-推荐管理','{:Url('recommend')}?article_id={{d.id}}',{h:300,w:400})">
                            <i class="fa fa-check-circle"></i> 推荐
                        </button>-->
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
    layList.tableList('List',"{:Url('article_list',['cid'=>$cid])}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'5%',align: 'center'},
            {field: 'title', title: '新闻名称',edit:'title',align: 'center'},
            {field: 'cate_name', title: '分类名称',align: 'center'},
            {field: 'image_input', title: '封面图',templet:'#image_input',align: 'center'},
            /*{field: 'recommend', title: '推荐至',templet:'#recommend',align: 'center'},*/
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center'},
            {field: 'is_show', title: '是否显示',templet:'#is_show',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'25%'},
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
            case 'fake_sales':
                action.set_value('fake_sales',id,value);
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
                $eb.openImage(data.image_input);
                break;
        }
    })
</script>
{/block}

