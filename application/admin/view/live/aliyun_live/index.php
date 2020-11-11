{extend name="public/container"}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <div class="layui-tab layui-tab-brief" lay-filter="tab">
        <ul class="layui-tab-title">
            <li lay-id="list" {eq name='type' value='1'}class="layui-this" {/eq} >
            <a href="{eq name='type' value='1'}javascript:;{else}{:Url('special_live',['special_type'=>4,'type'=>1])}{/eq}">直播列表</a>
            </li>
            <li lay-id="list" {eq name='type' value='2'}class="layui-this" {/eq}>
            <a href="{eq name='type' value='2'}javascript:;{else}{:Url('index',['special_type'=>4,'type'=>2])}{/eq}">直播间管理</a>
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
                                <label class="layui-form-label">直播间号</label>
                                <div class="layui-input-block">
                                    <input type="text" name="stream_name" class="layui-input" placeholder="请输入直播名称,关键字,编号">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">所属专题</label>
                                <div class="layui-input-block">
                                    <select name="special_id">
                                        <option value="">全部</option>
                                        {volist name='special_list' id='item'}
                                        <option value="{$item.id}">{$item.title}</option>
                                        {/volist}
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
                        列表[直播标题],[密码],[排序],[自动回复]可进行快速修改,双击或者单击进入编辑模式,失去焦点可进行自动保存
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="play_time">
                        <p>开播：{{d.start_play_time}}</p>
                        <p>停播：{{d.stop_play_time}}</p>
                    </script>
                    <script type="text/html" id="is_pink">
                        {{# if(d.is_pink){ }}
                        <span class="layui-badge layui-bg-green">拼团开启</span>
                        {{# }else{ }}
                        <span class="layui-badge">拼团关闭</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="is_play">
                        {{# if(d.is_play){ }}
                        <span class="layui-badge layui-bg-green">开播中</span>
                        {{# }else{ }}
                        <span class="layui-badge">未直播</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="is_recording">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_recording' lay-text='是|否'  {{ d.is_recording == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;width: 80px;height: 40px;" lay-event='open_image' src="{{d.live_image}}">
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a href="javascript:;" lay-event='update'>
                                    <i class="fa fa-paste"></i> 编辑直播间
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" lay-event='live_user'>
                                    <i class="fa fa-user-circle"></i> 直播间用户管理
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" lay-event='live_goods' >
                                    <i class="fa fa-bullhorn"></i> 直播推荐
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" lay-event='live_remind' >
                                    <i class="fa fa-bell"></i> 直播提醒
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" lay-event='guest_list'>
                                    <i class="fa fa-list-alt"></i> 嘉宾设置
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" lay-event='comment_list'>
                                    <i class="fa fa-commenting-o"></i> 评论查看
                                </a>
                            </li>
                        </ul>
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="live_remind">
    <div style="padding: 20px; background-color: #F2F2F2;">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-body">
                        内容
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
    layList.date({elem:'#start_time',theme:'#393D49',type:'datetime'});
    layList.date({elem:'#end_time',theme:'#393D49',type:'datetime'});
    //加载列表
    layList.tableList({
        o:'List',
        done:function () {

        }
    },"{:Url('get_live_list')}",function (){
        return [
            {field: 'stream_name', title: '直播间号'},
            {field: 'live_title', title: '直播标题',edit:'live_title'},
            {field: 'live_image', title: '封面图',templet:'#image',width:"13%"},
            {field: 'play_time', title: '每日开停时间',templet:'#play_time',width:'20%'},
            {field: 'studio_pwd', title: '直播间密码',edit:'studio_pwd'},
            {field: 'auto_phrase', title: '开播自动回复',edit:'auto_phrase'},
            {field: 'online_user_num', title: '在线人数'},
            {field: 'online_num', title: '虚拟在线人数',edit:'online_num'},
            {field: 'is_play', title: '直播状态',templet:'#is_play'},
            {field: 'is_recording', title: '是否自动录制',templet:'#is_recording'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',width:"3%"},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'},
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
                a: 'set_live_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
        //直播提醒
        live_remind: function (data) {
            if (data.buy_user_num == 0) {
                var content = "暂无直播购买用户";
            } else {
                var content = "【" + data.live_title + "】" + "于" + "【" + data.start_play_time + "】" + "开始直播，" + "共有" + "【" +data.buy_user_num + "位用户】购买直播课程，快去通知吧！"
            }

            layList.layer.open({
                type: 1
                ,scrollbar: true
                ,area: ['400px', '200px']
                ,content: '<div style="padding: 20px 100px;">'+ content +'</div>'
                ,btn: '发送提醒'
                ,btnAlign: 'c' //按钮居中
                ,shade: 0 //不显示遮罩
                ,yes: function(){
                    layList.baseGet(layList.U({a:'send_remind',q:{id:data.special_id}}),function(res){
                        if (res.code == 200) {
                            layer.closeAll();
                            layer.msg("提醒成功", {
                                icon: 1,
                                time: 2000
                            });
                        }else{
                            layer.closeAll();
                            layer.msg("提醒失败", {
                                icon: 2,
                                time: 2000
                            });
                        }
                    }, function (res) {
                        layer.msg(res.msg, {
                            icon: 2,
                            time: 2000
                        });
                    })
                    //
                }
                ,cancel:function () {
                    $('.live_remind').hide();
                }
            });
        }
    }
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('is_recording',function (odj,value) {
        action.set_value('is_recording',value,odj.elem.checked ? 1 :0);
    });
    //快速编辑
    layList.edit(function (obj) {
        action.set_value(obj.field,obj.data.id,obj.value);
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
                $eb.openImage(data.live_image);
                break;
            case 'update':
                $eb.createModalFrame(data.live_title+'--编辑',layList.U({a:'update_live',q:{id:data.id}}));
                break;
            case 'live_user':
                $eb.createModalFrame(data.live_title+'--用户管理',layList.U({a:'live_user',q:{id:data.id}}),{w:1200});
                break;
            case 'comment_list':
                $eb.createModalFrame(data.live_title+'--用户评论',layList.U({a:'comment_list',q:{special_id:data.special_id}}),{w:1200});
                break;
            case 'guest_list':
                $eb.createModalFrame(data.live_title+'--嘉宾设置',layList.U({a:'guest_list',q:{live_id:data.id}}),{w:1200});
                break;
            case 'live_remind':
                action.live_remind(data);
                break;
            case 'live_goods' :
                $eb.createModalFrame(data.live_title+'--推荐课程',layList.U({a:'live_goods',q:{live_id:data.id}}),{w:1200});
                break;

        }
    })
</script>
{/block}

