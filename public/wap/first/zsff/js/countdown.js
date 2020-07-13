define(['vue'], function (Vue) {
    'use strict';
    Vue.component('count-down', {
        props: {
            start: {
                type: String,
                default: '倒计时：'
            },
            dayUnit: {
                type: String,
                default: '日'
            },
            horUnit: {
                type: String,
                default: '时'
            },
            minUnit: {
                type: String,
                default: '分'
            },
            secUnit: {
                type: String,
                default: '秒'
            },
            time: {
                type: Number,
                default: 0
            },
            dayShow: {
                type: Boolean,
                default: true
            },
            secShow: {
                type: Boolean,
                default: true
            }
        },
        template: '<div class="time">{{start}}' +
            '<span v-if="dayShow" class="styleAll">{{day}}{{dayUnit}}</span>' +
            '<span class="styleAll">{{hour}}{{horUnit}}</span>' +
            '<span class="styleAll">{{minute}}{{minUnit}}</span>' +
            '<span v-if="secShow" class="styleAll">{{second}}{{secUnit}}</span>' +
            '</div>',
        data: function () {
            return {
                day: "00",
                hour: "00",
                minute: "00",
                second: "00"
            }
        },
        mounted: function () {
            this.show_time();
        },
        methods: {
            show_time: function () {
                var that = this;
                function runTime() {//时间函数
                    var intDiff = that.time - Date.parse(new Date()) / 1000;//获取数据中的时间戳的时间差；
                    var day = 0, hour = 0, minute = 0, second = 0;
                    if (intDiff > 0) {//转换时间
                        if (that.isDay == true) {
                            day = Math.floor(intDiff / (60 * 60 * 24));
                        } else {
                            day = 0;
                        }
                        hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
                        minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
                        second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
                        if (hour <= 9) hour = '0' + hour;
                        if (minute <= 9) minute = '0' + minute;
                        if (second <= 9) second = '0' + second;
                        that.day = day;
                        that.hour = hour;
                        that.minute = minute;
                        that.second = second;
                    } else {
                        that.day = "00";
                        that.hour = "00";
                        that.minute = "00";
                        that.second = "00";
                    }
                }
                runTime();
                var timer = setInterval(runTime, 1000);
            }
        }
    });
});

