{extend name="public/container"}
{block name="head_top"}
<style>
    .layui-table td, .layui-table th{line-height:100px};
    .col_72{background-color:#797dbd}
    .col_79{background-color:#72a4d2}
    .col_7b{background-color:#72bad2}
    .col_7a{background-color:#71bad2}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" >
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header text-center">组织架构</div>
                <div class="layui-card-body">
                    <table class="layui-table" lay-size="lg">
                        <colgroup>
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col width="200">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>职称</th>
                            <th>用户名</th>
                            <th>姓名</th>
                            <th>分享人</th>
                            <th>加入时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        {if isset($member_list)}
                            {volist name='member_list' id='vo'}
                            <tr class="{if $key==0}col_72{elseif $key > 0 && $key <3}col_79{elseif $key>2 && $key < 5}col_7b{elseif $key>4}col_7a{/if}">
                                <td>
                                    {if $key==0}
                                    店长
                                    {elseif $key > 0 && $key <3}
                                    副店长
                                    {elseif $key>2 && $key < 5}
                                    店员
                                    {elseif $key>4}
                                    待销售
                                    {/if}
                                </td>
                                <td>{$vo.phone}</td>
                                <td>{$vo.user_name}</td>
                                <td>{$vo.share_name}</td>
                                <td>{$vo.add_time|date='Y-m-d H:i:s',###}</td>
                            </tr>
                            {/volist}
                        {else}
                        <tr>
                            <td colspan="5" class="text-center">暂无数据</td>
                        </tr>
                        {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}