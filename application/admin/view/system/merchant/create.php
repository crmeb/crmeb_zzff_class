<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/head"}
    <title>{$title|default=''}</title>
    <script src="{__PLUG_PATH}jquery-1.10.2.min.js"></script>
    <script src="{__PLUG_PATH}reg-verify.js"></script>
</head>
<body>
<div id="form-add" class="mp-form" v-cloak="">
    <i-Form :model="formData" :label-width="120">
        <Form-Item label="商户账号">
            <i-input v-model="formData.account" placeholder="请输入商户账号"></i-input>
        </Form-Item>
        <Form-Item label="绑定微信用户ID">
            <i-input v-model="formData.uid" placeholder="请输入绑定的用户ID"></i-input>
        </Form-Item>
        <Form-Item label="商户密码(默认:666666)">
            <i-input type="password" v-model="formData.pwd" placeholder="请输入商户密码"></i-input>
        </Form-Item>
        <Form-Item label="确认密码">
            <i-input type="password" v-model="formData.conf_pwd" placeholder="请输入确认密码"></i-input>
        </Form-Item>
        <Form-Item label="商户名称">
            <i-input v-model="formData.mer_name" placeholder="请输入商户名称"></i-input>
        </Form-Item>
        <Form-Item label="联系人">
            <i-input v-model="formData.real_name" placeholder="请输入联系人"></i-input>
        </Form-Item>
        <Form-Item label="联系电话">
            <i-input v-model="formData.mer_phone" placeholder="请输入联系电话"></i-input>
        </Form-Item>
        <Form-Item label="联系邮箱">
            <i-input v-model="formData.mer_email" placeholder="请输入联系邮箱"></i-input>
        </Form-Item>
        <Form-Item label="联系地址">
            <i-input v-model="formData.mer_address" placeholder="请输入联系地址"></i-input>
        </Form-Item>
        <Form-Item label="备注">
            <i-input type="textarea" v-model="formData.mark" placeholder="请输入备注"></i-input>
        </Form-Item>
        <Form-Item label="状态">
            <Radio-Group v-model="formData.status">
                <Radio label="1">开启</Radio>
                <Radio label="0">关闭</Radio>
            </Radio-Group>
        </Form-Item>
        <Form-Item label="审核状态">
            <Radio-Group v-model="formData.is_audit">
                <Radio label="1">开启</Radio>
                <Radio label="0">关闭</Radio>
            </Radio-Group>
        </Form-Item>
        <Form-Item label="可用权限">
            <Tree :data="menus" show-checkbox ref="tree"></Tree>
        </Form-Item>
        <Form-Item :class="'add-submit-item'">
            <i-Button :type="'primary'" :html-type="'submit'" :size="'large'" :long="true" :loading="loading" @click.prevent="submit">提交</i-Button>
        </Form-Item>
    </i-Form>
</div>

<script>
    $eb = parent._mpApi;
    var menus = <?php echo $menus; ?> || [];
    console.log(menus);
    mpFrame.start(function(Vue){
        new Vue({
            el:'#form-add',
            data:{
                formData:{
                    account:'',
                    pwd:'666666',
                    conf_pwd:'666666',
                    mer_name:'',
                    mer_phone:'',
                    mark:'',
                    mer_email:'',
                    real_name:'',
                    mer_address:'',
                    status:0,
                    is_audit:0,
                    checked_menus:[],
                    uid:''
                },
                menus:[],
                loading:false
            },
            methods:{
                tidyRes:function(){
                    var data = [];
                    menus.map((menu)=>{
                        data.push(this.initMenu(menu));
                });
                    this.$set(this,'menus',data);
                },
                initMenu:function(menu){
                    var data = {};
                    data.title = menu.menu_name;
                    data.id = menu.id;
                    data.expand = false;
                    if(menu.child && menu.child.length >0){
                        data.children = [];
                        menu.child.map((child)=>{
                            data.children.push(this.initMenu(child));
                    })
                    }
                    return data;
                },
                submit:function(){
                    this.formData.checked_menus = [];
                    this.$refs.tree.getCheckedNodes().map((node)=>{
                        this.formData.checked_menus.push(node.id);
                    });
                    if($reg.isEmpty(this.formData.uid)){
                        return $eb.message('error','请输入绑定的用户ID');
                    }
                    if(this.formData.mer_phone){
                        if(!$reg.isPhone(this.formData.mer_phone)){
                        return $eb.message('error','请输入正确的手机号');
                    }}
                    if(this.formData.mer_email){
                        if(!$reg.isEmail(this.formData.mer_email)){
                          return $eb.message('error','请输入正确的邮箱');
                        }
                    }
                    this.loading = true;
                    $eb.axios.post("{$action}",this.formData).then((res)=>{
                        if(res.status && res.data.code == 200)
                    return Promise.resolve(res.data);
                    else
                    return Promise.reject(res.data.msg || '添加失败,请稍候再试!');
                }).then((res)=>{
                        $eb.message('success',res.msg || '操作成功!');
                    $eb.closeModalFrame(window.name);
                }).catch((err)=>{
                        this.loading=false;
                    $eb.message('error',err);
                });
                }
            },
            mounted:function(){
                t = this;
                this.tidyRes();
            }
        });
    });
</script>
</body>
