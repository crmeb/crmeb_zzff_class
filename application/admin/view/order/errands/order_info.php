{extend name="public/container"}
{block name="content"}
<style>
    .color-red{color: red}
</style>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" >
        <!--订单归属用户信息-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">订单归属用户信息</div>
                <div class="layui-card-body">
                    <table class="layui-table">
                        <colgroup>
                            <col width="150">
                            <col>
                        </colgroup>
                        <tbody>
                            <tr>
                                <td class="text-center">用户名</td>
                                <td>{$userinfo.nickname}</td>
                            </tr>
                            <tr>
                                <td class="text-center">联系方式</td>
                                <td>{$order.user_phone}</td>
                            </tr>
                            <tr>
                                <td class="text-center">真实姓名</td>
                                <td>{$order.real_name}</td>
                            </tr>
                            <tr>
                                <td class="text-center">详细地址</td>
                                <td>{$order.user_address}</td>
                            </tr>
                            <tr>
                                {if $order.order_type==1}
                                <td class="text-center">指定购买地址</td>
                                <td>{$order.specify_address}</td>
                                {elseif $order.order_type==2}
                                <td class="text-center">指定送货地址</td>
                                <td>{$order.specify_address}</td>
                                {elseif $order.order_type==3}
                                <td class="text-center">指定取货地址</td>
                                <td>{$order.specify_address}</td>
                                {elseif $order.order_type==4}
                                <td class="text-center">指定地址</td>
                                <td>{$order.specify_address}</td>
                                {/if}
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end-->
        <!--订单详情-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">订单详情</div>
                <div class="layui-card-body">
                    <table class="layui-table">
                        <colgroup>
                            <col width="15%">
                            <col width="35%">
                            <col width="15%">
                            <col width="35%">
                        </colgroup>
                        <tbody>
                        <tr>
                            <td class="text-center">商品名称或服务类型</td>
                            <td colspan="3">{$order.good_name}</td>
                        </tr>
                        <tr>
                            <td class="text-center">订单ID</td>
                            <td>{$order.order_id}</td>
                            <td class="text-center">订单类型</td>
                            <td>{$order.pink_name}</td>
                        </tr>
                        <tr>
                            <td class="text-center">配送费</td>
                            <td class="color-red">￥{$order.delivery_price}</td>
                            <td class="text-center">支付状态</td>
                            <td>{$order.pay_type_name}</td>
                        </tr>
                        <tr>
                            <td class="text-center">订单状态</td>
                            <td>{$order.status_name}</td>
                            <td class="text-center">线下支付</td>
                            <td>{if $order.pay_type_info==1}是{else}否{/if}</td>
                        </tr>
                        <tr>
                            <td class="text-center">支付时间</td>
                            <td>{if $order.pay_time==0}暂无支付时间{else}{$order.pay_time|date='Y-m-d H:i:s',###}{/if}</td>
                            <td class="text-center">订单生成时间</td>
                            <td>{$order.add_time|date='Y-m-d H:i:s',###}</td>
                        </tr>
                        <tr>
                            <td class="text-center">订单金额</td>
                            <td class="color-red">￥{$order.total_postage}</td>
                            <td class="text-center">支付金额</td>
                            <td class="color-red">￥{$order.pay_price}</td>
                        </tr>
                        {if $order.refund_status > 0}
                        <tr>
                            <td class="text-center">退款原因</td>
                            <td class="color-red">{$order.refund_reason_wap}</td>
                            <td class="text-center">退款金额金额</td>
                            <td class="color-red">￥{$order.refund_price}</td>
                        </tr>
                        {/if}
                        {if $order.order_type==1}
                        <tr>
                            <td class="text-center">预期支付商品金额</td>
                            <td class="color-red" colspan="3">￥{$order.estimate_price}</td>
                        </tr>
                        {/if}
                        <tr>
                            <td class="text-center">用户备注</td>
                            <td colspan="3">{$order.user_make}</td>
                        </tr>
                        <tr>
                            <td class="text-center">管理员备注</td>
                            <td colspan="3">{$order.admin_make}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end-->
        <!--配送员或服务小哥详情-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">配送员或服务小哥详情</div>
                <div class="layui-card-body">
                    <table class="layui-table">
                        <colgroup>
                            <col width="15%">
                            <col width="35%">
                            <col width="15%">
                            <col width="35%">
                        </colgroup>
                        <tbody>
                            {if $order.delivery_id && isset($delivery) && $delivery}
                            <tr>
                                <td class="text-center">用户名</td>
                                <td>{$delivery.nickname}</td>
                                <td class="text-center">真实姓名</td>
                                <td>{$delivery.real_name}</td>
                            </tr>
                            <tr>
                                <td class="text-center">性别</td>
                                <td>{if $delivery.sex==0}未知{elseif $delivery.sex==1}男{else}女{/if}</td>
                                <td class="text-center">联系方式</td>
                                <td>{$delivery.phone}</td>
                            </tr>
                            <tr>
                                <td class="text-center">学校名称</td>
                                <td>{$delivery.school}</td>
                                <td class="text-center">专业</td>
                                <td>{$delivery.major}</td>
                            </tr>
                            <tr>
                                <td class="text-center">年级</td>
                                <td>{$delivery.grade}</td>
                                <td class="text-center">成为时间</td>
                                <td>{$delivery.add_time|date='Y-m-d H:i:s',###}</td>
                            </tr>
                            <tr>
                                <td class="text-center">证件信息</td>
                                <td colspan="3">
                                    {volist name='$delivery.attachment' id='pic'}
                                    <img src="{$pic}" alt="" style="width: 150px;height: 150px" data-name="{$pic}">
                                    {/volist}
                                </td>
                            </tr>
                            {else}
                            <tr>
                                <td class="text-center" colspan="2">尚未接单暂无信息</td>
                            </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end-->
    </div>
</div>
{/block}
{block name="script"}
<script>
    $('img').on('click',function () {
        $eb.openImage($(this).data('name'));
    })
</script>
{/block}