{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">素材列表</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">是否显示</label>
                                        <div class="layui-input-inline">
                                            <select name="is_show">
                                                <option value="">是否显示</option>
                                                <option value="1">显示</option>
                                                <option value="0">不显示</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">素材名称</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="title" class="layui-input" placeholder="请输入素材名称">
                                            <input type="hidden" name="coures_id" value="{$coures_id}">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                <i class="layui-icon">&#xe615;</i>搜索
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="action.open_add('{:Url('add_source',['special_type' =>$special_type])}','添加{$special_title}素材')">
                                    <i class="layui-icon">&#xe608;</i>添加{$special_title}素材
                                </button>
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()">
                                    <i class="layui-icon">&#xe669;</i>刷新
                                </button>
                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="image">
                                <img style="cursor: pointer;width: 80px;height: 40px;" lay-event='open_image' src="{{d.image}}">
                            </script>
                            <script type="text/html" id="is_show">
                                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                            </script>
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
                                    <i class="layui-icon">&#xe625;</i>操作
                                </button>
                                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                                    <li>
                                        <a href="javascript:void(0)" onclick="action.open_add('{:Url('add_source')}?id={{d.id}}&special_type={$special_type}','编辑')" >
                                            <i class="fa fa-paste"></i> 编辑
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="$eb.createModalFrame('编辑内容','{:Url('update_content')}?id={{d.id}}&field=content&special_type={$special_type}',{h:600,w:800})">
                                            <i class="fa fa-paste"></i> 内容
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="$eb.createModalFrame('编辑简介','{:Url('update_content')}?id={{d.id}}&field=detail&special_type={$special_type}',{h:600,w:800})">
                                            <i class="fa fa-paste"></i> 简介
                                        </a>
                                    </li>

                                    <li>
                                        <a lay-event='delete' href="javascript:void(0)">
                                            <i class="fa fa-warning"></i> 删除
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
    layList.tableList('List',"{:Url('source_list')}?special_type={$special_type}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'10%',align: 'center'},
            {field: 'title', title: '素材名称',edit:'title',align: 'center'},
            {field: 'image', title: '素材封面',templet:'#image',height:100,align: 'center'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',width:'7%',align: 'center'},
            {field: 'is_show', title: '是否显示',templet:'#is_show',width:'10%',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'13%'},
        ];
    });
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
    };
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
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('is_show',function (odj,value) {
        var is_show_value = 0
        if(odj.elem.checked==true){
            var is_show_value = 1
        }
        action.set_value('is_show',value,is_show_value,'task');
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'title':
                action.set_value('title',id,value,'task');
                break;
            case 'sort':
                action.set_value('sort',id,value,'task');
                break;
           /* case 'play_count':
                action.set_value('play_count',id,value);
                break;*/
        }
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delete':
                var url=layList.U({a:'delete',q:{id:data.id, model_type:'task'}});
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
            case 'turnTo':
                layer.confirm('转至其他专题', {
                    btn: ['音频','视频','直播'], //按钮
                    btn1:function(){
                        var url=layList.U({a:'turnTo',q:{id:data.id, model_type:'task',type:2}});
                        $eb.axios.get(url).then(function(res){
                            if(res.status == 200 && res.data.code == 200) {
                                layer.msg(res.data.msg, {icon: 1});
                                location.reload();
                            }else
                                return Promise.reject(res.data.msg || '转换失败')
                        }).catch(function(err){
                            $eb.$swal('error',err);
                        });
                    },
                    btn2:function(){
                        var url=layList.U({a:'turnTo',q:{id:data.id, model_type:'task',type:3}});
                        $eb.axios.get(url).then(function(res){
                            if(res.status == 200 && res.data.code == 200) {
                                layer.msg(res.data.msg, {icon: 1});
                                location.reload();
                            }else
                                return Promise.reject(res.data.msg || '转换失败')
                        }).catch(function(err){
                            $eb.$swal('error',err);
                        });
                    },
                    btn3:function(){
                        var url=layList.U({a:'turnTo',q:{id:data.id, model_type:'task',type:4}});
                        $eb.axios.get(url).then(function(res){
                            if(res.status == 200 && res.data.code == 200) {
                                layer.msg(res.data.msg, {icon: 1});
                                location.reload();
                            }else
                                return Promise.reject(res.data.msg || '转换失败')
                        }).catch(function(err){
                            $eb.$swal('error',err);
                        });
                    }
                });
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })
</script>
{/block}
