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
<div class="layui-fluid" style="background: #fff;padding: 25px;">
    <div class="layui-row layui-col-space15" id="app">
        <div class="layui-col-md12">
            <div class="layui-card" v-cloak="">
                <div class="layui-card-header">添加域名</div>
                <div class="layui-card-body" style="padding: 10px 10px;">
                    <form action="" class="layui-form">
                        <div class="layui-form-item m-t-5" v-cloak="">
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">加速域名</label>
                            <div class="layui-input-block">
                                <input type="text" name="domain_name" style="width: 90%" v-model="formData.domain_name" autocomplete="off" placeholder="添加推流或播流域名,如:live.test.com;暂不支持添加泛域名,如“*.test.com”" class="layui-input">
                            </div>
                        </div>
                         <div class="layui-form-item submit" style="margin-top: 30px;">
                                <label class="layui-form-label">直播中心</label>
                                <div class="layui-input-block">
                                    <select name="region" v-model="formData.region" lay-search="" lay-filter="region">
                                            <option v-for="(item,index) in endpoint"  :value="item">{{index}}</option>
                                    </select>
                                </div>
                         </div>
                        <div class="layui-form-item" style="margin-top: 30px;">
                            <label class="layui-form-label">业务类型</label>
                            <div class="layui-input-block">
                                <input type="radio" name="live_domain_type" value="liveEdge" title="推流域名" v-model="formData.live_domain_type" lay-filter="live_domain_type">
                                <input type="radio" name="live_domain_type" value="liveVideo" title="播流域名" v-model="formData.live_domain_type" lay-filter="live_domain_type" >
                            </div>
                        </div>
                            <div class="layui-form-item" style="margin-top: 30px;">
                            <label class="layui-form-label">CDN 加速区域</label>
                            <div class="layui-input-block">
                                <input type="radio" name="scope" value="domestic" title="中国大陆" v-model="formData.scope" lay-filter="scope" >
                                <input type="radio" name="scope" value="global" title="全球加速" v-model="formData.scope" lay-filter="scope">
                                <input type="radio" name="scope" value="overseas" title="海外及港澳台加速" v-model="formData.scope" lay-filter="scope">
                            </div>
                        </div>

                        <div class="layui-form-item submit">
                            <div class="layui-input-block" style="margin-left: 40%;margin-top: 50px;">
                                <button class="layui-btn layui-btn-normal" type="button" @click="save">添加域名</button>
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
                    domain_name:'',
                    region:'cn-beijing',
                    live_domain_type:'liveEdge',
                    scope:'domestic',
                },
                endpoint:endpoint
            },
            methods:{
                save:function () {
                    var that=this;
                    if(that.formData.domain_name=='') return layList.msg('请输入加速域名');
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
                layList.form.on('radio(live_domain_type)',function (data) {
                    that.formData.live_domain_type=data.value;
                });
                layList.form.on('radio(scope)',function (data) {
                    that.formData.scope=data.value;
                });
                layList.select('region', function (obj) {
                    that.formData.region = obj.value;
                });
            }
        })
    })
</script>
{/block}