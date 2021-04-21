{extend name="public/container"}
{block name="title"}{$title|default=''}{/block}
{block name="head_top"}
    <link href="/system/frame/css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <link href="/system/frame/css/style.min.css?v=3.0.0" rel="stylesheet">
    <style>
        .check {
            color: #f00
        }

        .demo-upload {
            display: block;
            height: 33px;
            text-align: center;
            border: 1px solid transparent;
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
            position: relative;
            box-shadow: 0 1px 1px rgba(0, 0, 0, .2);
            margin-right: 4px;
        }

        .demo-upload img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .demo-upload-cover {
            display: none;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, .6);
        }

        .demo-upload:hover .demo-upload-cover {
            display: block;
        }

        .demo-upload-cover i {
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            margin: 0 2px;
        }

        .code-send {
            cursor: pointer;
        }
        .form-title {
            padding: 93px 0 32px;
            font-size: 22px;
            text-align: center;
            color: #1890FF;
        }
        .layui-form-item {
            text-align: center;
        }
        .layui-form-item .layui-inline {
            margin-bottom: 28px;
            margin-right: 0;
        }
        .layui-form-pane .layui-form-label {
            width: 90px;
            height: 36px;
            margin-bottom: 0;
        }
        .layui-form-item .layui-input-inline {
            width: 261px;
            margin-right: 0;
        }
        .layui-form-pane .layui-input {
            height: 36px;
        }
        .layui-btn {
            width: 350px;
            background-color: #1890FF;
        }
        .layui-form-item .layui-inline a {
            padding-left: 10px;
            padding-right: 10px;
            font-size: 14px;
            color: #1890FF;
        }
        .layui-form-item .layui-inline a ~ a {
            border-left: 1px solid #aaa;
        }
    </style>
    <script>
        window.test = 1;
    </script>
    {/block}
    {block name="content"}
<div class="layui-row layui-col-space15">
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <div class="form-title">一号通账户注册</div>
                <form class="layui-form layui-form-pane" action="" lay-filter="example">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">账号</label>
                            <div class="layui-input-inline">
                                <input type="text" name="account" required  lay-verify="required" placeholder="请输入账号" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">密码</label>
                            <div class="layui-input-inline">
                                <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">手机号</label>
                            <div class="layui-input-inline">
                                <input type="text" name="phone" required  lay-verify="required" placeholder="请输入手机号" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <div class="layui-input-inline" style="width: 232px;">
                                <input type="text" name="verify_code" required  lay-verify="required" placeholder="请输入验证码" autocomplete="off" class="layui-input">
                            </div>
                            <label class="layui-form-label" style="width: 120px;margin-left: -1px;" id="send">发送验证码</label>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <button class="layui-btn" lay-submit lay-filter="*">注册</button>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <a href="{:Url('setting.systemPlat/index?out=1')}">立即登录</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
layui.use('form', function () {
    var form = layui.form;
    var lock = false;
    form.on('submit(*)', function (data) {
        $eb.axios.post("{:Url('go_register')}", data.field).then(function (res) {
            if (res.status == 200 && res.data.code == 200) {
                $eb.message('success', res.data.msg || '提交成功!');
                $eb.closeModalFrame(window.name);
                window.location.href = "{:url('setting.systemPlat/index')}";
            } else {
                $eb.message('error', res.data.msg || '请求失败!');
            }
        }).catch(function (err) {
            $eb.message('error', err);
        })
        return false;
    });
    layui.$('#send').on('click', function () {
        var that = this;
        var phone = layui.$('input[name="phone"]').val();
        if (lock) {
            return;
        }
        if (!phone) {
            return $eb.message('error', '请填写手机号');
        }
        if (!/^1[3456789]\d{9}$/.test(phone)) {
            return $eb.message('error', '手机号格式错误');
        }
        lock = true;
        $eb.axios.post("{:Url('verify')}", {phone: phone}).then(function (res) {
            if (res.data.code == 200) {
                var cd = 60;
                var timeClone = setInterval(function () {
                    cd--;
                    if (cd <= 0) {
                        $(that).html('重新发送');
                        clearInterval(timeClone);
                        lock = false;
                    } else {
                        $(that).html('剩余' + cd + 's');
                    }
                }, 1000);
                $eb.message('success', res.data.msg || '发送成功');
            } else {
                $eb.message('error', res.data.msg || '发送失败');
            }
            return false;
        }).catch(function (err) {
            lock = false;
            $eb.message('error', err);
        })
    })
})
</script>
{/block}