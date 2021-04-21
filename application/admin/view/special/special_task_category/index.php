{extend name="public/container"}
{block name="head"}
<style>
    .single-line {
        padding: 0;
        border: none;
        background: none;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">
                    <div style="font-weight: bold;">素材分类</div>
                </div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">素材分类</label>
                                        <div class="layui-input-inline">
                                            <select name="pid" lay-search="">
                                                <option value="0">全部</option>
                                                {volist name='category' id='vo'}
                                                <option value="{$vo.id}" {eq name="pid" value="$vo.id"}selected="selected"{/eq}>{$vo.html}{$vo.title}</option>
                                                {/volist}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">素材分类名称</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="cate_name" class="layui-input" placeholder="请输入素材分类名称">
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
                                {if condition="$pid neq 0"}
                                <a class="layui-btn layui-btn-normal layui-btn-sm" href="{:Url('admin/special.special_task_category/index')}">
                                    <i class="layui-icon">&#xe608;</i>返回首页
                                </a>
                                {/if}
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="add">
                                    <i class="layui-icon">&#xe608;</i>添加素材分类
                                </button>
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh">
                                    <i class="layui-icon">&#xe669;</i>刷新
                                </button>
                            </div>
                            <table id="List" lay-filter="List"></table>
                            <script type="text/html" id="is_show">
                                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                            </script>
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">
                                    <i class="layui-icon">&#xe642;</i>编辑
                                </button>
                                <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">
                                    <i class="layui-icon">&#xe640;</i>删除
                                </button>
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
    layui.config({
        base: '{__PLUG_PATH}'
    }).use(['treeTable'], function () {
        var $ = layui.jquery;
        var treeTable = layui.treeTable;
        var insTb = treeTable.render({
            elem: '#List',
            tree: {
                iconIndex: 1,
                isPidData: true,
                idName: 'id',
                pidName: 'pid',
                getIcon: function (d) {
                    return '';
                }
            },
            cols: [[
                {field: 'id', title: '编号', align: 'center', width: 60},
                {field: 'title', title: '分类名称'},
                {field: 'sum', title: '素材数量', align: 'center'},
                {field: 'right', title: '操作', align:'center', toolbar:'#act'},
            ]],
            reqData: function(data, callback) {
                $.get('{:Url('get_category_list')}', function (res) {
                    if (res.code == 200) {
                        callback(res.data)
                    } else {
                        callback(res.msg)
                    }
                }, 'json');
            }
        });

        treeTable.on('tool(List)', function(obj) {
            var data = obj.data;
            var layEvent = obj.event;
            if (layEvent === 'edit') {
                $eb.createModalFrame('编辑', '{:Url('create')}?id=' + data.id + '&level=1', { h: 500, w: 800 });
            } else if (layEvent === 'del') {
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
            }
        });
        $('.layui-btn').on('click', function(event) {
            var target = event.target;
            var type = target.dataset.type;
            if (type === 'add') {
                $eb.createModalFrame('添加素材分类', '{:Url('create')}?pid={$pid}',{ h: 500 ,w: 800 });
            } else if (type === 'refresh') {
                insTb.refresh();
            }
        });
        //查询
        layList.search('search',function(where){
            insTb = treeTable.render({
                elem: '#List',
                tree: {
                    iconIndex: 1,
                    isPidData: true,
                    idName: 'id',
                    pidName: 'pid',
                    getIcon: function (d) {
                        return '';
                    }
                },
                cols: [[
                    {field: 'id', title: '编号', align: 'center',  width:60},
                    {field: 'title', title: '分类名称'},
                    {field: 'sum', title: '素材数量', align: 'center'},
                    {field: 'right', title: '操作', align:'center', toolbar:'#act'},
                ]],
                reqData: function(data, callback) {
                    $.get('{:Url('get_category_list')}', where, function (res) {
                        if (res.code == 200) {
                            callback(res.data)
                        } else {
                            callback(res.msg)
                        }
                    }, 'json');
                }
            });
        });
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
    
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'sort':
                if(value < 0) return layList.msg('排序不能小于0');
                action.set_value('sort',id,value);
                break;
        }
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delstor':
                var url=layList.U({a:'delete',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            location.reload();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                })
                break;
            case 'open_image':
                $eb.openImage(data.pic);
                break;
        }
    })
    //是否显示快捷按钮操作
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
</script>
{/block}
