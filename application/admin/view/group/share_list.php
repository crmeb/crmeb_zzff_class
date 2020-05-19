{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" >
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">姓名用户名</label>
                                <div class="layui-input-block">
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
                        <table class="layui-table" lay-size="lg">
                            <thead>
                                <tr>
                                    <th>用户名</th>
                                    <th>手机号码</th>
                                    <th>分享人数</th>
                                    <th>姓名</th>
                                    <th>分享人</th>
                                    <th>查看小组</th>
                                    <th>加入时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                {volist name='list' id='vo'}
                                <tr>
                                    <td>{$vo.user_name}</td>
                                    <td>{$vo.phone}</td>
                                    <td>{$vo.share_count}</td>
                                    <td>{$vo.full_name}</td>
                                    <td>{$vo.share_name}</td>
                                    <td><a href="{:Url('this_group',['shop_uid'=>$vo['shop_uid']])}">查看</a></td>
                                    <td>{$vo.add_time}</td>
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
{/block}