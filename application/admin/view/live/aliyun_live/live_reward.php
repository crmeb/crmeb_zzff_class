{extend name="public/container"}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-carousel layadmin-carousel layadmin-shortcut" lay-anim="" lay-indicator="inside" lay-arrow="none" style="background:none">
                        <div class="layui-card-body">
                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">搜索内容:</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="user_info" style="width: 50%" v-model="where.user_info" placeholder="请输入搜索内容" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label" >直播间</label>
                                    <div class="layui-input-block">
                                        <select name="live_id"  v-model="where.live_id" style="width: 50%" class="layui-input">
                                            <option value="">全部</option>
                                            <option v-for="item in live_studio" :value="item.id">{{ item.live_title }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <label class="layui-form-label">创建时间:</label>
                                    <div class="layui-input-block" data-type="date" v-cloak="">
                                        <button class="layui-btn layui-btn-sm" type="button" v-for="item in dateList" @click="setData(item)" :class="{'layui-btn-primary':where.date!=item.value}">{{item.name}}</button>
                                        <button class="layui-btn layui-btn-sm" type="button" ref="time" @click="setData({value:'zd',is_zd:true})" :class="{'layui-btn-primary':where.date!='zd'}">自定义</button>
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" v-show="showtime==true" ref="date_time">{$year.0} - {$year.1}</button>
                                    </div>
                                </div>
                                <div class="layui-col-lg12">
                                    <div class="layui-input-block">
                                        <button @click="search" type="button" class="layui-btn layui-btn-sm layui-btn-normal">
                                            <i class="layui-icon layui-icon-search"></i>搜索</button>
                                        <button @click="refresh" type="reset" class="layui-btn layui-btn-primary layui-btn-sm">
                                            <i class="layui-icon layui-icon-refresh" ></i>刷新</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

    </div>
    <!--产品列表-->
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <div class="alert alert-info" role="alert">
                    <!--列表[排序]可进行快速修改,双击或者单击进入编辑模式,失去焦点可进行自动保存
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                </div>
               <!-- <div class="layui-btn-container">
                    <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i>  刷新</button>
                </div>-->
                <table class="layui-hide" id="List" lay-filter="List"></table>
                <script type="text/html" id="is_pink">
                    {{# if(d.is_pink){ }}
                    <span class="layui-badge layui-bg-green">拼团开启</span>
                    {{# }else{ }}
                    <span class="layui-badge">拼团关闭</span>
                    {{# } }}
                </script>
                <!-- <script type="text/html" id="gis_show">
                     <input type='checkbox' name='live_goods_id' lay-skin='switch' value="{{d.live_goods_id}}" lay-filter='gis_show' lay-text='显示|隐藏'  {{ d.gis_show == 1 ? 'checked' : '' }}>
                 </script>-->
                <script type="text/html" id="image">
                    <img style="cursor: pointer;width: 80px;height: 40px;" lay-event='open_image' src="{{d.gift_image}}">
                </script>
                <script type="text/html" id="act">
                    <button type="button" class="layui-btn layui-btn-xs" onclick="dropdown(this)">操作 <span class="caret"></span></button>
                    <ul class="layui-nav-child layui-anim layui-anim-upbit">
                        <li>
                            <a lay-event='delect' href="javascript:void(0)">
                                <i class="fa fa-trash"></i> 删除推荐课程
                            </a>
                        </li>
                    </ul>
                </script>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    //实例化form
    // layList.form.render();
    // layList.date({elem:'#start_time',theme:'#393D49',type:'datetime'});
    // layList.date({elem:'#end_time',theme:'#393D49',type:'datetime'});
    //加载列表
    layList.tableList({
        o:'List',
        done:function () {

        }
    },"{:Url('live_reward_list',[])}",function (){
        return [
            {field: 'id', title: '编号', sort: true,/*event:'live_goods_id',*/width:'5%',align: 'center'},
            {field: 'live_title', title: '直播间',align: 'center'},
            {field: 'nickname', title: '用户名',align: 'center'},
            {field: 'avatar', title: '用户头像',align: 'center',templet: '<p><img class="avatar" style="cursor: pointer" class="open_image" data-image="{{d.avatar}}" src="{{d.avatar}}" alt="{{d.nickname}}"></p>'},
            {field: 'gift_name', title: '礼物名称'},
            {field: 'gift_image', title: '礼物图标',templet:'#image',align: 'center'},
            {field: 'gift_price', title: '礼物单价（{$gold_info["gold_name"]}）'},
            {field: 'gift_num', title: '礼物数量'},
            {field: 'total_price', title: '总额（{$gold_info["gold_name"]}）',sort: true, align: 'center'},
            {field: 'add_time', title: '贡献时间',align: 'center'},
        ];
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
    //自定义方法
    var action= {
        set_value: function (field, id, value, model_type) {
            layList.baseGet(layList.Url({
                c:'special.special_type',
                a: 'set_value',
                q: {field: field, id: id, value: value, model_type:model_type}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
        //打开新添加页面
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['100%', '100%']
                ,title: title
                ,shade: 0.6 //遮罩透明度
                ,maxmin: true //允许全屏最小化
                ,anim: 1 //0-6的动画形式，-1不开启
                ,content: url
                ,end:function() {
                    location.reload();
                }
            });
        }
    }
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('gis_show',function (odj,value) {
        var is_show_value = 0
        if(odj.elem.checked==true){
            var is_show_value = 1
        }
        action.set_value('is_show',value,is_show_value,'live_goods');
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.live_goods_id,value=obj.value;
        switch (obj.field) {
            case 'gsort':
                action.set_value('sort',id,value,'live_goods');
                break;
            case 'gfake_sales':
                action.set_value('fake_sales',id,value,'live_goods');
                break;
        }
    });
    //监听并执行排序
    layList.sort(['live_goods_id','gsort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({c:'special.special_type',a:'set_value',q:{id:data.live_goods_id, field:'is_delete',value:1,model_type:'live_goods'}});
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
                })
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })
    var live_studio='<?=$live_studio;?>';
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                live_studio: JSON.parse(live_studio),
                dateList: [
                    {name: '全部', value: ''},
                    {name: '昨天', value: 'yesterday'},
                    {name: '今天', value: 'today'},
                    {name: '本周', value: 'week'},
                    {name: '本月', value: 'month'},
                    {name: '本季度', value: 'quarter'},
                    {name: '本年', value: 'year'},
                ],
                where:{
                    date:'',
                    user_info:'',
                    live_id:'',
                },
                showtime: false,
            },
            watch: {

            },
            methods: {
                setData:function(item){
                    var that=this;
                    if(item.is_zd==true){
                        that.showtime=true;
                        this.where.date=this.$refs.date_time.innerText;
                    }else{
                        this.showtime=false;
                        this.where.date=item.value;
                    }
                },
                getBadge:function() {
                    var that=this;
                    layList.basePost(layList.Url({c:'live.aliyun_live',a:'getBadge'}),this.where,function (rem) {
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
                        that.where.date=value;
                    }
                });
            }
        })
    });
</script>
{/block}

