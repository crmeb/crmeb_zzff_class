{extend name="public/container"}
{block name="head_top"}
<style>
    .col{color: red}
    .layui-form-item{margin-top: 10px}
    .layui-input-block{margin-left: 125px}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" >
        <div class="layui-col-md6" style="margin: 0 auto;float: none">
            <div class="layui-card">
                <div class="layui-card-header text-center">申请合伙人</div>
                <div class="layui-card-body">
                    <form class="layui-form" action="">
                        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                            <legend>用户信息</legend>
                        </fieldset>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="col">*</span>用户名</label>
                            <div class="layui-input-block">
                                <input type="text" name="user_name" onclick="getUser('user_name',1)" lay-verify="user_name" autocomplete="off" placeholder="请输入用户名" class="layui-input">
                                <input type="hidden" value="" name="uid">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="col">*</span>分享人用户名</label>
                            <div class="layui-input-block">
                                <input type="text" name="share_name" onclick="getUser('share_name',2)" lay-verify="share_name" autocomplete="off" placeholder="请输入分享人用户名" class="layui-input">
                                <input type="hidden" value="" name="share_uid">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="col">*</span>店长用户名</label>
                            <div class="layui-input-block">
                                <input type="text" name="shop_name" onclick="getUser('shop_name',3)" lay-verify="shop_name" autocomplete="off" placeholder="请输入店长用户名" class="layui-input">
                                <input type="hidden" value="" name="shop_uid">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="col">*</span>运营中心编号</label>
                            <div class="layui-input-block">
                                <input type="text" name="code" lay-verify="code" autocomplete="off" placeholder="请输入运营中心编号" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="col">*</span>登录密码</label>
                            <div class="layui-input-block">
                                <input type="password" name="password" lay-verify="password" autocomplete="off" placeholder="请输入登录密码" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="col">*</span>确认登录密码</label>
                            <div class="layui-input-block">
                                <input type="password" name="password_ok" lay-verify="password_ok" autocomplete="off" placeholder="请输入确认登录密码" class="layui-input">
                            </div>
                        </div>
                        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                            <legend>基本信息</legend>
                        </fieldset>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="col">*</span>手机号</label>
                            <div class="layui-input-block">
                                <input type="text" name="phone" lay-verify="phone" autocomplete="off" placeholder="请输入手机号" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="col">*</span>姓名</label>
                            <div class="layui-input-block">
                                <input type="text" name="full_name" lay-verify="full_name" autocomplete="off" placeholder="请输入姓名" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">性别</label>
                            <div class="layui-input-block">
                                <input type="radio" name="sex" value="0" title="男" checked="">
                                <input type="radio" name="sex" value="1" title="女">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span class="col">*</span>身份证号</label>
                            <div class="layui-input-block">
                                <input type="text" name="card_id" lay-verify="card_id" autocomplete="off" placeholder="请输入身份证号" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn" lay-submit="" lay-filter="save">立即提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    window.getUser=function(name_type,type){
        layList.createModalFrame('用户列表',layList.U({
            a:'user_list',q:{
                name:name_type,
                type:type
            }
        }),{w:1000});
    }
    window.setInputValue=function(name_type,nickname,uid){
        var input=$('input[name="'+name_type+'"]');
        if(!input) layList.msg('未查到此元素');
        input.val(nickname);
        input.next().val(uid);
    }
    require(['reg-verify'],function($reg) {

        layList.form.render();

        layList.search('save',function (data) {
            if(!data.user_name) return layList.msg('请输入用户名');
            if(!data.share_name) return layList.msg('请输入分享人用户名');
            if(!data.shop_name) return layList.msg('请输入店长用户名');
            if(!data.code) return layList.msg('请输入运营中心编号');
            if(!data.password) return layList.msg('请输入登录密码!');
            if(!data.password_ok) return layList.msg('请输入确认登录密码!');
            if(data.password_ok!=data.password) return layList.msg('两次输入的密码不同');
            if(!data.phone) return layList.msg('请输入手机号码');
            if(!$reg.isPhone(data.phone)) return layList.msg('请输入正确的手机号码');
            if(!data.full_name) return layList.msg('请输入姓名');
            if(!data.card_id) return layList.msg('请输入身份证号码!');
            if(!$reg.isCard(data.card_id)) return layList.msg('请输入正确的身份证号码!');
            layList.basePost(layList.U({a:'save_group_v1'}),data,function (res) {
                layList.msg(res.msg,function () {
                    window.location.reload();
                });
            },function (res) {
                layList.msg(res.msg);
            });
        })
    })
</script>
{/block}