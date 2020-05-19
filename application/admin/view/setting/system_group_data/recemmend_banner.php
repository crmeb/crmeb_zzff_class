{extend name="public/container"}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <div class="layui-tab layui-tab-brief" lay-filter="tab">
        <ul class="layui-tab-title">
            <li lay-id="list" class="layui-this">
                <a href="javascript:;">列表</a>
            </li>
            <li lay-id="list">
                <a href="{:Url('create_recemmend_banner',['type'=>2,'id'=>$id])}">添加</a>
            </li>
        </ul>
    </div>
    <div class="layui-row layui-col-space15"  id="app">
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="pic">
                        <img style="cursor: pointer" lay-event='open_image' src="{{d.pic}}">
                    </script>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        <button class="layui-btn layui-btn-warm layui-btn-xs" onclick="location.href = '{:Url('create_recemmend_banner',['id'=>$id])}?banner_id={{d.id}}'">
                            <i class="fa fa-paste"></i>  编辑
                        </button>
                        <button class="layui-btn layui-btn-xs layui-btn-danger" lay-event='delete'>
                            <i class="fa fa-trash"></i> 删除
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
    layList.tableList('List',"{:Url('recemmend_banner_list')}?id={$id}",function (){
        return [
            {field: 'sort', title: '排序',edit:'sort',width:'7%'},
            {field: 'url', title: '跳转链接',edit:'url'},
            {field: 'pic', title: '封面',templet:'#pic',width:'15%'},
            {field: 'is_show', title: '是否显示',templet:'#is_show',width:'13%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'13%'},
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value_banner',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_show_banner',p:{is_show:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_show_banner',p:{is_show:0,id:value}}),function (res) {
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
    //监听并执行排序
    layList.sort(['sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delete':
                var url=layList.U({a:'delete_banner',q:{id:data.id}});
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
                $eb.openImage(data.pic);
                break;
        }
    })
</script>
{/block}
