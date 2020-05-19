{extend name="public/container"}
{block name="head_top"}

{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">会员类型</label>
                                <div class="layui-input-block">
                                    <select name="vip_id" lay-verify="vip_id">
                                        <option value="">全部</option>
                                        {volist name='system_vip_list' id='val'}
                                        <option value="{$key}">{$val}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">用户名</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" lay-verify="title" class="layui-input" placeholder="请输入会员名称">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-block">
                                    <select name="status" lay-verify="status">
                                        <option value="">全部</option>
                                        <option value="1">正常</option>
                                        <option value="0">锁定</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">时间范围</label>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="start_time" value="" placeholder="开始时间" id="start_time" class="layui-input">
                                </div>
                                <div class="layui-form-mid">-</div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="end_time" value="" placeholder="结束时间" id="end_time" class="layui-input">
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
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">会员列表</div>
                <div class="layui-card-body">
                    <script type="text/html" id="too">
                        <div class="layui-btn-container">

                        </div>
                    </script>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="status">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='status' lay-text='正常|锁定'  {{ d.status == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-xs" lay-event="delete"><i class="layui-icon layui-icon-edit"></i>删除</button>
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

    layList.form.render();
    layList.date({elem:'#start_time',theme:'#393D49',type:'datetime'});
    layList.date({elem:'#end_time',theme:'#393D49',type:'datetime'});

    layList.tableList({o:'List',t:'too'},layList.U({a:'getUserVipList'}),function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'5%'},
            {field: 'title', title: '购买会员名'},
            {field: 'nickname', title: '会员用户名'},
            {field: 'vip_time', title: '成为会员时间'},
            //{field: 'valid_date', title: '有效期'},
            //{field: 'discount', title: '享受商品折扣(%)'},
            {field: 'status', title: '会员状态',templet:'#status'},
            //{field: 'is_forever', title: '是否永久'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'},
        ];
    });
    layList.search('search',function(where){
        if(where.start_time!=''){
            if(where.end_time==''){
                return layList.msg('请选择结束时间');
            }
        }
        if(where.end_time!=''){
            if(where.start_time==''){
                return layList.msg('请选择开始时间');
            }
        }
        layList.reload(where,true);
    });
    layList.switch('status',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_status',p:{status:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_status',p:{status:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    layList.tool(function (layEvent,data,obj) {
        switch (layEvent){
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
                });
                break;
        }
    });
</script>
{/block}