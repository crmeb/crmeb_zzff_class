<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/head"}
    <title>{$title|default=''}</title>
    <script src="{__PLUG_PATH}jquery-1.10.2.min.js"></script>
    <script src="{__PLUG_PATH}reg-verify.js"></script>
    <script src="{__PLUG_PATH}city.js"></script>
</head>
<body>
<div id="form-add" class="mp-form" v-cloak="">
    <i-Form :model="formData" :label-width="80">
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
        <Form-Item label="银行卡开户行">
            <i-input v-model="formData.bank" placeholder="请输入银行卡开户行"></i-input>
        </Form-Item>
        <Form-Item label="银行卡卡号">
            <i-input v-model="formData.bank_number" placeholder="请输入银行卡卡号"></i-input>
        </Form-Item>
        <Form-Item label="银行卡持卡人姓名">
            <i-input v-model="formData.bank_name" placeholder="请输入银行卡持卡人姓名"></i-input>
        </Form-Item>
        <Form-Item label="银行卡开户行地址">
            <i-input v-model="formData.bank_address" placeholder="请输入银行卡开户行地址"></i-input>
        </Form-Item>
        <Form-Item label="联系地址">
            <i-input v-model="formData.mer_address" placeholder="请输入联系地址"></i-input>
        </Form-Item>
        <Form-Item label="省市区">
            <Cascader :data="city" v-model="formData.cityid" @on-change="selectEnd" ></Cascader>
        </Form-Item>
        <Form-Item label="备注">
            <i-input type="textarea" v-model="formData.mark" placeholder="请输入备注"></i-input>
        </Form-Item>
        <Form-Item label="商户状态" >
            <Radio-Group v-model="formData.status">
                <Radio label="1">开启</Radio>
                <Radio label="0">关闭</Radio>
            </Radio-Group>
        </Form-Item>
        <Form-Item label="产品审核">
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
    var role = <?php echo $roles; ?> || {};
    var menus = <?php echo $menus; ?> || [];
    mpFrame.start(function(Vue){
        new Vue({
            el:'#form-add',
            data:{
                formData:{
                    mer_name:role.mer_name || '',
                    real_name:role.real_name || '',
                    mer_phone:role.mer_phone || '',
                    mer_email:role.mer_email || '',
                    mer_address:role.mer_address || '',
                    bank:role.bank || '',
                    bank_number:role.bank_number || '',
                    bank_name:role.bank_name || '',
                    bank_address:role.bank_address || '',
                    mark:role.mark || '',
                    status:role.status || 0,
                    cityid:role.cityid == '' ? [] : JSON.parse(role.cityid),
                    cityName: role.pro+','+role.city+','+role.area || '',
                    is_audit:role.is_audit || 0,
                    checked_menus:role.rules
                },
                menus:[],
                loading:false,
                city: city
            },
            methods:{
                selectEnd:function (value,selectedData) {
                  var t= this;
                  t.formData.cityName = '';
                  $.each(selectedData,function (index,item) {
                      if(!t.formData.cityName)
                            t.formData.cityName = item.label;
                      else
                          t.formData.cityName = t.formData.cityName +","+item.label;
                  })
                },
                tidyRes:function(){
                    var data = [];
                    menus.map((menu)=>{
                        data.push(this.initMenu(menu));
                    });
                    this.$set(this,'menus',data);
                },
                initMenu:function(menu){
                    var data = {},checkMenus = ','+this.formData.checked_menus+',';
                    data.title = menu.menu_name;
                    data.id = menu.id;
                    if(menu.child && menu.child.length >0){
                        data.children = [];
                        menu.child.map((child)=>{
                            data.children.push(this.initMenu(child));
                        })
                    }else{
                        data.checked = checkMenus.indexOf(String(','+data.id+',')) !== -1;
                        data.expand = !data.checked;
                    }
                    return data;
                },
                submit:function(){
                    this.formData.checked_menus = [];
                    this.$refs.tree.getCheckedNodes().map((node)=>{
                        this.formData.checked_menus.push(node.id);
                    });
                    if(this.formData.mer_phone){
                        if(!$reg.isPhone(this.formData.mer_phone)){
                            return $eb.message('error','请输入正确的手机号');
                        }}
                    if(!this.formData.cityName)
                        return $eb.message('error','请选择省市区');
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
