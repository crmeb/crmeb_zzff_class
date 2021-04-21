{extend name="public/container"}
{block name="content"}
<div class="ibox-content order-info">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    退款信息
                </div>
                <div class="panel-body">
                    <div class="row show-grid">
                        <div class="col-xs-6" >订单编号: {$orderInfo.order_id}</div>
                        <div class="col-xs-6">商品总数: {$orderInfo.total_num}</div>
                        <div class="col-xs-6">订单总价: ￥{$orderInfo.total_price}</div>
                        <div class="col-xs-6">支付邮费: ￥{$orderInfo.total_postage}</div>
                        <div class="col-xs-6">实际支付: ￥{$orderInfo.pay_price}</div>
                        <div class="col-xs-6" style="color: #f1a417">退款金额: ￥{$orderInfo.pay_price}</div>
                        <div class="col-xs-6">申请时间: {$orderInfo.refund_application_time|date="Y/m/d H:i",###}</div>
                        <div class="col-xs-6">支付方式:
                            {if condition="$orderInfo['paid'] eq 1"}
                                           {if condition="$orderInfo['pay_type'] eq 'weixin'"}
                                           微信支付
                                           {elseif condition="$orderInfo['pay_type'] eq 'yue'"}
                                           余额支付
                                           {elseif condition="$orderInfo['pay_type'] eq 'zhifubao'"}
                                           支付宝支付
                                           {else/}
                                           其他支付
                                           {/if}
                            {else/}
                            未支付
                            {/if}
                        </div>
                        {notempty name="orderInfo.pay_time"}
                        <div class="col-xs-6">支付时间: {$orderInfo.pay_time|date="Y/m/d H:i",###}</div>
                        {/notempty}
                        <div class="col-xs-6" style="color: #733b5c">用户备注: {$orderInfo.mark?:'无'}</div>
                        <div class="col-xs-12" style="color: #733b5c">退款凭证:
                            {volist name="orderInfo.refund_reason_wap_img" id="vc"}
                            <img src="{$vc}" style="width: 100px;margin-left: 10px;" onclick="openImg('{$vc}')">
                            {/volist}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="{__FRAME_PATH}js/content.min.js?v=1.0.0"></script>
{/block}
{block name="script"}
<script>
  function  openImg (pic){
      $eb.openImage(pic);
  }
</script>
{/block}
