{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-item .special-label{
        width: 50px;
        float: left;
        height: 30px;
        line-height: 38px;
        margin-left: 10px;
        margin-top: 5px;
        border-radius: 5px;
        background-color: #0092DC;
        text-align: center;
    }
    .layui-form-item .special-label i{
        display: inline-block;
        width: 18px;
        height: 18px;
        font-size: 18px;
        color: #fff;
    }
    .layui-form-item .label-box{
        border: 1px solid;
        border-radius: 10px;
        position: relative;
        padding: 10px;
        height: 30px;
        color: #fff;
        background-color: #393D49;
        text-align: center;
        cursor: pointer;
        display: inline-block;
        line-height: 10px;
    }
    .layui-form-item .label-box p{
        line-height: inherit;
    }
    .layui-form-mid{
        margin-left: 18px;
    }
    .m-t-5{
        margin-top:5px;
    }
    .edui-default .edui-for-image .edui-icon{
        background-position: -380px 0px;
    }
</style>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}plug/ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/aliyun-oss-sdk-4.4.4.min.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/lib/plupload-2.1.2/js/plupload.full.min.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/OssUpload.js"></script>
{/block}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <div class="layui-row layui-col-space15"  id="app">
        <form action="" class="layui-form">
            <div class="layui-col-md12">
                <div class="layui-card" v-cloak="">
                    <div class="layui-card-header">基本信息</div>
                    <div class="layui-card-body" style="padding: 10px 150px;">
                        <!--<div class="layui-form-item">
                            <label class="layui-form-label">会员类型选择</label>
                            <div class="layui-input-block">
                                <select name="subject_id" v-model="formData.subject_id" lay-search="" lay-filter="subject_id">
                                    <option value="0">请选分类</option>
                                    <option :value="item.id" v-for="item in subject_list">{{item.name}}</option>
                                </select>
                            </div>
                        </div>-->
                        <div class="layui-form-item">
                            <label class="layui-form-label" >批次名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" v-model="formData.title" autocomplete="off" placeholder="请输入批次名称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">制卡数量</label>
                            <div class="layui-input-block">
                                <input type="number" min="1" name="total_num" v-model="formData.total_num" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">体验天数</label>
                            <div class="layui-input-block">
                                <input type="number" min="1" name="use_day" v-model="formData.use_day" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">是否激活</label>
                            <div class="layui-input-block">
                                <input type="radio" name="status" lay-filter="status" v-model="formData.status" value="0" title="冻结">
                                <input type="radio" name="status" lay-filter="status" v-model="formData.status" value="1" title="激活">
                            </div>
                        </div>
                        <div class="layui-form-item m-t-5">
                            <label class="layui-form-label">备注</label>
                            <div class="layui-input-block">
                                <textarea placeholder="请输入备注" v-model="formData.remark" class="layui-textarea"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-form-item submit" style="margin-bottom: 10px">
                        <div class="layui-input-block">
                            <button class="layui-btn layui-btn-normal" type="button" @click="save">{$id ? '确认修改':'立即提交'}</button>
                            <button class="layui-btn layui-btn-primary clone" type="button" @click="clone_form">取消</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var id= {$id},
        batch=<?=isset($batch) ? $batch : "{}"?>;
        batch_time='<?=date('Y-m-d H:i:s',time())?>';//时间初始化使用
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                formData:{
                    title:batch.title || '',
                    total_num:batch.total_num || 0,
                    use_day:batch.use_day || 0,
                    status:batch.status || 0,
                    remark:batch.remark || ""
                },
            },
            methods:{
                save:function () {
                    var that=this;
                    if(!that.formData.title) return layList.msg('请输入批次标题');
                    if(!that.formData.use_day) return layList.msg('请输入体验天数');
                    if(!that.formData.total_num) return layList.msg('请输入制卡数量');
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'save_batch',q:{id:id}}),that.formData,function (res) {
                        layList.loadClear();
                        if(parseInt(id) == 0) {
                            layList.layer.confirm('添加成功,您要继续添加素材吗?', {
                                btn: ['继续添加', '取消'] //按钮
                            }, function () {
                                window.location.reload();
                            }, function () {
                                parent.layer.closeAll();
                            });
                        }else{
                            layList.msg('修改成功',function () {
                               parent.layer.closeAll();
                            })
                        }
                    },function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                },
                clone_form:function () {
                    var that = this;
                    //有关闭扩展事件直接写在这里
                    if(parseInt(id) == 0){
                        parent.layer.closeAll();
                    }
                    parent.location.href = layList.U({a:'batch_index',p:{}});
                }
            },
            mounted:function () {
                var that=this;
                this.$nextTick(function () {
                    layList.form.render();
                });
                //操作dom时触发
                layList.form.on('radio(status)', function(data){
                    that.formData.pay_type = parseInt(data.value);
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });

                that.$nextTick(function () {
                    //扩展操作
                })
            }
        })
    })

</script>
{/block}