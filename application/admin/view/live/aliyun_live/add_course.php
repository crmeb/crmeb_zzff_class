{include file="public/frame_head"}
<link href="{__FRAME_PATH}css/plugins/iCheck/custom.css" rel="stylesheet">
<script src="{__PLUG_PATH}moment.js"></script>
<link rel="stylesheet" href="{__PLUG_PATH}daterangepicker/daterangepicker.css">
<script src="{__PLUG_PATH}daterangepicker/daterangepicker.js"></script>
<script src="{__ADMIN_PATH}frame/js/plugins/iCheck/icheck.min.js"></script>
<style type="text/css">
    .form-inline .input-group{display: inline-table;vertical-align: middle;}
    .form-inline .input-group .input-group-btn{width: auto;}
    .form-add{position: fixed;left: 0;bottom: 0;width:100%;}
    .form-add .sub-btn{border-radius: 0;width: 100%;padding: 6px 0;font-size: 14px;outline: none;border: none;color: #fff;background-color: #2d8cf0;}
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="m-b m-l">
                        <form class="form-inline search" id="form" method="get">
                            <div class="input-group">
                                <input style="width: 200px;" type="text" name="nickname" value="{$where.nickname}" placeholder="请输入微信用户名称" class="input-sm form-control"> <span class="input-group-btn">
                                <button type="submit" class="btn btn-sm btn-primary"> <i class="fa fa-search"></i>搜索</button> </span>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">编号</th>
                            <th class="text-center">微信用户名称</th>
                            <th class="text-center">头像</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        <form method="post" class="sub-save">
                            {volist name="list" id="vo"}
                            <tr>
                                <td class="text-center">
                                    {$vo.uid}
                                </td>
                                <td class="text-center">
                                    {$vo.nickname}
                                </td>
                                <td class="text-center">
                                    <img src="{$vo.avatar}" alt="{$vo.nickname}" title="{$vo.nickname}" style="width:50px;height: 50px;cursor: pointer;" class="head_image" data-image="{$vo.avatar}">
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-xs" data-uid="{$vo.uid}" type="button"><i class="fa fa-paste"></i>添加为嘉宾</button>
                                </td>
                            </tr>
                            {/volist}
                        </form>
                        </tbody>
                    </table>
                </div>
                {include file="public/inner_page"}
            </div>
        </div>
    </div>
</div>
{block name="script"}
<script>
    $('.btn-warning').on('click',function(){
        window.t = $(this);
        var _this = $(this),uid =_this.data('uid'),live_id={$live_id};
        layer.confirm('选择嘉宾类型', {
            btn: ['讲师','助教'] //按钮
        }, function(index){
           var url ="{:Url('save_guest')}?uid="+uid+'&live_id='+live_id+'&type=1';
            $eb.$swal('delete',function(){
                $eb.axios.get(url).then(function(res){
                    if(res.status == 200 && res.data.code == 200) {
                        $eb.$swal('success',res.data.msg);
                        layer.close(index);
                    }else{
                        layer.close(index);
                        return Promise.reject(res.data.msg)
                    }
                }).catch(function(err){
                    $eb.$swal('error',err);
                });
            },{title:"确认要把该用户添加成嘉宾吗?",text:'确认后可在列表修改',confirm:'确认'});
        }, function(idx){
            var url ="{:Url('save_guest')}?uid="+uid+'&live_id='+live_id+'&type=0';
            $eb.$swal('delete',function(){
                $eb.axios.get(url).then(function(res){
                    if(res.status == 200 && res.data.code == 200) {
                        $eb.$swal('success',res.data.msg);
                        layer.close(idx);
                    }else{
                        layer.close(idx);
                        return Promise.reject(res.data.msg)
                    }
                }).catch(function(err){
                    $eb.$swal('error',err);
                });
            },{title:"确认要把该用户添加成嘉宾吗?",text:'确认后可在列表修改',confirm:'确认'});
        });
    });
</script>
{/block}