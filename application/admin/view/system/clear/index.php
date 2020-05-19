{include file="public/frame_head"}
<div class="row" style="width: 100%;margin-top: 50px; text-align: center;">
<button type="button" class="btn btn-w-m layui-btn" data-url="{:Url('system.clear/refresh_cache')}">刷新数据缓存</button>
<button type="button" class="btn btn-w-m layui-btn" data-url="{:Url('system.clear/delete_cache')}">清除缓存</button>
<button type="button" class="btn btn-w-m layui-btn" data-url="{:Url('system.clear/delete_log')}">清除日志</button>
<button type="button" class="btn btn-w-m layui-btn-normal" onclick="action.data_compatible()">数据兼容</button>
</div>
<!--<script src="/publick/sytem/js/index.js"></script>-->
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    $('.layui-btn').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                }else
                    return Promise.reject(res.data.msg || '操作失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        },{'title':'您确定要进行此操作吗？','text':'操作后runtime目录文件有可能被删除,请谨慎操作！','confirm':'是的，我要操作'})
    });
    //自定义方法
    var action= {
        data_compatible: function () {
            var loading = layer.load(0, {
                shade: [0.1,'#fff'],
                time: 100*1000
            });
            layList.baseGet(layList.Url({
                c: 'system.system_clear' ,
                a: 'data_compatible',
                q: {}
            }), function (res) {
                layer.close(loading)
                layList.msg(res.msg);
            }, function (){
                layer.close(loading)
                layList.msg(res.msg);
            });
        },
    }

</script>
{include file="public/inner_footer"}
