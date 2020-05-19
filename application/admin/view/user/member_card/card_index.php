{extend name="public/container"}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <div class="layui-tab layui-tab-brief" lay-filter="tab">
        <ul class="layui-tab-title">
            <li lay-id="list" {eq name='activity_type' value='1'}class="layui-this" {/eq} >
            <a href="{eq name='activity_type' value='1'}javascript:;{else}{:Url('batch_index',['activity_type'=>1])}{/eq}">批次列表</a>
            </li>
            <li lay-id="list" {eq name='activity_type' value='2'}class="layui-this" {/eq}>
            <a href="{eq name='activity_type' value='2'}javascript:;{else}{:Url('card_index',['activity_type'=>2])}{/eq}">会员卡列表</a>
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
                                <label class="layui-form-label">批次</label>
                                <div class="layui-input-block">
                                    <select name="card_batch_id">
                                        <option value="">全部</option>
                                        {foreach $batch_list as $vo}
                                        <option value="{$vo.id}" {if condition="$card_batch_id eq $vo['id']"} selected {/if}>{$vo.title}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">卡号</label>
                                <div class="layui-input-block">
                                    <input type="text" name="card_number" class="layui-input" placeholder="请输入卡号">
                                    <input type="hidden" name="activity_type" value="{$activity_type}">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">电话</label>
                                <div class="layui-input-block">
                                    <input type="text" name="phone" class="layui-input" placeholder="请输入电话">

                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">是否领取</label>
                                <div class="layui-input-block">
                                    <select name="is_use">
                                        <option value="">全部</option>
                                        <option value="1">领取</option>
                                        <option value="0">未领取</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">是否激活</label>
                                <div class="layui-input-block">
                                    <select name="is_status">
                                        <option value="">全部</option>
                                        <option value="1">激活</option>
                                        <option value="0">冻结</option>
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
                        列表[会员卡列表],[排序]可进行快速修改,双击或者单击进入编辑模式,失去焦点可进行自动保存
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="layui-btn-container">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i>  刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="is_status">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_status' lay-text='激活|冻结'  {{ d.status == 1 ? 'checked' : '' }}>
                    </script>
                   <!-- <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a href="{:Url('add')}?id={{d.id}}" >
                                    <i class="fa fa-paste"></i> 编辑
                                </a>
                            </li>

                            <li>
                                <a lay-event='delect' href="javascript:void(0)">
                                    <i class="fa fa-trash"></i> 删除
                                </a>
                            </li>
                        </ul>
                    </script>-->
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
    },"{:Url('card_list',['card_batch_id'=>$card_batch_id])}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'8%',align: 'center'},
            {field: 'card_number', title: '卡号',align: 'center'},
            {field: 'card_password', title: '密码',align: 'center'},
            {field: 'username', title: '领取人',align: 'center'},
            {field: 'user_phone', title: '领取人电话',align: 'center'},
            {field: 'use_time', title: '领取时间',align: 'center'},
            {field: 'status', title: '是否激活', templet:'#is_status',align: 'center'},
            //{field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'},
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
    layList.switch('is_status',function (odj,value) {
        var is_status_value = 0
        if(odj.elem.checked==true){
            var is_status_value = 1
        }
        action.set_value('status',value,is_status_value,'member_card');
    });
    //快速编辑
   /* layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'title':
                action.set_value('title',id,value,'member_card_batch');
                break;
            case 'remark':
                action.set_value('remark',id,value,'member_card_batch');
                break;
            case 'use_day':
                action.set_value('use_day',id,value,'member_card_batch');
                break;
        }
    });*/
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    /*layList.tool(function (event,data,obj) {
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
    })*/

</script>
{/block}

