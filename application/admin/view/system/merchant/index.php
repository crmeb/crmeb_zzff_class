{extend name="public/container"}
{block name="content"}
<div class="row empty_margin">
    <div class="col-sm-12">
        <div class="ibox">
<!--            <div class="ibox-title">-->
<!--                <button type="button" class="btn btn-w-m btn-primary" onclick="$eb.createModalFrame(this.innerText,'{:Url('create')}')">添加商户</button>-->
<!--            </div>-->
            <div class="ibox-content">
                <div class="row">
                    <div class="m-b m-l">
                        <?php /*  <form action="" class="form-inline">
                              <i class="fa fa-search" style="margin-right: 10px;"></i>
                              <select name="is_show" aria-controls="editable" class="form-control input-sm">
                                  <option value="">是否显示</option>
                                  <option value="1" {eq name="params.is_show" value="1"}selected="selected"{/eq}>显示</option>
                                  <option value="0" {eq name="params.is_show" value="0"}selected="selected"{/eq}>不显示</option>
                              </select>
                              <select name="access" aria-controls="editable" class="form-control input-sm">
                                  <option value="">子管理员是否可用</option>
                                  <option value="1" {eq name="params.access" value="1"}selected="selected"{/eq}>可用</option>
                                  <option value="0" {eq name="params.access" value="0"}selected="selected"{/eq}>不可用</option>
                              </select>
                          <div class="input-group">
                              <input type="text" name="keyword" value="{$params.keyword}" placeholder="请输入关键词" class="input-sm form-control"> <span class="input-group-btn">
                                      <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                          </div>
                          </form>  */ ?>
                        <form action="" class="form-inline">
                            <select name="status" aria-controls="editable" class="form-control input-sm">
                                <option value="">状态</option>
                                <option value="1" {eq name="where.status" value="1"}selected="selected"{/eq}>开启</option>
                                <option value="0" {eq name="where.status" value="0"}selected="selected"{/eq}>关闭</option>
                            </select>
                            <div class="input-group">
                                <input type="text" name="mer_name" value="{$where.mer_name}" placeholder="请输入商户姓名和联系人" class="input-sm form-control" size="26"> <span class="input-group-btn">
                                   <button type="submit" class="btn btn-sm btn-primary"> <i class="fa fa-search" ></i>搜索</button> </span>
                            </div>
                        </form>
                    </div>

                </div>
                <div class="table-responsive"  style="overflow:visible">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center" >编号</th>
                            <th class="text-center" >商户名称</th>
                            <th class="text-center" >联系人</th>
                            <th class="text-center" >联系电话</th>
                            <th class="text-center" >商户状态</th>
                            <th class="text-center" >店铺状态</th>
                            <th class="text-center">备注</th>
                            <th class="text-center" width="18%">操作</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        {volist name="list" id="vo"}
                        <tr>
                            <td class="text-center">
                                {$vo.id}
                            </td>
                            <td class="text-center">
                                {$vo.mer_name}
                            </td>
                            <td class="text-center">
                                {$vo.real_name}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['mer_phone']"}
                                {$vo.mer_phone}
                                {else/}
                                无
                                {/if}
                            </td>
                            <td class="text-center">
                                {eq name='vo.status' value='1'}
                                <span class="modify" data-url="{:Url('modify',array('id'=>$vo['id'],'status'=>0))}" style="color:#00f;cursor: pointer">[正常]</span>
                                {else/}
                                <span class="modify" data-url="{:Url('modify',array('id'=>$vo['id'],'status'=>1))}" style="color:#f00;cursor: pointer">[锁定]</span>
                                {/eq}
                            </td>
                            <td class="text-center">
                                <i class="fa {eq name='vo.estate' value='1'}fa-check text-navy{else/}fa-close text-danger{/eq}"></i>
                            </td>
                            <td class="text-center">
                                {$vo.mark}
                            </td>
                            <td class="text-center">
                                {if $vo.status==-1}
                                <button class="layui-btn layui-btn-normal layui-btn-sm verify" data-url="{:Url('verify',array('id'=>$vo['id']))}">审核</button>
                                <button class="layui-btn layui-btn-danger layui-btn-sm verify_no" data-url="{:Url('verify_no',array('id'=>$vo['id']))}">拒绝审核</button>
                                {else}
                                <a class="layui-btn layui-btn-normal layui-btn-sm" target="_blank" href="{:url('login',array('id'=>$vo['id']))}">访问</a>
                                <button class="layui-btn layui-btn-primary layui-btn-sm" onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['id']))}')">编辑</button>
                                <button class="layui-btn layui-btn-warm success layui-btn-sm" data-url="{:Url('reset_pwd',array('id'=>$vo['id']))}">重置密码</button>
                                <button class="layui-btn layui-btn-danger layui-btn-sm warning" data-url="{:Url('delete',array('id'=>$vo['id']))}">删除</button>
                                {/if}
                            </td>
                        </tr>
                        {/volist}
                        </tbody>
                    </table>
                </div>
                {include file="public/inner_page"}
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    $('.warning').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                    _this.parents('tr').remove();
                }else
                    return Promise.reject(res.data.msg || '删除失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        })
    });
    $('.verify').on('click',function () {
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    window.location.reload();
                    $eb.$swal('success',res.data.msg);
                }else
                    return Promise.reject(res.data.msg || '修改失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        },{'title':'您确定审核通过吗？','text':'请谨慎操作！','confirm':'确认,审核通过'})
    })
    $('.verify_no').on('click',function () {
        var _this = $(this),url =_this.data('url');
        $eb.$alert('textarea',{title:'请输入审核未通过原因',value:'信息不完整!'},function (result) {
            if(result){
                $.ajax({
                    url:url,
                    data:{remark:result},
                    type:'post',
                    dataType:'json',
                    success:function (res) {
                        if(res.code == 200) {
                            window.location.reload();
                            $eb.$swal('success',res.msg);
                        }else
                            $eb.$swal('error',res.msg);
                    }
                })
            }else{
                $eb.$swal('error','请输入要备注的内容');
            }
        });
    })
    $('.modify').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    window.location.reload();
                    $eb.$swal('success',res.data.msg);
                }else
                    return Promise.reject(res.data.msg || '修改失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        },{'title':'您确定要修改商户的状态吗？','text':'请谨慎操作！','confirm':'是的，我要修改'})
    });
    $('.success').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    window.location.reload();
                    $eb.$swal('success',res.data.msg);
                }else
                    return Promise.reject(res.data.msg || '删除失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        },{'title':'您确定重置选择商户的密码吗？','text':'重置后的密码为1234567','confirm':'您确定重置密码吗？'})
    });
</script>
{/block}
