{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">
            <div class="layui-btn-group">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame('添加存储空间','{:Url('create')}',{w:800,h:560})"><i class="layui-icon">&#xe608;</i> 添加存储空间</button>
            </div>
        </div>

        <div class="layui-card-body">

            <div class="ibox-content">
                <div class="row">
                    <div class="m-b m-l">
                        <form action="" class="form-inline">
                            <select name="endpoint" aria-controls="editable" class="form-control input-sm">
                                <option value="">区域</option>
                                {volist name="$endpoint" id="vo" key="k"}
                                <option value="{$vo}" {eq name="where.endpoint" value="$vo"}selected="selected"{/eq}>{$key}</option>
                                {/volist}
                            </select>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <input type="hidden" name="types" value="1" id="types">
                                    <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm"><i class="layui-icon">&#xe615;</i> 搜索</button>
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload();" style="margin-left: 5px;"><i class="layui-icon">&#xe669;</i> 刷新</button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">存储空间名称</th>
                            <th class="text-center">区域</th>
                            <th class="text-center">空间域名 Domain</th>
                            <th class="text-center">是否使用</th>
                            <th class="text-center">创建时间</th>
                            <th class="text-center">拉取时间</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        {volist name="list" id="vo"}
                        <tr>
                            <td class="text-center">
                                {$vo.bucket_name}
                            </td>
                            <td class="text-center">
                                {$vo.endpoint}
                            </td>
                            <td class="text-center">
                                {$vo.domain_name}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['is_use'] eq 1"}
                                上传使用<br/>
                                {elseif condition="$vo['is_use'] eq 2"}
                                直播使用<br/>
                                {else/}
                                未使用<br/>
                                <button data-url="{:url('userUse',['id'=>$vo['id']])}" class="layui-btn layui-btn-normal layui-btn-xs use-btn" type="button"><i class="fa fa-check"></i> 使用</button>
                                {/if}
                            </td>
                            <td class="text-center">
                                {$vo.creation_time}
                            </td>
                            <td class="text-center">
                                {$vo.add_time ? date('Y/m/d H:i',$vo.add_time) : ''}
                            </td>
                            <td class="text-center">
                                <button class="layui-btn layui-btn-danger layui-btn-xs" data-url="{:Url('delete',array('id'=>$vo['id']))}" type="button"><i class="layui-icon">&#xe640;</i> 删除
                                </button>
                            </td>
                        </tr>
                        {/volist}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    $('.layui-btn-danger').on('click',function(){
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
    $('.use-btn').on('click',function(){
        var url = $(this).data('url');
        $eb.$swal('delete',function(){
            $eb.axios.post(url).then(function(res){
                if(res.data.code == 200) {
                    window.location.reload();
                    $eb.$swal('success', res.data.msg);
                }else
                    $eb.$swal('error',res.data.msg||'操作失败!');
            });
        },{
            title:'确定使用该储存空间吗?',
            text:'使用后无法撤销，请谨慎操作！',
            confirm:'确认'
        });
    });
</script>
{/block}
