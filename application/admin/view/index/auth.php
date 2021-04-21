{extend name="public/container"}
{block name='head'}
<style>
    [v-cloak] {
        display: none;
    }

    .layui-card-header {
        font-weight: bold;
        font-size: 16px;
    }

    .layui-card-header .layui-icon-auz {
        margin-right: 5px;
        vertical-align: bottom;
    }

    .layui-card-body {
        font-size: 16px;
        line-height: 1.6;
        color: #515a6e;
    }

    .layui-card-body img {
        display: block;
        width: 100%;
        margin-bottom: 30px;
        pointer-events: none;
    }

    .button-section {
        margin-top: 25px;
        text-align: center;
    }

    .banner {
        padding-top: 50px;
        padding-bottom: 50px;
        background: url("{__FRAME_PATH}img/auth.jpg") center/cover no-repeat;
    }

    .banner .title {
        font-weight: bold;
        font-size: 32px;
        text-align: center;
        color: #fff;
    }

    .banner .info {
        margin-top: 25px;
        font-size: 16px;
        text-align: center;
        color: #fff;
    }

    .auth-section {
        text-align: center;
    }

    .auth-content {
        display: inline-block;
        padding: 60px 0 10px;
        background: url("{__FRAME_PATH}img/auth-icon.png") center 10px/26px 40px no-repeat;
        text-align: left;
    }

    .auth-content div ~ div {
        margin-top: 10px;
    }

    .auth-content span {
        color: #868686;
    }
</style>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header"><i class="layui-icon layui-icon-auz"></i>商业授权</div>
                <div class="layui-card-body">
                    <div class="banner">
                        <div class="title">商业使用授权证书，保护您的合法权益</div>
                        <div class="info">您的支持是我们不断进步的动力，商业授权更多是一个保障和附加的增值服务，让您优先享受新版本的强大功能和安全保障</div>
                    </div>
                    <div class="auth-section">
                        <div class="auth-content">
                            <div><span>授权状态：</span>{{ status ? '授权通过' : statusText }}</div>
                            <div><span>授权期限：</span>{{ authTime ? authTime + '天' : '永久' }}</div>
                            <div><span>授权码：</span>{{ authCode }}</div>
                        </div>
                    </div>
                    <div class="button-section">
                        <button type="button" class="layui-btn layui-btn-normal" @click="goQuery">查询授权</button>
                        <button type="button" class="layui-btn layui-btn-normal" @click="goAuth">获取授权</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    require(['vue', 'axios'], function (Vue, axios) {
        new Vue({
            el: "#app",
            data: {
                status: false,
                statusText: '---',
                authTime: '---',
                authCode: '---',
                errorCode: '---'
            },
            created: function () {
                this.init();
            },
            methods: {
                init: function () {
                    Promise.all([this.checkAuth(), this.authInfo()]).then(function (res) {
                        if (res[0].data.code === 200) {
                            if (res[0].data.data.status === 1) {
                                this.status = true;
                            }
                        } else {

                        }
                        if (res[1].data.code === 200) {
                            this.statusText = res[1].data.data.msg;
                            this.authTime = res[1].data.data.day;
                            this.authCode = res[1].data.data.authCode;
                        } else {

                        }
                    }.bind(this));
                },
                checkAuth: function () {
                    return axios.get("{:Url('check_auth')}");
                },
                authInfo: function () {
                    return axios.get("{:Url('auth_data')}");
                },
                goAuth: function (params) {
                    window.open('http://www.crmeb.com/web/auth/apply.html');
                },
                goQuery: function () {
                    window.open('http://www.crmeb.com/web/auth/query.html');
                }
            }
        });
    });
</script>
{/block}
