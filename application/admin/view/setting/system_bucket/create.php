{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-item .special-label i{display: inline-block;width: 18px;height: 18px;font-size: 18px;color: #fff;}
    .layui-form-item .label-box p{line-height: inherit;}
    .m-t-5{margin-top:5px;}
    #app .layui-barrage-box{margin-bottom: 10px;margin-top: 10px;margin-left: 10px;border: 1px solid #0092DC;border-radius: 5px;cursor: pointer;position: relative;}
    #app .layui-barrage-box.border-color{border-color: #0bb20c;}
    #app .layui-barrage-box .del-text{position: absolute;top: 0;left: 0;background-color: rgba(0,0,0,0.5);color: #ffffff;width: 92%;text-align: center;}
    #app .layui-barrage-box p{padding:5px 5px; }
    #app .layui-empty-text{text-align: center;font-size: 18px;}
    #app .layui-empty-text p{padding: 10px 10px;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">添加存储空间</div>
                <div class="layui-card-body">
                    <form action="" class="layui-form">
                        <div class="layui-form-item m-t-5">
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">存储空间名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" style="width: 50%" v-model="formData.bucket_name" autocomplete="off" placeholder="请输入存储空间名称" class="layui-input">
                            </div>
                        </div>
                         <div class="layui-form-item submit" style="margin-top: 30px;">
                                <label class="layui-form-label">存储空间区域</label>
                                <div class="layui-input-block">
                                    <select name="endpoint" v-model="formData.endpoint" lay-search="" lay-filter="endpoint">
                                            <option v-for="(item,index) in endpoint"  :value="item">{{index}}</option>
                                    </select>
                                </div>
                         </div>
                        <div class="layui-form-item" style="margin-top: 30px;">
                            <label class="layui-form-label">存储类型</label>
                            <div class="layui-input-block">
                                <input type="radio" name="type" value="1" title="标准储存" v-model="formData.type" lay-filter="type" >
                                <input type="radio" name="type" value="2" title="低频访问储存" v-model="formData.type" lay-filter="type">
                                <input type="radio" name="type" value="3" title="归档储存" v-model="formData.type" lay-filter="type">
                            </div>
                        </div>
                            <div class="layui-form-item" style="margin-top: 30px;">
                            <label class="layui-form-label">读写权限</label>
                            <div class="layui-input-block">
                                <input type="radio" name="jurisdiction" value="1" title="私有" v-model="formData.jurisdiction" lay-filter="jurisdiction" >
                                <input type="radio" name="jurisdiction" value="2" title="公共读" v-model="formData.jurisdiction" lay-filter="jurisdiction">
                                <input type="radio" name="jurisdiction" value="3" title="公共读写" v-model="formData.jurisdiction" lay-filter="jurisdiction">
                            </div>
                        </div>

                        <div class="layui-form-item submit">
                            <div class="layui-input-block" style="margin-left: 40%;margin-top: 30px;">
                                <button class="layui-btn layui-btn-normal" type="button" @click="save">添加存储空间</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/OssUpload.js"></script>
{/block}
{block name='script'}
<script>
    var endpoint=<?=isset($endpoint) ? $endpoint : []?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                formData:{
                    bucket_name:'',
                    endpoint:'oss-cn-hangzhou.aliyuncs.com',
                    type:1,
                    jurisdiction:3,
                },
                endpoint:endpoint
            },
            methods:{
                save:function () {
                    var that=this;
                    if(that.formData.bucket_name=='') return layList.msg('请输入存储空间名称');
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'save'}),that.formData,function (res) {
                        layList.loadClear();
                            layList.layer.confirm('添加成功,您要继续添加存储空间吗?', {
                                btn: ['继续添加', '立即提交'] //按钮
                            }, function () {
                                window.location.reload();
                            }, function () {
                                parent.layer.closeAll();
                            });
                    },function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                }
            },
            mounted:function () {
                var that=this;
                layList.form.render();
                layList.form.on('radio(type)',function (data) {
                    that.formData.type=data.value;
                });
                layList.form.on('radio(jurisdiction)',function (data) {
                    that.formData.jurisdiction=data.value;
                });
                layList.select('endpoint', function (obj) {
                    that.formData.endpoint = obj.value;
                });
            }
        })
    })
</script>
{/block}