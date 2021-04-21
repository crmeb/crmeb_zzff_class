{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">专题列表</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;width: 80px;height: 40px;" lay-event='open_image' src="{{d.image}}">
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
    layList.tableList('List',"{:Url('is_pay_source_list')}?source_id={$source_id}",function (){
        return [
            {field: 'id', title: '编号',align: 'center'},
            {field: 'title', title: '专题标题',align: 'center'},
            {field: 'image', title: '封面',templet:'#image',align: 'center'}
        ];
    });
</script>
{/block}
