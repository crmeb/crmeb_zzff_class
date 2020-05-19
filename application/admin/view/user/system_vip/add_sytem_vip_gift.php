{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">姓名</label>
                                <div class="layui-input-block">
                                    <input type="text" name="gift_name" placeholder="请输入姓名/电话/地址进行查找" class="layui-input" value="{$where.gift_name}">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="layui-col-md12 layui-col-sm12 layui-col-lg12">
            <div class="layui-card">
                <div class="layui-card-header">团粉丝团评论</div>
                <div class="layui-card-body">
                    <div class="layui-btn-container">
                        <button class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('add_gift',['vip_id'=>$vip_id])}')">添加礼品</button>
                        <button class="layui-btn layui-btn-sm layui-btn-warm" onclick="location.reload()">刷新</button>
                    </div>
                    <div class="layui-form">
                        <table class="layui-table">
                            <thead>
                            <tr>
                                <th>礼品ID</th>
                                <th>礼品名</th>
                                <th>礼品个数</th>
                                <th>排序</th>
                                <th>礼品封面图</th>
                                <th>添加时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="list" id="vo"}
                            <tr>
                                <td>{$vo.id}</td>
                                <td>{$vo.gift_name}</td>
                                <td>{$vo.gift_count}</td>
                                <td>{$vo.sort}</td>
                                <td><img src="{$vo.gift_cover}" alt=""></td>
                                <td>{$vo.add_time|date='Y-m-d H:i:s',###}</td>
                                <td>
                                    <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('add_gift',['vip_id'=>$vip_id,'id'=>$vo['id']])}')">编辑</button>
                                    <button class="layui-btn layui-btn-primary layui-btn-sm delstor" data-url="{:Url('delete_gift',['id'=>$vo['id']])}">删除</button>
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
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    layList.form.render();
    layList.date({elem:'#start_time',theme:'#393D49',type:'datetime'});
    layList.date({elem:'#end_time',theme:'#393D49',type:'datetime'});
    layList.search('search',function(where){
        window.location.href=layList.U({a:'comment_list',q:where});
    });
    $('.delstor').on('click',function (){
        var url=$(this).data('url'),that=this;
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                    $(that).parents('tr').remove();
                }else
                    return Promise.reject(res.data.msg || '删除失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        })
    })
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    })
    function dropdown(that){
        var oEvent = arguments.callee.caller.arguments[0] || event;
        oEvent.stopPropagation();
        var offset = $(that).offset();
        var top=offset.top-$(window).scrollTop();
        var index = $(that).parents('tr').data('index');
        $('.layui-nav-child').each(function (key) {
            if (key != index) {
                $(this).hide();
            }
        })
        if($(document).height() < top+$(that).next('ul').height()){
            $(that).next('ul').css({
                'padding': 10,
                'top': - ($(that).parent('td').height() / 2 + $(that).height() + $(that).next('ul').height()/2),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }else{
            $(that).next('ul').css({
                'padding': 10,
                'top':$(that).parent('td').height() / 2 + $(that).height(),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }
    }
    $(".open_image").on('click',function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
</script>
{/block}