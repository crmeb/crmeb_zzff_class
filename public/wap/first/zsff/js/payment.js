(function(global,factory){
    typeof define == 'function' && define(['store','helper','vue'],factory);
})(this,function(app,$h,Vue){
    Vue.component('payment',{
        props: {
            payment:{
                type:Boolean,
                default:false
            },
            money:{
                type:String,
                default:'0.00'
            },
            special_id:{
                type:Number,
                default:0,
            },
            pay_type_num:{
                type:Number,
                default:-1,
            },
            pinkId:{
                type:Number,
                default:0,
            },
            link_pay_uid:{
                type:Number,
                default:0
            },
            iswechat:{
                type:Boolean,
                default:false
            }
        },
        template:`<div>
                        <div class="payment" :class="payment?'':'up'">
                            <div class="title"><span class="iconfont icon-guanbi1" @click="close"></span>付款详情</div>
                            <div class="total acea-row row-between-wrapper">
                                <div>支付总额</div>
                                <div class="money">¥ {{money}}</div>
                            </div>
                            <div class="mode">支付方式</div>
                            <div class="select-btn" style="height: auto;">
                                <div class="checkbox-wrapper" v-show="iswechat" @click=" payType='weixin' "><label class="well-check"><input @click=" payType='weixin' " type="radio" name="payType" value="weixin" :checked="payType=='weixin'? true: false "> <i class="icon"></i><span class="iconfont icon-weixinzhifu"></span><span class="sex">微信支付</span></label></div>
                                <div class="checkbox-wrapper" @click=" payType='zhifubao' "><label class="well-check"><input @click=" payType='zhifubao' " type="radio" name="payType"  value="yue" :checked="payType=='zhifubao'? true: false "> <i class="icon"></i><span class="iconfont icon-umidd17" style="color: #00A0E9"></span><span class="sex">支付宝</span></label></div>
                                <div class="checkbox-wrapper" @click=" payType='yue' "><label class="well-check"><input @click=" payType='yue' " type="radio" name="payType"  value="yue" :checked="payType=='yue'? true: false "> <i class="icon"></i><span class="iconfont icon-qiandai" style="color: #FC992C"></span><span class="sex">余额</span></label></div>
                            </div>
                            <div class="payBnt" @click="goPay">确认支付</div>
                        </div>
                        <div class="mask" @touchmove.prevent :hidden="payment"></div>
                    </div>`,
        data:function () {
            return {
                payType:'yue',
            }
        },
        mounted:function(){
        },
        methods:{
            close:function (){
                this.$emit("change",{action:'payClose',value:true})//$emit():注册事件；
            },
            goPay:function () {
                var that=this;
                $h.loadFFF();
                app.baseGet($h.U({
                    c:'special',
                    a:'create_order',
                    q:{
                        special_id:this.special_id,
                        pay_type_num:this.pay_type_num,
                        payType:that.payType,
                        pinkId:this.pinkId,
                        link_pay_uid:this.link_pay_uid
                    }
                }),function (res) {
                    $h.loadClear();
                    res.data.data.msg=res.data.msg;
                    that.$emit('change',{action:'pay_order',value:res.data.data});
                },function () {
                    $h.loadClear();
                });
            }
        }
    });
})