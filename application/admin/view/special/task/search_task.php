{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
       <!-- <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">素材名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" class="layui-input" placeholder="请输入素材名称">
                                    <input type="hidden" name="coures_id" value="{$coures_id}">
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
        </div>-->
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">{$special_title}素材列表</div>
                <div class="layui-card-body">
                    <!--<div class="alert alert-info" role="alert">
                        注:素材名称和排序可进行快速编辑;
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>-->
                    <div class="layui-btn-container">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i>  刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="toolbarDemo" >
                        <div class="layui-btn-container">
                            <button id="test" class="layui-btn layui-btn-sm" lay-event="getCheckData">获取选中行数据</button>
                        </div>
                    </script>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;width: 80px;" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
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
    var special_id = <?=isset($special_id) ? $special_id : ""?>;
    //实例化form
    layui.table.render();
    var table = layui.table;
    //加载列表
   /* layList.tableList('List',"{:Url('source_list')}?coures_id={$coures_id}",function (){
        return [
            {type: 'checkbox'},
            {field: 'id', title: '编号', sort: true,event:'id'},
            {field: 'title', title: '素材标题',edit:'title'},
            {field: 'image', title: '封面',templet:'#image'},
            {field: 'is_show', title: '是否显示',templet:'#is_show',width:'10%'},
        ];
    });*/
    table.render({
        elem: '#List'
        ,url:"{:Url('source_list')}?special_id={$special_id}&special_type={$special_type}"
        ,toolbar: '#toolbarDemo' //开启头部工具栏，并为其绑定左侧模板
        ,defaultToolbar: ['filter', 'exports', 'print', { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
            title: '提示'
            ,layEvent: 'LAYTABLE_TIPS'
            ,icon: 'layui-icon-tips'
        }]
        ,cols: [[
            {type: 'checkbox'},
            {field: 'id', title: '编号', sort: true,event:'id'},
            {field: 'title', title: '素材标题'},
            {field: 'image', title: '封面',templet:'#image'},
        ]]
        ,page: true
    });

    //头工具栏事件
    table.on('toolbar(List)', function(obj){
        var checkStatus = table.checkStatus(obj.config.id);
        switch(obj.event){
            case 'getCheckData':
                var data = checkStatus.data;
                $("#check_source_tmp",window.parent.document).val(JSON.stringify(data));
                break;
        };
    });

   /* table.on('row(List)',function(obj){
        var checkStatus = table.checkStatus('List');
        var data = obj.data;
        console.log(checkStatus);
       // conId = data.id;
        //标注选中样式
        obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
    });*/
   /* table.on('checkbox(List)', function(obj){
        console.log(obj)
    });*/

    //自定义方法
  /*  var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }*/
    //查询
/*    layList.search('search',function(where){
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
            case 'play_count':
                action.set_value('play_count',id,value);
                break;
        }
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })*/
</script>
{/block}
