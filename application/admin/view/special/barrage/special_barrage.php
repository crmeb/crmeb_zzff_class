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
<div class="layui-fluid" style="background: #fff">
    <div class="layui-row layui-col-space15" id="app">
        <div class="layui-col-md12">
            <form action="" class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">弹幕开关</label>
                    <div class="layui-input-block">
                        <input type="checkbox" checked="" name="open_barrage" lay-skin="switch" :value="open_barrage" lay-filter='is_show' lay-text="打开|关闭" :checked="open_barrage">
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card" v-cloak="">
                <div class="layui-card-header">添加弹幕</div>
                <div class="layui-card-body" style="padding: 10px 150px;">
                    <form action="" class="layui-form">
                        <div class="layui-form-item m-t-5" v-cloak="">
                            <label class="layui-form-label">头    像</label>
                            <div class="layui-input-block">
                                <div class="upload-image-box" v-if="avatar" @mouseenter="is_show = true" @mouseleave="is_show = false">
                                    <img :src="avatar" alt="" style="border-radius: 5px;">
                                    <div class="mask" v-show="is_show" style="display: block">
                                        <p><i class="fa fa-eye" @click="look(avatar)"></i><i class="fa fa-trash-o" @click="avatar = ''"></i></p>
                                    </div>
                                </div>
                                <div class="upload-image"  v-show="!avatar" @click="upload(1)">
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>上传图片</p>
                                </div>
                            </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">昵    称</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" style="width: 50%" v-model="nickname" autocomplete="off" placeholder="请输入昵称" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">排    序</label>
                            <div class="layui-input-block">
                                <input type="number" name="sort" style="width: 50%" v-model="sort" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">动    作</label>
                            <div class="layui-input-block">
                                <input type="radio" name="action" value="1" title="开团" v-model="action" lay-filter="action">
                                <input type="radio" name="action" value="2" title="参团" v-model="action" lay-filter="action">
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" type="button" @click="save_barrage">{{id ? '立即修改':'立即提交'}}</button>
                                <button class="layui-btn layui-btn-primary clone" type="button" @click="empty_barrage">清空</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card" v-cloak="">
                <div class="layui-card-header">弹幕列表</div>
                <blockquote class="layui-elem-quote layui-quote-nm" style="margin: 10px;">
                    点击列表里面的弹幕可进行修改,取消修改请点击【清空】按钮；
                </blockquote>
                <div class="layui-row">
                    <div class="layui-col-md1 layui-barrage-box" :class="id==item.id ? 'border-color':'' " v-for="(item,index) in barrageList" @click.stop="set_barrage(item)">
                        <p @click.stop="del_barrage(item,index)" class="del-text"><span>删除</span></p>
                        <img :src="item.avatar" alt="" style="width: 100%;height: 136px;">
                        <p><span>用户名：</span>{{ item.nickname.length > 6 ? item.nickname.slice(0,5) : item.nickname }}</p>
                        <p><span>排序：</span>{{ item.sort }}</p>
                        <p><span>类型：</span>{{ item.action==1 ? '开团':'参团' }}</p>
                    </div>
                    <div class="layui-col-md12 layui-empty-text" v-if="barrageList.length <= 0">
                        <p>暂无数据</p>
                    </div>
                </div>
                <div class="layui-row" style="text-align: right;">
                    <div ref="barrage_page"></div>
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
    var open_barrage=<?=$open_barrage ? (int)$open_barrage : 0?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                avatar:'',
                nickname:'',
                sort:0,
                action:1,
                id:0,
                open_barrage:open_barrage,
                barrageList:[],
                loading:false,
                loadend:false,
                page:1,
                limit:22,
                is_show:false
            },
            watch:{
                page:function () {
                    this.get_barrage_list();
                }
            },
            methods:{
                del_barrage:function(item,index){
                    var that=this;
                    layList.layer.confirm('是否删除【'+item.nickname+'】虚拟用户的弹幕？', {
                        btn: ['删除','我在想想'] //按钮
                    }, function(){
                        layList.baseGet(layList.U({a:'del_barrage',q:{id:item.id}}),function () {
                            that.get_barrage_list();
                            layList.msg('删除成功');
                        });
                    }, function(){
                        that.get_barrage_list();
                    });
                },
                set_barrage:function(item){
                    this.action=item.action;
                    this.avatar=item.avatar;
                    this.avatar.is_show=false;
                    this.$set(this,'avatar',this.avatar);
                    this.sort=item.sort;
                    this.id=item.id;
                    this.nickname=item.nickname;
                    this.$nextTick(function () {
                        layList.form.render('radio');
                    })
                },
                empty_barrage:function(){
                    if(this.id){
                        this.avatar='';
                        this.nickname='';
                        this.sort=0;
                        this.action=1;
                        this.id=0;
                    }else{
                        this.avatar='';
                        this.nickname='';
                        this.sort=0;
                        this.action=1;
                    }
                },
                save_barrage:function(){
                  var that=this;
                  if(!this.avatar) return layList.msg('请上传虚拟用户头像');
                  if(!this.nickname) return layList.msg('请输入虚拟用户昵称');
                  layList.loadFFF();
                  layList.basePost(layList.U({a:'save_barrage'}),{nickname:that.nickname,avatar:that.avatar,action:that.action,sort:that.sort,id:that.id},function (res) {
                      layList.loadClear();
                      if(that.id) return layList.msg('修改成功',function () {
                          that.avatar='';
                          that.nickname='';
                          that.sort=0;
                          that.action=1;
                          that.id=0;
                          that.$nextTick(function () {
                              layList.form.render('radio');
                          })
                      });
                      layList.layer.confirm('添加成功，是保留当前内容？', {
                          btn: ['保留','清空'] //按钮
                      }, function(){
                          that.get_barrage_list();
                      }, function(){
                          that.avatar='';
                          that.nickname='';
                          that.sort=0;
                          that.action=1;
                          that.get_barrage_list();
                      });
                  },function (res) {
                      layList.loadClear();
                  });
                },
                //删除图片
                delect:function(act,key,index) {
                    var that = this;
                    that.avatar = '';
                },
                //查看图片
                look:function(pic){
                    $eb.openImage(pic);
                },
                //鼠标移入事件
                enter:function(){
                    this.avatar.is_show=true;
                    this.$set(this,'avatar',this.avatar);
                },
                //鼠标移出事件
                leave:function(){
                    this.avatar.is_show=false;
                    this.$set(this,'avatar',this.avatar);
                },
                changeIMG:function(key,value,multiple){
                    if(multiple){
                        var that = this;
                        value.map(function (v) {
                            that[key].push({pic:v,is_show:false});
                        });
                        this.$set(this,key,this[key]);
                    }else{
                        this.$set(this,key,value);
                    }
                },
                //上传图片
                upload:function() {
                    console.log(11);
                    ossUpload.createFrame('选择头像',{fodder:'avatar'},{w:800,h:550});
                },
                get_barrage_list:function(){
                    var that=this;
                    if(that.loading) return;
                    if(that.loadend) return;
                    that.loading=true;
                    layList.baseGet(layList.U({a:'get_barrage_list',q:{page:that.page,limit:that.limit}}),function (res){
                        that.loading=false;
                        that.$set(that,'barrageList',res.data.list);
                        that.set_page(res.data.count);
                        that.$nextTick(function () {
                            layList.form.render('radio');
                        })
                    },function (res) {
                        that.loading=false;
                    });
                },
                set_page:function (count){
                    var that=this;
                    layList.laypage.render({
                        elem: this.$refs.barrage_page,
                        count: count,
                        limit:this.limit,theme: '#1E9FFF',
                        jump:function (obj,first) {
                            that.page=obj.curr;
                        }
                    });
                }
            },
            mounted:function () {
                this.$nextTick(function () {
                    layList.form.render();
                });
                this.get_barrage_list();
                window.changeIMG = this.changeIMG;
                layList.form.on('radio(action)',function (data) {
                    that.action=data.value;
                });
                layList.switch('is_show',function (odj,value) {
                    if(odj.elem.checked==true){
                        layList.baseGet(layList.Url({a:'set_barrage_show',p:{value:1,key_nime:'open_barrage'}}),function (res) {
                            layList.msg(res.msg);
                        });
                    }else{
                        layList.baseGet(layList.Url({a:'set_barrage_show',p:{value:0,key_nime:'open_barrage'}}),function (res) {
                            layList.msg(res.msg);
                        });
                    }
                });
            }
        })
    })
</script>
{/block}