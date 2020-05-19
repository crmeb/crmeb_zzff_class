{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app" v-cloak="">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">昵称/ID</label>
                                <div class="layui-input-block">
                                    <input type="text" name="nickname" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">时间范围</label>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="start_time" placeholder="开始时间" id="start_time" class="layui-input">
                                </div>
                                <div class="layui-form-mid">-</div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <input type="text" name="end_time" placeholder="结束时间" id="end_time" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索</button>
                                    <button class="layui-btn layui-btn-primary layui-btn-sm export"  lay-submit="export" lay-filter="export">
                                        <i class="fa fa-floppy-o" style="margin-right: 3px;"></i>导出</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 中间详细信息-->
        <div :class="item.col!=undefined ? 'layui-col-sm'+item.col+' '+'layui-col-md'+item.col:'layui-col-sm6 layui-col-md3'" v-for="item in badge" v-if="item.count > 0">
            <div class="layui-card">
                <div class="layui-card-header">
                    {{item.name}}
                    <span class="layui-badge layuiadmin-badge" :class="item.background_color">{{item.field}}</span>
                </div>
                <div class="layui-card-body">
                    <p class="layuiadmin-big-font">{{item.count}}</p>
                    <p v-show="item.content!=undefined">
                        {{item.content}}
                        <span class="layuiadmin-span-color">{{item.sum}}<i :class="item.class"></i></span>
                    </p>
                </div>
            </div>
        </div>
        <!--enb-->
    </div>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">合伙人资金</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="userList" lay-filter="userList"></table>
                    <script type="text/html" id="number">
                        {{#  if(d.pm ==0){ }}
                        <span style="color:#FF5722">-{{d.number}}</span>
                        {{# }else{ }}
                        <span style="color:#009688">{{d.number}}</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="spread">
                        {{#  if(d.link_id !=0){ }}
                        {{d.spread_nickname}}/{{d.spread_uid}}/{{d.spread_phone}}
                        {{# }else{ }}
                        暂无
                        {{# } }}
                    </script>
                    <script type="text/html" id="status">
                        {{#  if(d.status == 0){ }}
                        <span style="color:#FF5722">待确定</span>
                        {{# }else{ }}
                        <span style="color:#009688">有效</span>
                        {{# } }}
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>

    layList.tableList('userList',"{:Url('billlist')}",function () {
        return [
            {field: 'uid', title: '会员ID', sort: true,event:'uid'},
            {field: 'nickname', title: '昵称' },
            {field: 'name', title: '真实姓名' },
            {field: 'number', title: '金额/积分',sort:true,templet:'#number'},
            {field: 'title', title: '类型'},
            {field: 'status', title: '状态',templet:'#status'},
            {field: 'mark', title: '备注'},
            {field: 'spread', title: '关联人信息',templet:'#spread'},
            {field: 'add_time', title: '创建时间'},
        ];
    });
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                where:{},
            },
            methods:{
                get_badge:function () {
                    var that=this;
                    layList.basePost(layList.Url({a:'getBadge'}),this.where,function (rem) {
                        that.badge=rem.data;
                    });
                }
            },
            mounted:function () {
                var that=this;
                this.get_badge();
                layList.form.render();
                layList.date({elem:'#start_time',theme:'#393D49',type:'datetime'});
                layList.date({elem:'#end_time',theme:'#393D49',type:'datetime'});
                layList.search('search',function(where){
                    that.where=where;
                    if(where.start_time!='' && where.end_time=='') return layList.msg('请选择结束时间');
                    if(where.end_time!='' && where.start_time=='') return layList.msg('请选择开始时间');
                    layList.reload(where);
                    that.get_badge();
                });
                layList.search('export',function(where){
                    location.href=layList.U({a:'save_bell_export',q:where});
                });
            }
        })
    })
</script>
{/block}