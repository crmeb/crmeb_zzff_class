{extend name="public/container"}
{block name="head_top"}

{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <!--搜索条件-->
        <div class="layui-col-md12">
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
        <!--end-->
        <!-- 中间详细信息-->
        <div :class="item.col!=undefined ? 'layui-col-sm'+item.col+' '+'layui-col-md'+item.col:'layui-col-sm6 layui-col-md3'" v-for="item in badge" v-cloak="" v-if="item.count > 0">
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
    <!--列表-->
    <div class="layui-row layui-col-space15" >
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">订单列表</div>
                <div class="layui-card-body">
<!--                    <div class="layui-btn-container" id="container-action">-->
<!--                        <button class="layui-btn layui-btn-sm" data-type="del_order">批量删除订单</button>-->
<!--                        <button class="layui-btn layui-btn-sm layui-btn-warm" data-type="write_order">订单核销</button>-->
<!--                    </div>-->
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <!--订单-->
                    <script type="text/html" id="order_id">
                        {{d.order_id}}<br/>
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
                        {{# if(d.pay_type_info!=undefined){ }}
                        <p><span>线下支付</span></p>
                        {{# }else{ }}
                        <p>{{d.pay_type_name}}</p>
                        {{# } }}
                        {{# }; }}
                    </script>
                    <!--订单状态-->
                    <script type="text/html" id="status">
                        {{d.status_name}}
                    </script>

                    <script type="text/html" id="act">
                        {{#  if(d._status==1){ }}
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a href="javascript:void(0);" lay-event='order_info'>
                                    <i class="fa fa-file-text"></i> 订单详情
                                </a>
                            </li>
                            <li>
                                <a lay-event='marke' href="javascript:void(0);" >
                                    <i class="fa fa-paste"></i> 订单备注
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                                    <i class="fa fa-newspaper-o"></i> 订单记录
                                </a>
                            </li>
                        </ul>
                        {{#  }else if(d._status==2){ }}
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a href="javascript:void(0);" lay-event='order_info'>
                                    <i class="fa fa-file-text"></i> 订单详情
                                </a>
                            </li>
                            <li>
                                <a lay-event='marke' href="javascript:void(0);" >
                                    <i class="fa fa-paste"></i> 订单备注
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                                    <i class="fa fa-newspaper-o"></i> 订单记录
                                </a>
                            </li>
                        </ul>
                        {{#  }else if(d._status==3){ }}
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a href="javascript:void(0);" lay-event='order_info'>
                                    <i class="fa fa-file-text"></i> 订单详情
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                                    <i class="fa fa-newspaper-o"></i> 订单记录
                                </a>
                            </li>
                        </ul>
                        {{#  }else if(d._status==4){ }}
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a href="javascript:void(0);" lay-event='order_info'>
                                    <i class="fa fa-file-text"></i> 订单详情
                                </a>
                            </li>
                            <li>
                                <a lay-event='marke' href="javascript:void(0);" >
                                    <i class="fa fa-paste"></i> 订单备注
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                                    <i class="fa fa-newspaper-o"></i> 订单记录
                                </a>
                            </li>
                        </ul>
                        {{#  }; }}
                    </script>
                </div>
            </div>
        </div>
    </div>
    <!--end-->
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    layList.tableList('List',"{:Url('order_list',['real_name'=>$real_name])}",function (){
        return [
            {type:'checkbox'},
            {field: 'order_id', title: '订单号', sort: true,event:'order_id',width:'15%',templet:'#order_id'},
            {field: 'nickname', title: '用户信息',templet:'#userinfo',width:'8%',align:'center'},
            {field: 'title', title: '活动标题',templet:"#title",height: 'full-20'},
            {field: 'ticket', title: '票名',templet:"#ticket",height: 'full-20',width:'8%'},
            {field: 'ticket_price', title: '票价',templet:"#ticket_price",height: 'full-20',width:'6%'},
            {field: 'number', title: '票总数',templet:"#number",height: 'full-20',width:'6%'},
            {field: 'pay_price', title: '实际支付',width:'6%',align:'center'},
            {field: 'paid', title: '支付状态',templet:'#paid',width:'6%',align:'center'},
            {field: 'status', title: '订单状态',templet:'#status',width:'6%',align:'center'},
            {field: 'add_time', title: '下单时间',width:'8%',sort: true,align:'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'},
        ];
    });
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'order_paid':
                var url =layList.U({c:'order.sign_up',a:'offline',p:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                        }else
                            return Promise.reject(res.data.msg || '修改失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'您确定要修改支付状态吗？','text':'修改后将无法恢复,请谨慎操作！','confirm':'是的，我要修改'})
                break;
            case 'marke':
                var url =layList.U({c:'order.sign_up',a:'remark'}),
                    id=data.id,
                    make=data.remark;
                $eb.$alert('textarea',{title:'请修改内容',value:make},function (result) {
                    if(result){
                        $.ajax({
                            url:url,
                            data:'remark='+result+'&id='+id,
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
                var url =layList.U({c:'order.sign_up',a:'take_delivery',p:{id:data.id}});
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
            case 'order_info':
                $eb.createModalFrame(data.nickname+'订单详情',layList.U({a:'order_info',q:{oid:data.id}}));
                break;
        }
    })
    var action={
        del_order:function () {
            var ids=layList.getCheckData().getIds('id');
            if(ids.length){
                var url =layList.U({c:'order.sign_up',a:'del_order'});
                $eb.$swal('delete',function(){
                    $eb.axios.post(url,{ids:ids}).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'您确定要修删除订单吗？','text':'删除后将无法恢复,请谨慎操作！','confirm':'是的，我要删除'})
            }else{
                layList.msg('请选择要删除的订单');
            }
        },
        write_order:function () {
            return $eb.createModalFrame('订单核销',layList.U({a:'write_order'}),{w:500,h:400});
        },
    };
    $('#container-action').find('button').each(function () {
        $(this).on('click',function(){
            var act = $(this).data('type');
            action[act] && action[act]();
        });
    })
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
                'top': - ($(that).parents('td').height() / 2 + $(that).height() + $(that).next('ul').height()/2),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }else{
            $(that).next('ul').css({
                'padding': 10,
                'top':$(that).parents('td').height() / 2 + $(that).height(),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }
    }
    var real_name='<?=$real_name?>';
    var orderCount=<?=json_encode($orderCount)?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                orderStatus: [
                    {name: '全部', value: ''},
                    // {name: '未支付', value: 0,count:orderCount.wz},
                    {name: '待核销', value: 1,count:orderCount.wf,class:true},
                    {name: '已核销', value: 2,count:orderCount.ds},
                    {name: '已完成', value: 3,count:orderCount.dp},
                    // {name: '退款中', value: -1,count:orderCount.tk,class:true},
                    // {name: '已退款', value: -2,count:orderCount.yt},
                    // {name: '已删除', value: -4,count:orderCount.del},
                ],
                dataList: [
                    {name: '全部', value: ''},
                    {name: '今天', value: 'today'},
                    {name: '昨天', value: 'yesterday'},
                    {name: '最近7天', value: 'lately7'},
                    {name: '最近30天', value: 'lately30'},
                    {name: '本月', value: 'month'},
                    {name: '本年', value: 'year'},
                ],
                where:{
                    data:'',
                    status:status,
                    type:'',
                    pay_type:'',
                    real_name:real_name || '',
                    excel:0,
                },
                showtime: false,
            },
            watch: {
                'where.status':function () {
                    this.getBadge();
                    layList.reload(this.where,true);
                },
                'where.data':function () {
                    this.getBadge();
                    layList.reload(this.where,true);
                },
                'where.type':function () {
                    this.getBadge();
                    layList.reload(this.where,true);
                },
                'where.pay_type':function () {
                    this.getBadge();
                    layList.reload(this.where,true);
                }
            },
            methods: {
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
                getBadge:function() {
                    var that=this;
                    layList.basePost(layList.Url({c:'order.sign_up',a:'getBadge'}),this.where,function (rem) {
                        that.badge=rem.data;
                    });
                },
                search:function () {
                    this.where.excel=0;
                    this.getBadge();
                    layList.reload(this.where,true);
                },
                refresh:function () {
                    layList.reload();
                    this.getBadge();
                },
                excel:function () {
                    this.where.excel=1;
                    location.href=layList.U({c:'order.sign_up',a:'order_list',q:this.where});
                }
            },
            mounted:function () {
                var that=this;
                that.getBadge();
                window.formReload = this.search;
                layList.laydate.render({
                    elem:this.$refs.date_time,
                    trigger:'click',
                    eventElem:this.$refs.time,
                    range:true,
                    change:function (value){
                        that.where.data=value;
                    }
                });
            }
        })
    });
</script>
{/block}