{extend name="public/container"}
{block name="head_top"}

{/block}
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
                                    <input type="hidden" name="name" value="{$where.name}">
                                    <input type="text" name="nickname" placeholder="请输入姓名/电话/地址进行查找" class="layui-input" value="{$where.nickname}">
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

        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">列表</div>
                <div class="layui-card-body">
                    <div class="layui-form">
                        <table class="layui-table">
                            <thead>
                                <tr>
                                    <th>用户UID</th>
                                    <th>用户名</th>
                                    <th>头像</th>
                                    <th>加入时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                {volist name='list' id='vo'}
                                <tr>
                                    <td>{$vo.uid}</td>
                                    <td>{$vo.nickname}</td>
                                    <td><img src="{$vo.avatar}" alt="" style="width: 100px;height: 100px"></td>
                                    <td>{$vo.add_time|date='Y-m-d H:i:s',###}</td>
                                    <td>
                                        <button class="layui-btn layui-btn-normal layui-btn-sm select" data-uid="{$vo.uid}" data-nickname="{$vo.nickname}">选择</button>
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
<script>
    var val_name="{$where.name}";
    var index =parent.layer.getFrameIndex(window.name);
    $('.select').on('click',function () {
        console.log(val_name);
        parent.setInputValue(val_name,$(this).data('nickname'),$(this).data('uid'));
        parent.layer.close(index);
    })
</script>
{/block}