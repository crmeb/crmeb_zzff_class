{extend name="public/container"}
{block name="head_top"}
<style>
    .layui-input-block button {
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .layui-card-body {
        padding-left: 10px;
        padding-right: 10px;
    }

    .layui-card-body p.layuiadmin-big-font {
        font-size: 36px;
        color: #666;
        line-height: 36px;
        padding: 5px 0 10px;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-all;
        white-space: nowrap;
    }

    .layui-fluid .layui-row . {
        position: absolute;
        right: 15px;
    }

    .layuiadmin-badge {
        top: 50%;
        margin-top: -9px;
        color: #01AAED;
    }

    .layuiadmin-span-color i {
        padding-left: 5px;
    }

    .layui-card.card {
        border: 2px solid #F2F2F2;
        border-radius: 2%;
    }

    .layui-card.checkcard {
        border: 2px solid #F47822;
        border-radius: 5px;
    }

    .block-rigit button {
        width: 100px;
        letter-spacing: .5em;
        line-height: 28px;
    }

    .layui-form-item {
        margin-bottom: .5rem;
    }

    .layui-form-item .code {
        width: 15rem;
    }

    .layui-form-item .code img {
        width: 100%;
        height: 100%;
    }
</style>
<script src="{__PLUG_PATH}echarts.common.min.js"></script>
{/block}
{block name="content"}
<div class="layui-fluid" id="app" v-cloak="">
    <div class="layui-row layui-col-space15">
        <div class="layui-card">
            <div class="layui-card-header">
                购买服务
            </div>
            <div class="layui-tab layui-tab-brief" lay-filter="tab">
                <ul class="layui-tab-title">
                    <li lay-id="list" class="layui-this" @click="changeMealType('sms')">短信
                    </li>
                </ul>
            </div>
            <div class="layui-row">
                <div class="layui-card-body layui-col-space10">
                    <div class="layui-col-sm1 layui-col-md2" v-for="(item,index) in priceList"
                         @click="checkMeal(item,index)">
                        <div class="layui-card" :class="param == index ? 'checkcard' : 'card'">
                            <div class="layui-card-body" style="padding: 27px 15px 0;">
                                <p style="text-align: center;color: #F47822;font-size: 30px;line-height: 30px;">{{
                                    item.num }}条</p>
                            </div>
                            <div class="layui-card-header">
                                <p style="text-align: center;color: #C2C5BE;font-size: 16px;">￥{{ item.price }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-row layui-col-space15">
            <div class="layui-card" v-show="checked">
                <div class="layui-card-header">
                    立即支付
                </div>
                <div class="layui-card-body">
                    <div class="layui-row">
                        <form class="layui-form" action="">
                            <div class="layui-form-item">
                                <label class="layui-form-label">短信账户：</label>
                                <div class="layui-input-block">
                                    <span style="font-size: 24px;line-height: 33px;">{$info['account']}</span>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">剩余条数：</label>
                                <div class="layui-input-block">
                                    <span style="font-size: 24px;line-height: 33px;">{$info['sms']['num']} 条</span>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">支付方式：</label>
                                <div class="layui-input-block">
                                    <button type="button" class="layui-btn" :class="payType == 'weixin'? 'layui-btn-normal' : 'layui-btn-primary'" @click="changeType('weixin')">微信支付
                                    </button>
<!--                                    <button type="button" class="layui-btn" :class="payType == 'alipay'? 'layui-btn-normal' : 'layui-btn-primary'" @click="changeType('alipay')">支付宝-->
<!--                                    </button>-->
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">充值条数：</label>
                                <div class="layui-input-block">
                                    <span style="font-size: 24px;line-height: 33px;">{{ checked.num }} 条</span>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">支付金额：</label>
                                <div class="layui-input-block">
                                    <p style="line-height: 33px;font-size: 16px;color: #F47822;">￥{{ checked.price
                                        }}</p>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="code.code_show">
                                <div class="layui-input-block code" >
                                    <div id="qrcode" style="width:100px; height:100px; margin-top:15px;"></div>
                                </div>
                                <div class="layui-input-block">
                                    <span>
                                        支付码过期时间：{{code.invalid}}
                                    </span>
                                </div>
                            </div>
                            <div class="layui-form-item" v-else>
                                <span>您尚未开通短信服务，请先开启<a href="{:Url('setting.systemPlat/sms_open')}" style="color: rgb(244, 120, 34);font-size: 20px;">开通服务</a></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script src="{__ADMIN_PATH}js/qrcode.js"></script>
<script>
    require(['vue'], function (Vue) {
        new Vue({
            el: "#app",
            data: {
                type: 'sms',
                number: 0,
                send_total: 0,
                option: {},
                status: '',
                data: '',
                title: '全部商品',
                myChart: {},
                showtime: false,
                priceList: [],
                checked: false,
                param: '',
                payType: 'weixin',
                code: '',
                qrcode: ''
            },
            watch: {
                "param": function (newVal) {
                    let that = this;
                    that.checked = that.priceList[newVal];
                    that.param = newVal;
                    that.getCode();
                }
            },
            methods: {
                checkMeal: function (item, index) {
                    this.param = index;
                },
                changeMealType: function (val) {
                    if (val != this.type) {
                        this.type = val;
                        this.getLackList();
                    }
                },
                getLackList: function () {
                    var that = this;
                    var index = layList.layer.load(2, {shade: [0.3, '#fff']});
                    layList.baseGet(layList.Url({a: 'get_meal', p: {type: that.type}}), function (res) {
                        layList.layer.close(index);
                        that.priceList = res.data.data;
                        if (that.priceList.length > 0) {
                            that.checked = res.data[0];
                            that.param = 0;
                        }
                    }, function (err) {
                        layList.layer.close(index);
                    });
                },
                changeType: function (val) {
                    if (val != this.payType) {
                        this.payType = val;
                        this.getCode();
                    }
                },
                getCode: function () {
                    let that = this;
                    let index = layList.layer.load(2, {shade: [0.3, '#fff']});
                    layList.basePost(layList.Url({a: 'pay'}), {
                        type: that.checked.type,
                        num: that.checked.num,
                        price: that.checked.price,
                        meal_id: that.checked.id,
                        pay_type: that.payType
                    }, function (res) {
                        layList.layer.close(index);
                        that.code = res.data;
                        that.qrcode.makeCode(res.data.qr_code);
                    }, function (err) {
                        layList.layer.close(index);
                    });
                },

            },
            mounted: function () {
                this.getLackList();
                this.$nextTick(function () {
                    this.qrcode = new QRCode(document.getElementById("qrcode"), {
                        width : 100,
                        height : 100
                    });
                })
            }
        });
    })
</script>
{/block}
