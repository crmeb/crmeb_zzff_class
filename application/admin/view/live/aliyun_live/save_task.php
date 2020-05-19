{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form" action="">
                        <div class="layui-form-item">
                            <label class="layui-form-label">一级分类：</label>
                            <div class="layui-input-block">
                                <select name="grade_id" lay-filter="grade_id">
                                    <option value="">全部</option>
                                    {volist name='grade_list' id='item'}
                                    <option value="{$item.id}">{$item.name}</option>
                                    {/volist}
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">二级分类：</label>
                            <div class="layui-input-block">
                                <select name="subject_id" lay-filter="subject_id">
                                    <option value="">全部-</option>
                                    <option :value="item.id" v-for="item in subjectList">{{ item.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">选择专题：</label>
                            <div class="layui-input-block">
                                <select name="special_id" lay-filter="special_id">
                                    <option value="">请选择专题</option>
                                    <option :value="item.id" v-for="item in specialList">{{ item.title }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn" lay-submit="search" lay-filter="search">确认选择</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script type="text/javascript">
    var live_id=<?=$live_id?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                grade_id:0,
                subject_id:0,
                subjectList:[],
                specialList:[],
            },
            watch:{
                grade_id:function () {
                    this.getSubjectList();
                },
                subject_id:function () {
                    this.getSpecialList();
                }
            },
            methods:{
                getSubjectList:function () {
                    var that = this;
                    layList.baseGet(layList.U({a:'get_subject_list',q:{grade_id:that.grade_id}}),function (res) {
                        that.$set(that,'subjectList',res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                getSpecialList:function () {
                    var that = this;
                    layList.baseGet(layList.U({a:'get_special_list',q:{subject_id:that.subject_id}}),function (res) {
                        that.$set(that,'specialList',res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                }
            },
            mounted:function () {
                var that = this;
                layList.form.render();
                //查询
                layList.search('search',function(where){
                    if(where.special_id)
                        window.location.href=layList.U({a:'save_task',q:{special_id:where.special_id,live_id:live_id}});
                    else
                        layList.msg('请选择专题');
                });

                layList.select('grade_id',function (obj) {
                    that.grade_id = obj.value;
                });

                layList.select('subject_id',function (obj) {
                    that.subject_id = obj.value;
                });
            }
        })
    })
</script>
{/block}