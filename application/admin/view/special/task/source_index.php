{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">
                    <div style="font-weight: bold;">素材列表</div>
                </div>
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
                                        <label class="layui-form-label">素材分类</label>
                                        <div class="layui-input-inline">
                                            <select name="pid" lay-search="">
                                                <option value="">全部</option>
                                                {volist name='category' id='vo'}
                                                <option value="{$vo.id}">{$vo.html}{$vo.title}</option>
                                                {/volist}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">素材名称</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="title" class="layui-input" placeholder="请输入素材名称">
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
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="add" onclick="action.open_add('{:Url('admin/special.special_type/addSources')}','添加素材')">
                                    <i class="layui-icon">&#xe608;</i>添加素材
                                </button>
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                                    <i class="layui-icon">&#xe669;</i>刷新
                                </button>
                            </div>
                            <table id="List" lay-filter="List"></table>
                            <script type="text/html" id="image">
                                <img style="cursor: pointer;" height="50" lay-event='open_image' src="{{d.image}}">
                            </script>
                            <script type="text/html" id="recommend">
                                <div class="layui-btn-container">
                                {{#  layui.each(d.recommend, function(index, item){ }}
                                <button type="button" class="layui-btn  layui-btn-normal layui-btn-xs" data-type="recommend" data-id="{{index}}" data-pid="{{d.id}}">{{item}}</button>
                                <!-- <span class="layui-badge layui-bg-blue recom-item" data-id="{{index}}" data-pid="{{d.id}}" style="margin-bottom: 5px;">{{item}}</span> -->
                                {{#  }); }}
                                </div>
                            </script>
                            <script type="text/html" id="is_pay_status_c">
                                {{# if(d.is_pay_status>0){ }}
                                <a onclick="$eb.createModalFrame('设置收费专题','{:Url('is_pay_status_c')}?id='+{{d.id}},{w:800})" class="layui-btn layui-btn-normal layui-btn-xs">已设置收费</a>
                                {{# }else if(d.use>0){ }}
                                <span class="layui-badge">未设置收费</span>
                                {{# }else{ }}
                                <span class="layui-badge">未使用</span>
                                {{# } }}
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
                                        <a href="javascript:;" onclick="action.open_add('{:Url('admin/special.special_type/addSources')}?id={{d.id}}','编辑')" >
                                            <i class="layui-icon">&#xe642;</i> 编辑
                                        </a>
                                    </li>
                                    {{# if(d.is_pay_status==0){ }}
                                    <li>
                                        <a href="javascript:void(0)" onclick="$eb.createModalFrame('{{d.title}}-推荐管理','{:Url('sourceRecommend')}?source_id={{d.id}}',{h:300,w:400})">
                                            <i class="fa fa-check-circle"></i> 推荐至首页
                                        </a>
                                    </li>
                                    {{# } }}
                                    <li>
                                        <a lay-event='delete' href="javascript:;">
                                            <i class="layui-icon">&#xe640;</i> 删除
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
        o: 'List',
        done: function () {
            $('.layui-btn').on('mouseover', function (event) {
                var target = event.target;
                var type = target.dataset.type;
                if ('recommend' === type) {
                    layer.tips('点击即可取消此推荐', target, {
                        tips: [1, '#0093dd']
                    });
                }
            });

            $('.layui-btn').on('mouseout', function (event) {
                var target = event.target;
                var type = target.dataset.type;
                if ('recommend' === type) {
                    layer.closeAll();
                }
            });

            $('.layui-btn').on('click', function (event) {
                var target = event.target;
                var type = target.dataset.type;
                if ('recommend' === type) {
                    var id = target.dataset.id;
                    var pid = target.dataset.pid;
                    var url = layList.U({ a: 'cancel_recommendation', q: { id: id, special_id: pid } });
                    $eb.$swal(
                        'delete',
                        function () {
                            $eb.axios
                                .get(url)
                                .then(function (res) {
                                    if (res.data.code == 200) {
                                        $eb.$swal('success', res.data.msg);
                                        layList.reload();
                                    } else {
                                        return Promise.reject(res.data.msg || '取消失败');
                                    }
                                })
                                .catch(function (err) {
                                    $eb.$swal('error', err);
                                });
                        },
                        {
                            title: '确定取消此推荐？',
                            text: '取消后无法撤销，请谨慎操作！',
                            confirm: '确定取消'
                        }
                    );
                }
            });
        }
    },"{:Url('get_source_list')}",function (){
        return [
            {field: 'id', title: '编号', align: 'center',width:60},
            {field: 'title', title: '素材名称',align: 'center'},
            {field: 'types', title: '素材类型',align: 'center'},
            {field: 'image', title: '素材封面',templet:'#image',align: 'center',style:'height:auto'},
            {field: 'use', title: '应用次数',align: 'center'},
            {field: 'is_pay_status_c', title: '是否收费',align: 'center',templet:'#is_pay_status_c'},
            {field: 'recommend', title: '推荐',templet:'#recommend',align: 'center'},
            {field: 'is_show', title: '状态',templet:'#is_show',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act'},
        ];
    });
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    });
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
                ,end:function () {
                    location.reload();
                }
            });
        }
    };

    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('is_show',function (odj,value) {
        var is_show_value = 0
        if(odj.elem.checked==true){
            is_show_value = 1
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
                if(value < 0) return layList.msg('排序不能小于0');
                action.set_value('sort',id,value,'task');
                break;
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
                            if(data.videoId){
                                layList.basePost(layList.U({a: 'video_upload_address_voucher'}),
                                    {
                                        FileName:'',type:4,image:'',videoId:data.videoId
                                    }, function (res) {
                                    var url=res.msg;
                                        $.ajax({
                                            url:url,
                                            data:{},
                                            type:"GET",
                                            dataType:'json',
                                            success:function (data) {
                                                if(data.RequestId){
                                                    $eb.$swal('success','删除成功！');
                                                    obj.del();
                                                }
                                            },
                                            error:function (err) {
                                                $eb.$swal('error',err['responseJSON'].Message);
                                                obj.del();
                                            }
                                        });
                                });
                            }else{
                                $eb.$swal('success','删除成功！');
                                obj.del();
                            }
                        }else{
                            return Promise.reject(res.data.msg || '删除失败');
                        }
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                });
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })
</script>
{/block}
