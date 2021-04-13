{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">订单管理</div>
                <div class="layui-card-body">
                    <div class="layui-carousel layadmin-carousel layadmin-shortcut" lay-anim="" lay-indicator="inside" lay-arrow="none" style="background:none">
                        <div class="layui-card-body">
                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">订单状态:</label>
                                    <div class="layui-input-block">
                                        <button type="button" class="layui-btn layui-btn-sm" :class="{'layui-btn-primary':where.status!==item.value}" @click="where.status = item.value" v-for="item in orderStatus">{{item.name}}
                                            <span v-if="item.count!=undefined" :class="item.class!=undefined ? 'layui-badge': 'layui-badge layui-bg-gray' ">{{item.count}}</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">创建时间:</label>
                                    <div class="layui-input-block" data-type="data">
                                        <button type="button" class="layui-btn layui-btn-sm"  v-for="item in dataList" @click="setData(item)" :class="{'layui-btn-primary':where.data!=item.value}">{{item.name}}</button>
                                        <button type="button" class="layui-btn layui-btn-sm" ref="time" @click="setData({value:'zd',is_zd:true})" :class="{'layui-btn-primary':where.data!='zd'}">自定义</button>
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" v-show="showtime==true" ref="date_time">{$year.0} - {$year.1}</button>
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">订单类型:</label>
                                    <div class="layui-input-block">
                                        <button type="button" class="layui-btn layui-btn-sm" :class="{'layui-btn-primary':where.type!==item.value}" @click="where.type = item.value" v-for="item in orderType">{{item.name}}
                                            <span v-if="item.count!=undefined" :class="item.class!=undefined ? 'layui-badge': 'layui-badge layui-bg-gray' ">{{item.count}}</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">搜索内容:</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="real_name" style="width: 50%" v-model="where.real_name" placeholder="请输入搜索内容" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <div class="layui-input-block">
                                        <button @click="search" type="button" class="layui-btn layui-btn-sm layui-btn-normal">
                                            <i class="layui-icon layui-icon-search"></i>搜索
                                        </button>
                                        <button @click="excel" type="button" class="layui-btn layui-btn-warm layui-btn-sm export">
                                            <i class="fa fa-floppy-o" style="margin-right: 3px;"></i>导出
                                        </button>
                                        <button @click="refresh" type="reset" class="layui-btn layui-btn-primary layui-btn-sm">
                                            <i class="layui-icon layui-icon-refresh" ></i>刷新
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    </div>
    <div class="layui-row layui-col-space15" >
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">订单列表</div>
                <div class="layui-card-body">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
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
                            {{# if(d.pay_type_info!=undefined){ }}
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
                    <!--商品信息-->
                    <script type="text/html" id="info">
                        {{#  if(d.type==0 && d._info){ }}
                        <p>
                            <span><img style="width: 40px;height: 15px;cursor: pointer;" src="{{d._info.image}}"></span>
                            <span>{{d._info.title}}</span><span></span>
                        </p>
                        {{# }else if(d.type==1 && d._info){ }}
                        <p>
                            <span>{{d._info.title}}</span><span> | ￥{{d.pay_price}}</span>
                        </p>
                        {{# }else{ }}
                        无
                        {{# };}}
                    </script>
                    <!--详情-->
                    <script type="text/html" id="order_info">
                        <button class="btn btn-white btn-bitbucket btn-xs" onclick="$eb.createModalFrame('{{d.nickname}}-订单详情','{:Url('order_info')}?oid={{d.id}}')">
                            <i class="fa fa-file-text"></i> 订单详情
                        </button>
                    </script>
                    <script type="text/html" id="act">
                        {{#  if(d._status==1){ }}
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('修改订单','{:Url('edit')}?id={{d.id}}')">
                                    <i class="fa fa-file-text"></i> 修改订单
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
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                                    <i class="fa fa-file-text"></i> 订单详情
                                </a>
                            </li>
                        </ul>
                        {{#  }else if(d._status==2){ }}
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a lay-event='marke' href="javascript:void(0);" >
                                    <i class="fa fa-paste"></i> 订单备注
                                </a>
                            </li>
                            {{#  if(d.pay_price!=d.refund_price && d.type!=1){ }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})">
                                    <i class="fa fa-history"></i> 立即退款
                                </a>
                            </li>
                            {{#  }else if(d.use_integral > 0 && d.use_integral >= d.back_integral && d.type!=1){ }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退积分','{:Url('integral_back')}?id={{d.id}}')">
                                    <i class="fa fa-history"></i> 退积分
                                </a>
                            </li>
                            {{# } ;}}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                                    <i class="fa fa-newspaper-o"></i> 订单记录
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                                    <i class="fa fa-file-text"></i> 订单详情
                                </a>
                            </li>
                        </ul>
                        {{#  }else if(d._status==3){ }}
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            {{#  if(d.use_integral > 0 && d.use_integral >= d.back_integral){ }}
                            <li>
                                <a lay-event='marke' href="javascript:void(0);">
                                    <i class="fa fa-paste"></i> 订单备注
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退积分','{:Url('integral_back')}?id={{d.id}}',{w:400,h:300})">
                                    <i class="fa fa-history"></i> 退积分
                                </a>
                            </li>
                            {{# }else if(d.pay_price != d.refund_price){ }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}',{w:400,h:300})">
                                    <i class="fa fa-history"></i>立即退款
                                </a>
                            </li>
                            {{# } ;}}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('不退款','{:Url('refund_n')}?id={{d.id}}',{w:400,h:300})">
                                    <i class="fa fa-openid"></i> 不退款
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                                    <i class="fa fa-newspaper-o"></i> 订单记录
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                                    <i class="fa fa-file-text"></i> 订单详情
                                </a>
                            </li>
                        </ul>
                        {{#  }else if(d._status==4){ }}
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a lay-event='marke' href="javascript:void(0);" >
                                    <i class="fa fa-paste"></i> 订单备注
                                </a>
                            </li>
                            <li>
                                <a lay-event='danger' href="javascript:void(0);">
                                    <i class="fa fa-cart-arrow-down"></i> 已收货
                                </a>
                            </li>
                            {{#  if(d.pay_price != d.refund_price){ }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}')">
                                    <i class="fa fa-history"></i> 立即退款
                                </a>
                            </li>
                            {{# }else if(d.use_integral > 0 && d.use_integral >= d.back_integral){ }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退积分','{:Url('integral_back')}?id={{d.id}}')">
                                    <i class="fa fa-history"></i> 退积分
                                </a>
                            </li>
                            {{# } }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                                    <i class="fa fa-newspaper-o"></i> 订单记录
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                                    <i class="fa fa-file-text"></i> 订单详情
                                </a>
                            </li>
                        </ul>
                        {{#  }else if(d._status==5 || d._status==6){ }}
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a lay-event='marke' href="javascript:void(0);" >
                                    <i class="fa fa-paste"></i> 订单备注
                                </a>
                            </li>
                            {{#  if(d.pay_price != d.refund_price){ }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退款','{:Url('refund_y')}?id={{d.id}}')">
                                    <i class="fa fa-history"></i> 立即退款
                                </a>
                            </li>
                            {{# }else if(d.use_integral > 0 && d.use_integral >= d.back_integral){ }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退积分','{:Url('integral_back')}?id={{d.id}}')">
                                    <i class="fa fa-history"></i> 退积分
                                </a>
                            </li>
                            {{# } }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                                    <i class="fa fa-newspaper-o"></i> 订单记录
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                                    <i class="fa fa-file-text"></i> 订单详情
                                </a>
                            </li>
                        </ul>
                        {{#  }else if(d._status==7){ }}
                        <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a lay-event='marke' href="javascript:void(0);" >
                                    <i class="fa fa-paste"></i> 订单备注
                                </a>
                            </li>
                            {{# if(d.use_integral > 0 && d.use_integral >= d.back_integral){ }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('退积分','{:Url('integral_back')}?id={{d.id}}')">
                                    <i class="fa fa-history"></i> 退积分
                                </a>
                            </li>
                            {{# } }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单记录','{:Url('order_status')}?oid={{d.id}}')">
                                    <i class="fa fa-newspaper-o"></i> 订单记录
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('订单详情','{:Url('order_info')}?oid={{d.id}}')">
                                    <i class="fa fa-file-text"></i> 订单详情
                                </a>
                            </li>
                        </ul>
                        {{#  }; }}
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
    layList.tableList('List',"{:Url('order_list',['real_name'=>$real_name])}",function (){
        return [
            {field: 'order_id', title: '订单号', sort: true,event:'order_id',width:'15%',templet:'#order_id'},
            {field: 'nickname', title: '用户信息',templet:'#userinfo',width:'10%',align: 'center'},
            {field: 'info', title: '商品信息',templet:"#info",width:'22%'},
            {field: 'spread_name', title: '推广人',width:'10%',align: 'center'},
            {field: 'spread_name_two', title: '上级推广人',width:'10%',align: 'center'},
            {field: 'pay_price', title: '实际支付',width:'8%',align: 'center'},
            {field: 'paid', title: '支付状态',templet:'#paid',width:'8%',align: 'center'},
            {field: 'status', title: '订单状态',templet:'#status',width:'8%',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'},
        ];
    });
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'marke':
                var url =layList.U({c:'order.store_order',a:'remark'}),
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
    var orderCount=<?=json_encode($orderCount)?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                orderStatus: [
                    {name: '全部', value: ''},
                    {name: '未支付', value: 0,count:orderCount.wz},
                    {name: '已支付', value: 1,count:orderCount.wf,class:true},
                    {name: '已退款', value: -2,count:orderCount.yt},
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
                    {name: '普通订单', value: 5,count:orderCount.pt,class:true},
                    {name: '拼团订单', value: 6,count:orderCount.pu},
                    {name: '礼物订单', value: 7,count:orderCount.lw},
                ],
                where:{
                    data:'',
                    status:'',
                    type:'',
                    real_name:real_name || '',
                    excel:0,
                    spread_type:'',
                },
                showtime: false,
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
                    layList.basePost(layList.Url({c:'order.store_order',a:'getBadge'}),this.where,function (rem) {
                        that.badge=rem.data;
                    });
                },
                search:function () {
                    this.getBadge();
                    layList.reload(this.where,true);
                },
                refresh:function () {
                    layList.reload();
                    this.getBadge();
                },
                excel:function () {
                    this.where.excel=1;
                    location.href=layList.U({c:'order.store_order',a:'order_list',q:this.where});
                }
            },
            mounted:function () {
                var that=this;
                that.getBadge();
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
