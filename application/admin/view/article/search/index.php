{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">关键词添加</div>
                <div class="layui-card-body">
                    <div class="layui-form-item">
                        <label class="layui-form-label">关键词</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" autocomplete="off" v-model="name" style="width: 30%;display:inline-block;margin-right: 10px;" class="layui-input" placeholder="请输入关键词名称">
                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal"  @click="add">确认添加</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">关键词列表</div>
                <div class="layui-card-body">
                    <button class="layui-btn layui-btn-primary" v-for="(item,index) in searchList" style="position: relative;margin-left: 10px;margin-top: 10px">
                        {{item.name}} <i class="layui-icon layui-icon-close lay-close" @click="del(item,index)"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var list=<?=count($list) ? json_encode($list) : "[]"?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                searchList:list,
                name:'',
            },
            methods:{
                add:function () {
                    var that=this;
                    layList.baseGet(layList.U({a:'save',q:{name:that.name}}),function (res) {
                        that.searchList.push(res.data);
                        that.$set(that,'searchList',that.searchList);
                        layList.msg(res.msg);
                        that.name='';
                    });
                },
                del:function (item,index) {
                    var that=this;
                    layList.baseGet(layList.U({a:'del_search',q:{id:item.id}}),function (res) {
                        that.searchList.splice(index,1);
                        that.$set(that,'searchList',that.searchList);
                        layList.msg(res.msg);
                    });
                }
            },
            mounted:function () {

            }
        })
    })
</script>
{/block}