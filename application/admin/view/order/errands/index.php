{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" >
        <!--搜索条件-->
        <div class="layui-col-md12" id="app">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <div class="layui-carousel layadmin-carousel layadmin-shortcut" lay-anim="" lay-indicator="inside" lay-arrow="none" style="background:none">
                        <div class="layui-card-body">
                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">订单状态:</label>
                                    <div class="layui-input-block" v-cloak="">
                                        <button class="layui-btn layui-btn-sm" :class="{'layui-btn-primary':where.status!==item.value}" @click="where.status = item.value" type="button" v-for="item in orderStatus">{{item.name}}
                                            <span v-if="item.count!=undefined" :class="item.class!=undefined ? 'layui-badge': 'layui-badge layui-bg-gray' ">{{item.count}}</span></button>
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">订单类型:</label>
                                    <div class="layui-input-block" v-cloak="">
                                        <button class="layui-btn layui-btn-sm" :class="{'layui-btn-primary':where.order_type!==item.value}" @click="where.order_type = item.value" type="button" v-for="item in orderType">{{item.name}}
                                            <span v-if="item.count!=undefined" :class="item.class!=undefined ? 'layui-badge': 'layui-badge layui-bg-gray' ">{{item.count}}</span></button>
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">创建时间:</label>
                                    <div class="layui-input-block" data-type="data" v-cloak="">
                                        <button class="layui-btn layui-btn-sm" type="button" v-for="item in dataList" @click="setData(item)" :class="{'layui-btn-primary':where.data!=item.value}">{{item.name}}</button>
                                        <button class="layui-btn layui-btn-sm" type="button" ref="time" @click="setData({value:'zd',is_zd:true})" :class="{'layui-btn-primary':where.data!='zd'}">自定义</button>
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" v-show="showtime==true" ref="date_time">{$year.0} - {$year.1}</button>
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">订单号:</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="real_name" style="width: 50%" v-model="where.real_name" placeholder="请输入姓名、电话、订单编号" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <div class="layui-input-block">
                                        <button @click="search" type="button" class="layui-btn layui-btn-sm layui-btn-normal">
                                            <i class="layui-icon layui-icon-search"></i>搜索</button>
                                        <button @click="excel" type="button" class="layui-btn layui-btn-warm layui-btn-sm export" type="button">
                                            <i class="fa fa-floppy-o" style="margin-right: 3px;"></i>导出</button>
                                        <button @click="refresh" type="reset" class="layui-btn layui-btn-primary layui-btn-sm">
                                            <i class="layui-icon layui-icon-refresh" ></i>刷新</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--列表-->
        <div class="layui-row layui-col-space15" >
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-header">订单列表</div>
                    <div class="layui-card-body">
                        <table class="layui-hide" id="List" lay-filter="List"></table>
                        <!--订单-->
                        <script type="text/html" id="order_id">
                            <h4>{{d.order_id}}</h4>
                            <span style="color: {{d.color}};">{{d.pink_name}}</span>　　
                        </script>
                        <!--用户信息-->
                        <script type="text/html" id="userinfo">
                            {{d.nickname==null ? '暂无信息':d.nickname}}/{{d.uid}}
                        </script>
                        <!--支付状态-->
                        <script type="text/html" id="paid">
                            {{#  if(d.pay_type==1){ }}
                            <p>{{d.pay_type_name}}</p>
                            {{#  }else{ }}
                            {{# if(d.pay_type_info!=undefined && d.pay_type_info==1){ }}
                            <p><span>线下支付</span></p>
                            <p><button type="button" lay-event='offline_btn' class="offline_btn btn btn-w-m btn-white">立即支付</button></p>
                            {{# }else{ }}
                            <p>{{d.pay_type_name}}</p>
                            {{# } }}
                            {{# }; }}
                        </script>
                        <!--订单状态-->
                        <script type="text/html" id="status">
                            {{d.status_name}}
                        </script>
                        <!--详情-->
                        <script type="text/html" id="order_info">
                            <button class="btn btn-white btn-bitbucket btn-xs" onclick="$eb.createModalFrame('{{d.nickname}}-订单详情','{:Url('order_info')}?oid={{d.id}}')">
                                <i class="fa fa-file-text"></i> 订单详情
                            </button>
                        </script>
                        <script type="text/html" id="act">
                            <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                            <ul class="layui-nav-child layui-anim layui-anim-upbit">
                                <li>
                                    <a lay-event='marke' href="javascript:void(0);" >
                                        <i class="fa fa-paste"></i> 订单备注
                                    </a>
                                </li>
                                {{# if(d.pay_price != d.refund_price && d.refund_status > 1  && d.status==-1){ }}
                                <li>
                                    <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})">
                                        <i class="fa fa-history"></i>立即退款
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" onclick="$eb.createModalFrame('不退款','{:Url('refund_n')}?id={{d.id}}',{w:400,h:300})">
                                        <i class="fa fa-openid"></i> 不退款
                                    </a>
                                </li>
                                {{# } ;}}
                                <li>
                                    <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                                        <i class="fa fa-newspaper-o"></i> 订单记录
                                    </a>
                                </li>
                            </ul>
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <!--end-->
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    })
    function dropdown(that){
        var oEvent = arguments.callee.caller.arguments[0] || event;
        oEvent.stopPropagation();
        var offset = $(that).offset();
        var top=offset.top-$(window).scrollTop();
        var index = $(that).parents('tr').data('index');
        $('.layui-nav-child').each(function (key) {
            if (key != index) {
                $(this).hide();
            }
        })
        if($(document).height() < top+$(that).next('ul').height()){
            $(that).next('ul').css({
                'padding': 10,
                'top': - ($(that).parent('td').height() / 2 + $(that).height() + $(that).next('ul').height()/2),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }else{
            $(that).next('ul').css({
                'padding': 10,
                'top':$(that).parent('td').height() / 2 + $(that).height(),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }
    }
    var real_name='<?=$real_name?>';
    var orderCount={};
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                orderStatus: [
                    {name: '全部', value: ''},
                    {name: '未支付', value: 5, count: orderCount.wz},
                    {name: '待接单', value: 0, count: orderCount.wf, class: true},
                    {name: '派送中', value: 1, count: orderCount.dp},
                    {name: '已送达', value: 2, count: orderCount.jy},
                    {name: '已退款', value: -2, count: orderCount.yt},
                ],
                dataList: [
                    {name: '全部', value: ''},
                    {name: '昨天', value: 'yesterday'},
                    {name: '今天', value: 'today'},
                    {name: '本周', value: 'week'},
                    {name: '本月', value: 'month'},
                    {name: '本季度', value: 'quarter'},
                    {name: '本年', value: 'year'},
                ],
                orderType:[
                    {name: '全部', value: ''},
                    {name: '帮我买', value: 1},
                    {name: '帮我送', value: 2},
                    {name: '帮我取', value: 3},
                    {name: '个性服务', value: 4},
                ],
                where: {
                    data: '',
                    status: '',
                    type: '',
                    real_name: real_name || '',
                    excel: 0,
                    order_type: '',
                },
                showtime: false,
            },
            watch: {},
            methods: {
                search:function () {
                    layList.reload(this.where);
                },
                refresh:function () {
                    layList.reload();
                },
                excel:function () {
                    this.where.excel=1;
                    location.href=layList.U({a:'order_list',q:this.where});
                },
                setData:function(item){
                    var that=this;
                    if(item.is_zd==true){
                        that.showtime=true;
                        this.where.data=this.$refs.date_time.innerText;
                    }else{
                        this.showtime=false;
                        this.where.data=item.value;
                    }
                },
            },
            mounted:function () {
                var that=this;
                layList.laydate.render({
                    elem:this.$refs.date_time,
                    trigger:'click',
                    eventElem:this.$refs.time,
                    range:true,
                    change:function (value){
                        that.where.data=value;
                    }
                });
                layList.tableList('List',layList.U({a:"order_list",p:{real_name:real_name}}),function (){
                    return [
                        {field: 'order_id', title: '订单号', sort: true,event:'order_id',width:'12%',templet:'#order_id'},
                        {field: 'nickname', title: '用户信息',templet:'#userinfo',width:'8%'},
                        {field: 'good_name', title: '商品名称',width:'10%'},
                        {field: 'pay_price', title: '实际支付',width:'8%'},
                        {field: 'delivery_price', title: '配送金额',width:'8%'},
                        {field: 'delivery_time', title: '配送时效',width:'8%'},
                        {field: 'paid', title: '支付状态',templet:'#paid',width:'8%'},
                        {field: 'status', title: '订单状态',templet:'#status',width:'8%'},
                        {field: 'user_make', title: '用户备注',width:'15%'},
                        {field: 'order_info', title: '详情',templet:'#order_info',width:'8%'},
                        {field: 'right', title: '操作',align:'center',toolbar:'#act'},
                    ];
                });

                layList.tool(function (event,data,obj) {
                    switch (event) {
                        case 'marke':
                            var url =layList.U({a:'remark',q:{id:data.id}}),make=data.admin_make;
                            $eb.$alert('textarea',{title:'请修改内容',value:make},function (result) {
                                if(result){
                                    $.ajax({
                                        url:url,
                                        data:{admin_make:result},
                                        type:'post',
                                        dataType:'json',
                                        success:function (res) {
                                            if(res.code == 200) {
                                                $eb.$swal('success',res.msg);
                                            }else
                                                $eb.$swal('error',res.msg);
                                        }
                                    })
                                }else{
                                    $eb.$swal('error','请输入要备注的内容');
                                }
                            });
                            break;
                        case 'danger':
                            var url =layList.U({c:'order.store_order',a:'take_delivery',p:{id:data.id}});
                            $eb.$swal('delete',function(){
                                $eb.axios.get(url).then(function(res){
                                    if(res.status == 200 && res.data.code == 200) {
                                        $eb.$swal('success',res.data.msg);
                                    }else
                                        return Promise.reject(res.data.msg || '收货失败')
                                }).catch(function(err){
                                    $eb.$swal('error',err);
                                });
                            },{'title':'您确定要修改收货状态吗？','text':'修改后将无法恢复,请谨慎操作！','confirm':'是的，我要修改'})
                            break;
                        case 'offline_btn':
                            var url =layList.U({c:'order.store_order',a:'offline',p:{id:data.id}}),pay_price =data.pay_price;
                            $eb.$swal('delete',function(){
                                $eb.axios.get(url).then(function(res){
                                    if(res.status == 200 && res.data.code == 200) {
                                        $eb.$swal('success',res.data.msg);
                                    }else
                                        return Promise.reject(res.data.msg || '收货失败')
                                }).catch(function(err){
                                    $eb.$swal('error',err);
                                });
                            },{'title':'您确定要修改已支付'+pay_price+'元的状态吗？','text':'修改后将无法恢复,请谨慎操作！','confirm':'是的，我要修改'})
                            break;
                    }
                });

            }
        })
    })
</script>
{/block}