define([
    'vue',
    'helper',
    'store',
    'text!wap/first/zsff/components/payment/index.html',
    'css!wap/first/zsff/components/payment/index.css'
], function(Vue, $h, $http, html) {
    'use strict';
    Vue.component('payment', {
        props: {
            payment: {
                type: Boolean,
                default: false
            },
            money: {
                type: String,
                default: 0
            },
            now_money: {
                type: String,
                default: 0
            },
            special_id: {
                type: Number,
                default: 0,
            },
            pay_type_num: {
                type: Number,
                default: -1,
            },
            pinkId: {
                type: Number,
                default: 0,
            },
            link_pay_uid: {
                type: Number,
                default: 0
            },
            isWechat: {
                type: Boolean,
                default: false
            },
            isAlipay: {
                type: Boolean,
                default: false
            },
            isBalance: {
                type: Boolean,
                default: false
            },
            signs: {
                type: Object,
                default: function () {
                    return {};
                }
            },
            templateId: {
                type: String,
                default: ''
            }
        },
        data: function () {
            return {
                payType: '',
                isIOS: false,
            };
        },
        template: html,
        created: function () {
            if (navigator.userAgent.indexOf('iPhone') > -1) {
                this.isIOS = false;
            }
            if (this.isIOS) {
                this.payType = this.isAlipay ? 'zhifubao' : 'yue';
            } else {
                this.payType = this.isWechat ? 'weixin' : (this.isAlipay ? 'zhifubao' : 'yue');
            }
        },
        methods: {
            onPay: function () {
                if (!this.isAlipay && !this.isWechat && !this.isBalance) {
                    this.$emit('change', {
                        action: 'payClose',
                        value: true
                    });
                    return $h.showMsg('支付未开启');
                }

                var data = {
                    special_id: this.special_id,
                    pay_type_num: this.pay_type_num,
                    payType: this.payType,
                    pinkId: this.pinkId,
                    link_pay_uid: this.link_pay_uid
                };

                Object.assign(data, this.signs);

                $h.loadFFF();
                $http.basePost($h.U({
                    c: 'special',
                    a: 'create_order'
                }), data, function (res) {
                    $h.loadClear();
                    res.data.data.msg = res.data.msg;
                    this.$emit('change', {
                        action: 'pay_order',
                        value: res.data.data
                    });
                }.bind(this), function () {
                    $h.loadClear();
                    this.$emit('change', {
                        action: 'payClose',
                        value: true
                    });
                }.bind(this));
            },
            onClose: function () {
                this.$emit('change', {
                    action: 'payClose',
                    value: true
                });
            },
            onSuccess: function (event) {
                if (event.detail.errMsg === 'subscribe:ok') {
                    this.onPay();
                }
            },
            onError: function (event) {
                $h.pushMsgOnce('订阅通知模板ID错误', function () {
                    this.onPay();
                }.bind(this));
            }
        }
    });
});
