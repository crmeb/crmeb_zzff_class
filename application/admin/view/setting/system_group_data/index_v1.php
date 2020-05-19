{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">是否展示</label>
                                <div class="layui-input-block">
                                    <select name="status">
                                        <option value="">全部</option>
                                        <option value="1">显示</option>
                                        <option value="0">不显示</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search"
                                            lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索
                                    </button>
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
                <div class="layui-card-header">分类列表</div>
                <div class="layui-card-body">
                    <div class="alert alert-info" role="alert">
                        注:分类名称和排序可进行快速编辑;
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-sm"
                                onclick="$eb.createModalFrame(this.innerText,'{:Url('create_v1')}',{w:800})">添加配置
                        </button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="pic">
                        <img style="cursor: pointer" lay-event='open_image' src="{{d.pic}}">
                    </script>
                    <script type="text/html" id="status">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='status'
                               lay-text='显示|隐藏' {{ d.status== 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        <button class="layui-btn layui-btn-xs"
                                onclick="$eb.createModalFrame('编辑','{:Url('create_v1')}?id={{d.id}}',{w:800})">
                            <i class="fa fa-paste"></i> 编辑
                        </button>
                        <button class="layui-btn layui-btn-xs" lay-event='delstor'>
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
    layList.tableList('List', "{:Url('get_group_data_list',['gid'=>$gid])}", function () {
        return [
            {field: 'id', title: '编号', sort: true, event: 'id', width: '5%'},
            {field: 'title', title: '标题', edit: 'title'},
            {field: 'info', title: '简介', edit: 'info'},
            {field: 'pic', title: '图标', templet: '#pic'},
            {field: 'sort', title: '排序', sort: true, event: 'sort', edit: 'sort'},
            {field: 'status', title: '是否显示', templet: '#status'},
            {field: 'right', title: '操作', align: 'center', toolbar: '#act', width: '10%'},
        ];
    });
    //自定义方法
    var action = {
        set_group_data: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_group_data',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }

    //查询
    layList.search('search', function (where) {
        layList.reload(where, true);
    });

    layList.switch('status', function (odj, value) {
        action.set_group_data('status', value, odj.elem.checked == true ? 1 : 0);
    });
    //快速编辑
    layList.edit(function (obj) {
        var id = obj.data.id, value = obj.value;
        switch (obj.field) {
            case 'title':
                action.set_group_data('title', id, value);
                break;
            case 'info':
                action.set_group_data('info', id, value);
                break;
            case 'sort':
                action.set_group_data('sort', id, value);
                break;
        }
    });
    //监听并执行排序
    layList.sort(['id', 'sort'], true);
    //点击事件绑定
    layList.tool(function (event, data, obj) {
        switch (event) {
            case 'delstor':
                var url = layList.U({ a: 'delete', q: {id: data.id}});
                $eb.$swal('delete', function () {
                    $eb.axios.get(url).then(function (res) {
                        if (res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success', res.data.msg);
                            obj.del();
                        } else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function (err) {
                        $eb.$swal('error', err);
                    });
                })
                break;
            case 'open_image':
                $eb.openImage(data.pic);
                break;
        }
    })
</script>
{/block}
