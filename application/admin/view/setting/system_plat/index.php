{extend name="public/container"}
{block name="head_top"}
<style scoped lang="scss">
    /*$cursor: #1890ff;*/
    .content {
        justify-content: space-between;
        padding:10px;
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-color: #e6e6e6;
    }

    .login-out {
        cursor:pointer;
    }

    .rR {
        text-align: center;
        font-size: 22px;
        /*display: block;*/
    }

    .dashboard-workplace-header-tip {
        display: inline-block;
        vertical-align: middle;
    }

    .dashboard-workplace-header-tip-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .dashboard-workplace-header-tip-desc {
        /*line-height: 0 !important;*/
        display: block;

    span {
        font-size: 12px;
        color: $ cursor;
        cursor: pointer;
        display: inline-block;
    }

    }
    .dashboard-workplace-header-extra {
        width: auto !important;
        min-width: 300px;
    }

    .pfont {
        font-size: 12px;
        color: #808695;
    }

    .circleUrl {
        width: 50px;
        height: 50px;
    }

    .circleUrl img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
    }

    .mr20 {
        margin-right: 20px;
    }

    .acea-row {
        display: -webkit-box;
        display: -moz-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-lines: multiple;
        -moz-box-lines: multiple;
        -o-box-lines: multiple;
        -webkit-flex-wrap: wrap;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        /* 辅助类 */
    }

    .acea-row.row-middle {
        -webkit-box-align: center;
        -moz-box-align: center;
        -o-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }

    .pfontBox {
        width: 93%;
    }

    .pfontImg {
        width: 28px;
        height: 28px;
        margin-right: 5px;
    }

    .pfontImg img {
        width: 100%;
        height: 100%;
        overflow: hidden;
        border-radius: 50%;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <!--列表-->
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">

                <div class="content acea-row row-middle">
                    <div class="demo-basic--circle acea-row row-middle">
                        <!--                <Avatar :size="50" :src="circleUrl" class="mr20"/>-->
                        <!--                <Avatar icon="ios-person" size="large" />-->
                        <div class="circleUrl mr20"><img src="/system/images/plat_user.png"></div>
                        <div class="dashboard-workplace-header-tip">
                            <div class="dashboard-workplace-header-tip-title">{$info['phone']}，祝您每一天开心！</div>

                            <div class="dashboard-workplace-header-tip-desc">
                                <span class="mr10"><a href="{:Url('setting.systemPlat/modify')}">修改密码</a></span>
                                <span @click="loginOut" class="login-out"><a>退出登录</a></span>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-workplace-header-extra acea-row">
                        <div class="pfontBox">
                            <span class="pfont acea-row row-middle">
                              <div class="pfontImg"><img src="/system/images/plat_sms.png"><Icon type="md-apps"></Icon></div>
                              <span>短信剩余条数</span>
                            </span>

                            <?php if (isset($info['sms']['open']) && $info['sms']['open'] == 1) { ?>
                               <div>剩余
                                   <span class="rR" v-text="numbers">{$info['sms']['num']} </span>条
                                   已使用 <span class="rR" v-text="numbers">{$info['sms']['surp']} </span>条
                               </div>

                            <?php } else { ?>
                                <span><a href="{:Url('setting.systemPlat/sms_open')}">开通服务</a></span>
                            <?php } ?>

                        </div>
                    </div>
                </div>
                <div class="layui-tab layui-tab-brief" lay-filter="tab">
                    <ul class="layui-tab-title">
                        <li lay-id="list" {eq name='type' value='sms' } class="layui-this" {/eq}>
                        <a href="{eq name='type' value='sms'}javascript:;{else}{:Url('index',['type'=>'sms'])}{/eq}">短信</a>
                        </li>
                    </ul>
                </div>
                <div class="layui-card-body">
                    <div class="layui-btn-container" id="container-action">
                        <?php if (isset($info['sms']['open']) && $info['sms']['open'] == 1) { ?>
                            <span class="layui-btn-sm" style="margin-right:10px;font-size: 18px;color: #333; ">短信签名：{$info.sms.sign}</span>
                            <button class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame('修改签名','{:Url('sms_modify')}',{h:540,w:600})" type="button">修改签名</button>
                        <?php } ?>
                        <button type="button" class="layui-btn layui-btn-sm" onclick="window.location.reload()">刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>

                </div>
            </div>
        </div>
    </div>

    <!--end-->
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    var type = "<?php echo $type;?>";
    layList.tableList('List', "{:Url('record',['type'=>$type])}", function () {
        if(type == 'sms'){
            return [
                {field: 'phone', title: '手机号码', width: '10%', align: 'center'},
                {field: 'content', title: '短信内容', height: 'full-20'},
                {field: '_resultcode', title: '发送状态', width: '8%', align: 'center'},
                {field: 'add_time', title: '发送时间', align: 'center'},
            ];
        }
    });
    $(document).on('click', ".open_image", function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    });
    $(document).on('click', ".login-out", function (e) {
        $eb.axios.post("{:Url('loginOut')}",).then(function (res) {
            if (res.status == 200 && res.data.code == 200) {
                $eb.message('success', res.data.msg || '退出成功!');
                $eb.closeModalFrame(window.name);
                window.location.href = "{:url('setting.systemPlat/index?out=1')}";
            } else {
                $eb.message('error', res.data.msg || '请求失败!');
            }
        }).catch(function (err) {
            $eb.message('error', err);
        })
    });
    //自定义方法
    var action={
        //打开新添加页面
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['50%', '50%']
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
    };
    require(['vue'], function (Vue) {
        new Vue({
            el: "#app",
            data: {},
            watch: {},
            methods: {},
            mounted: function () {

            }
        })
    });
</script>
{/block}