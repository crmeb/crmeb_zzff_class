{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form action="" class="form-inline">
                                <select name="status" aria-controls="editable" class="form-control input-sm">
                                    <option value="">是否有效</option>
                                    <option value="1" {eq name="where.status" value="1"}selected="selected"{/eq}>开启</option>
                                    <option value="0" {eq name="where.status" value="0"}selected="selected"{/eq}>关闭</option>
                                </select>
                                <div class="input-group">
                                    <input type="text" name="name" value="{$where.name}" placeholder="请输入模板名" class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm"><i class="layui-icon">&#xe615;</i>搜索</button> </span>
                                </div>
                            </form>
                        </div>
                        <div class="layui-col-md12">
                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" onclick="$eb.createModalFrame('添加订阅消息','{:Url('create')}')"><i class="layui-icon">&#xe608;</i>添加订阅消息</button>
                        </div>
                        <div class="layui-col-md12">
                            <div>
                                <p>主营行业：<span><?= isset($industry['primary_industry']) ? $industry['primary_industry']['first_class'].' | '.$industry['primary_industry']['second_class'] : '未选择' ?></span></p>
                                <p>副营行业：<span><?= isset($industry['secondary_industry']) ? $industry['secondary_industry']['first_class'].' | '.$industry['secondary_industry']['second_class'] : '未选择' ?></span></p>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped  table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width:60px;">编号</th>
                                        <th class="text-center">模板编号</th>
                                        <th class="text-center">模板ID</th>
                                        <th class="text-center">模板名</th>
                                        <th class="text-center">回复内容</th>
                                        <th class="text-center">状态</th>
                                        <th class="text-center">添加时间</th>
                                        <th class="text-center">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody class="">
                                    {volist name="list" id="vo"}
                                    <tr>
                                        <td class="text-center">
                                            {$vo.id}
                                        </td>
                                        <td class="text-center">
                                            {$vo.tempkey}
                                        </td>
                                        <td class="text-center">
                                            {$vo.tempid}
                                        </td>
                                        <td class="text-center">
                                            {$vo.name}
                                        </td>
                                        <td class="text-center">
                                            <pre>{$vo.content}</pre>
                                        </td>
                                        <td class="text-center">
                                            <i class="fa {eq name='vo.status' value='1'}fa-check text-navy{else/}fa-close text-danger{/eq}"></i>
                                        </td>
                                        <td class="text-center">
                                            {$vo.add_time|date='Y-m-d H:i:s',###}
                                        </td>
                                        <td class="text-center">
                                            <button class="layui-btn layui-btn-xs layui-btn-normal" type="button"  onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['id']))}',{h:400})" style="margin: 5px 0;"><i class="layui-icon">&#xe642;</i>编辑</button>
                                            <button class="layui-btn layui-btn-xs layui-btn-danger" data-url="{:Url('delete',array('id'=>$vo['id']))}" type="button" style="margin: 5px 0;"><i class="layui-icon">&#xe640;</i>删除
                                            </button>
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
                console.log(res);
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                    _this.parents('tr').remove();
                }else
                    return Promise.reject(res.data.msg || '删除失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        });
    });
    $(".open_image").on('click',function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
</script>
{/block}
