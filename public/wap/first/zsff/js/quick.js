define(['vue', 'helper', 'store'], function (Vue, $h, api) {
    'use strict';
    Vue.component('quick-menu', {
        data: function () {
            return {
                top: '50%',
                open: false,
                menuList: []
            };
        },
        created: function () {
            this.onReady();
        },
        methods: {
            onReady: function() {
                var that = this;
                api.baseGet($h.U({
                    c: 'auth_api',
                    a: 'suspensionButton'
                }), function(res) {
                    var data = res.data.data;
                    that.menuList = data;
                }, function(err) {
                    console.error(err.data.msg);
                });
            },
            onMove: function(event) {
                var clientHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight,
                    tithe = clientHeight / 10,
                    min = tithe * 2,
                    max = tithe * 8,
                    top = 0;
                if (min >= event.touches[0].clientY) {
                    top = clientHeight / 10 * 2;
                } else if (event.touches[0].clientY >= max) {
                    top = clientHeight / 10 * 8;
                } else {
                    top = event.touches[0].clientY;
                }
                this.top = top + 'px';
            }
        },
        template: '<div v-if="menuList.length" :style="{ top: top }" class="quick" @touchmove.stop.prevent="onMove">' +
                '<div v-show="open" class="menu">' +
                '<a v-for="item in menuList" :key="item.id" :href="item.url">' +
                '<img :src="item.icon" :alt="item.name">' +
                '</a>' +
                '</div>' +
                '<div class="main" @click="open = !open">' +
                '<img :src="open ? \'/wap/first/zsff/images/quick_open.gif\' : \'/wap/first/zsff/images/quick_close.gif\'">' +
                '</div>' +
                '</div>'
    });
});