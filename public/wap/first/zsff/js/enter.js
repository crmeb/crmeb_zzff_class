(function(global,factory){
    typeof define == 'function' && define(['store','helper','vue','reg-verify'],factory);
})(this,function(app,$h,Vue,$reg){
    Vue.component('enter',{
        props: ['appear','url','site_name'],
        template:`<div>
                        <div class="entry" :class="appear?'':'up'">
                            <div class="title"><span class="iconfont icon-guanbi1" @click="close"></span>手机号登录</div>
                            <div class="entry-list">
                                <div class="item"><input type="number" v-model="phone" placeholder="请输入手机号"></div>
                                <div class="item item1 acea-row row-between-wrapper">
                                    <input type="number" style="width: 3.85rem;" v-model="code_num" placeholder="请输入验证码">
                                    <button class="code" style="height:100%;" :disabled="active" :class="active == true?'on':''" @click="code">{{timetext}}</button>
                                </div>
                            </div>
                            <div class="select-btn">
                                <div class="checkbox-wrapper"><label class="well-check"><input type="checkbox" v-model="agree" name="" value="1" checked="checked"><i class="icon"></i>已阅读并同意 <a :href="goagree()">[{{site_name}}付费用户协议]</a></label></div>
                            </div>
                            <div class="enterBnt acea-row row-center-wrapper" @click="login">登录</div>
                        </div>
                        <div class="mask" @touchmove.prevent :hidden="appear" @click.stop="close"></div>
                    </div>`,
        data:function () {
            return {
                timetext:'获取验证码',
                active:false,
                phone:'',
                code_num:'',
                agree:1,
            }
        },
        mounted:function(){
            var that=this;
            $(document).ready(function () {
                that.$nextTick(function () {
                    $("input").blur(function () {
                        document.body && (document.body.scrollTop = document.body.scrollTop);
                    })
                })
            })
        },
        methods:{
            goagree:function(){
                return $h.U({c:"index",a:'agree'});
            },
            login:function(){
                var that=this;
                if(!this.phone) return $h.pushMsgOnce('请输入手机号码');
                if(!$reg.isPhone(this.phone)) return $h.pushMsgOnce('您输入的手机号码不正确');
                if(!that.code_num) return $h.pushMsgOnce('请输入验证码');
                if(!that.agree) return $h.pushMsgOnce('请同意'+that.site_name+'付费用户协议');
                $h.loadFFF();
                that.url=that.url ? that.url : $h.U({c:'index',a:'login'});
                app.basePost(that.url,{phone:this.phone,code:this.code_num},function (res) {
                    $h.loadClear();
                    $h.showMsg({
                        title:res.data.msg,
                        icon:'success',
                        success:function () {
                            that.$emit("change",{action:'logComplete',value:res.data.data});
                        }
                    });
                },function (res) {
                    $h.loadClear();
                    $h.showMsg(res)
                },true);
            },
            code:function () {
                var that = this;
                if(!that.phone) return $h.pushMsgOnce('请输入手机号码');
                if(!$reg.isPhone(that.phone)) return $h.pushMsgOnce('请输入正确的手机号码');
                that.active = true;
                var n = 60;
                app.baseGet($h.U({c:'public_api',a:'code',q:{phone:that.phone}}),function (res){
                    var data=res.data.data;
                    if(data.Message=='OK' || data.Code=='OK'){
                        var run =setInterval(function(){
                            n--;
                            if(n<0){
                                clearInterval(run);
                            }
                            that.timetext = "剩余 "+n+"s";
                            if(that.timetext<"剩余 "+0+"s"){
                                that.active = false;
                                that.timetext = "重发";
                            }
                        },1000);
                    }else{
                        if(data.Code=='isv.BUSINESS_LIMIT_CONTROL')
                            $h.pushMsgOnce('您的技能正在冷却中,请稍后再试!');
                        else
                            $h.pushMsgOnce(data.Message);
                        that.active =false;
                    }
                },function (res) {
                    that.active =false;
                });
            },
            close:function (){
                this.$emit("change",{action:'loginClose',value:true})//$emit():注册事件；
            }
        }
    });
})