{extend name="public/container"}
{block name="head_top"}
<link href="{__FRAME_PATH}css/plugins/iCheck/custom.css" rel="stylesheet">
<script src="{__PLUG_PATH}sweetalert2/sweetalert2.all.min.js"></script>
<script src="{__PLUG_PATH}moment.js"></script>
<link rel="stylesheet" href="{__PLUG_PATH}daterangepicker/daterangepicker.css">
<script src="{__PLUG_PATH}daterangepicker/daterangepicker.js"></script>
<script src="{__ADMIN_PATH}frame/js/plugins/iCheck/icheck.min.js"></script>
{/block}
{block name="content"}
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <button type="button" class="btn btn-w-m btn-primary grant" >对账</button>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="m-b m-l">
                        <form action="" class="form-inline">
                            <div class="input-group datepicker">
                                <input style="width: 188px;" type="text" id="data" class="input-sm form-control" name="data" value="{$where.data}" placeholder="请选择日期" >
                            </div>
                            <select name="is_mer_check" aria-controls="editable" class="form-control input-sm">
                                <option value="0" {eq name="where.is_mer_check" value="0"}selected="selected"{/eq}>未对账</option>
                                <option value="1" {eq name="where.is_mer_check" value="1"}selected="selected"{/eq}>已对账</option>
                                <option value="-1" {eq name="where.is_mer_check" value="-1"}selected="selected"{/eq}>全部</option>
                            </select>
                            <div class="input-group">
                                <input size="26" type="text" name="real_name" value="{$where.real_name}" placeholder="请输入姓名、电话、订单编号" class="input-sm form-control">
                                <input type="hidden" name="export" value="0">
                                <span class="input-group-btn">
                                    <button type="submit" id="no_export" class="btn btn-sm btn-primary"> <i class="fa fa-search" ></i> 搜索</button>
                                </span>
                            </div>
                            <button type="submit" id="export" class="btn btn-sm btn-info btn-outline"> <i class="fa fa-exchange" ></i> Excel导出</button>
                            <script>
                                $('#export').on('click',function(){
                                    $('input[name=export]').val(1);
                                });
                                $('#no_export').on('click',function(){
                                    $('input[name=export]').val(0);
                                });
                            </script>
                        </form>
                    </div>

                </div>
                <?php $list_num = $list->toArray(); ?>
                <div class="row">
                    <div class="col-sm-3">
                        <dl class="dl-horizontal">
                            {if condition="$list_num['total'] GT 0"}
                            <dt style="color: #1ab394">订单数：</dt>
                            <dd style="color: #1ab394">{$list_num.total}</dd>
                            {/if}
                            {if condition="$price['total_num'] GT 0"}
                            <dt style="color: #0c7a84">商品总数：</dt>
                            <dd style="color: #0c7a84">{$price.total_num}</dd>
                            {/if}
                            {if condition="$price['pay_price'] GT 0"}
                            <dt style="color: #b3573c">支付金额：</dt>
                            <dd style="color: #b3573c">￥{$price.pay_price}</dd>
                            {/if}
                            {if condition="$price['refund_price'] GT 0"}
                            <dt style="color: #3fb327">退款金额：</dt>
                            <dd style="color: #3fb327">￥{$price.refund_price}</dd>
                            {/if}
                            {if condition="$price['pay_price_wx'] GT 0"}
                            <dt style="color: #4b241a">微信支付金额：</dt>
                            <dd style="color: #4b241a">￥{$price.pay_price_wx}</dd>
                            {/if}
                            {if condition="$price['pay_price_yue'] GT 0"}
                            <dt style="color: #112351">余额支付金额：</dt>
                            <dd style="color: #112351">￥{$price.pay_price_yue}</dd>
                            {/if}
                            {if condition="$price['pay_price_offline'] GT 0"}
                            <dt style="color: #a438f1">线下支付金额：</dt>
                            <dd style="color: #a438f1">￥{$price.pay_price_offline}</dd>
                            {/if}
                            {if condition="$price['pay_price_other'] GT 0"}
                            <dt style="color: #e63e47">其他支付金额：</dt>
                            <dd style="color: #e63e47">￥{$price.pay_price_other}</dd>
                            {/if}
                            {if condition="$price['use_integral'] GT 0"}
                            <dt style="color: #1d25da">用户使用积分：</dt>
                            <dd style="color: #1d25da">{$price.use_integral}（抵扣金额：￥{$price.deduction_price}）</dd>
                            {/if}
                            {if condition="$price['back_integral'] GT 0"}
                            <dt style="color: #f530c0">退回积分：</dt>
                            <dd style="color: #f530c0">{$price.back_integral}</dd>
                            {/if}
                        </dl>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 80px">
                                <label>
                                    <span class="checkbox-all" style="cursor: pointer">全选</span>
                                </label>
                            </th>
                            <th class="text-center">编号</th>
                            <th class="text-center">是否对账</th>
                            <th class="text-center">订单编号</th>
                            <th class="text-center">微信用户</th>
                            <th class="text-center">用户信息</th>
                            <th class="text-center">商品信息</th>
                            <th class="text-center">商品总数</th>
                            <th class="text-center">商品总价</th>
                            <th class="text-center">邮费</th>
                            <th class="text-center">实际支付</th>
                            <th class="text-center">下单时间</th>
                            <th class="text-center">支付状态</th>
                            <th class="text-center">备注</th>
                            <th class="text-center">订单状态</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        {volist name="list" id="vo"}
                        <tr>
                            <td class="text-center">
                                {if condition="!$vo['is_mer_check']"}
                                <label class="checkbox-inline i-checks">
                                    <input type="checkbox" name="coupon[]" value="{$vo.id}">
                                </label>
                                {/if}
                            </td>
                            <td class="text-center">
                                {$vo.id}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['is_mer_check'] eq 1"}已对账{else}<font color="red">未对账</font> {/if}
                            </td>
                            <td class="text-center">
                                {$vo.order_id}
                            </td>
                            <td class="text-center">
                                {$vo.nickname}
                            </td>
                            <td class="text-center">
                                  <p>姓名:<span>{$vo.real_name}</span></p>
                                  <p>电话:<span>{$vo.user_phone}</span></p>
                                  <p>地址:<span>{$vo.user_address}</span></p>
                                {if condition="$vo['mark']"}
                                <p>订单备注:<span>{$vo.mark}</span></p>
                                {/if}
                            </td>
                            <td>
                                <?php $info_order = $vo['_info'];?>
                                {volist name="info_order" id="info"}
                                    {if condition="isset($info['cart_info']['productInfo']['attrInfo']) && !empty($info['cart_info']['productInfo']['attrInfo'])"}
                                       <p>
                                            <span><img class="open_image" data-image="{$info.cart_info.productInfo.image}" style="width: 100px;height: 100px;cursor: pointer;" src="{$info.cart_info.productInfo.attrInfo.image}" alt="{$info.cart_info.productInfo.store_name}" title="{$info.cart_info.productInfo.store_name}"></span>
                                            <span>{$info.cart_info.productInfo.store_name}&nbsp;{$info.cart_info.productInfo.attrInfo.suk}</span><span>({$info.cart_info.truePrice}×{$info.cart_info.cart_num})</span>
                                       </p>
                                    {else/}
                                       <p>
                                            <span><img class="open_image" data-image="{$info.cart_info.productInfo.image}" style="width: 100px;height: 100px;cursor: pointer;" src="{$info.cart_info.productInfo.image}" alt="{$info.cart_info.productInfo.store_name}" title="{$info.cart_info.productInfo.store_name}"></span>
                                            <span>{$info.cart_info.productInfo.store_name}</span><span>({$info.cart_info.truePrice}×{$info.cart_info.cart_num})</span>
                                       </p>
                                    {/if}
                                {/volist}
                            </td>
                            <td class="text-center">
                                {$vo.total_num}
                            </td>
                            <td class="text-center">
                                {$vo.total_price}
                            </td>
                            <td class="text-center">
                                {$vo.total_postage}
                            </td>
                            <td class="text-center">{$vo.pay_price}{if condition="$vo['refund_price'] GT 0"}
                                <br/><span style="color: #f29100">退款金额:{$vo.refund_price}</span>
                                {/if}
                                {if condition="$vo['deduction_price'] GT 0"}
                                <br/><span style="color: #07250a">使用了{$vo.use_integral}积分抵扣了{$vo.deduction_price}金额</span>
                                {/if}
                                {if condition="$vo['back_integral'] GT 0"}
                                <br/><span style="color: #943281">退积分：{$vo.back_integral}</span>
                                {/if}
                            </td>
                            <td class="text-center">
                                {$vo.add_time|date='Y-m-d H:i:s',###}
                            </td>
                            <td class="text-center">
                                 {if condition="$vo['paid'] eq 1"}
                                   <p>支付方式：<span style="color: red">
                                           {if condition="$vo['pay_type'] eq 'weixin'"}
                                           微信支付
                                           {elseif condition="$vo['pay_type'] eq 'yue'"}
                                           余额支付
                                           {elseif condition="$vo['pay_type'] eq 'offline'"}
                                           线下支付
                                           {else/}
                                           其他支付
                                           {/if}
                                       </span></p>
                                   <p>支付时间：<span>{$vo.pay_time|date='Y-m-d H:i:s',###}</span></p>
                                 {else/}
                                    {if condition="$vo['pay_type'] eq 'offline'"}
                                        <p>支付方式：<span style="color: #ff0000">线下支付</span></p>
                                        <p><button data-pay="{$vo.pay_price}" data-url="{:Url('offline',array('id'=>$vo['id']))}" type="button" class="offline_btn btn btn-w-m btn-white">立即支付</button></p>

                                    {else/}
                                        <p>状态：<span>未支付</span></p>
                                    {/if}
                                 {/if}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['paid'] eq 0 && $vo['status'] eq 0"}
                                    未支付
                                {elseif condition="$vo['paid'] eq 1 && $vo['status'] eq 0 && $vo['refund_status'] eq 0"/}
                                    已支付&nbsp;未发货
                                {elseif condition="$vo['paid'] eq 1 && $vo['status'] eq 1 && $vo['refund_status'] eq 0"/}
                                    待收货<br/>
                                    {if condition="$vo['delivery_type'] eq 'send'"}
                                        <p>送货人姓名:<span style="color: red">{$vo.delivery_name}</span></p>
                                        <p>送货人电话:<span style="color: red">{$vo.delivery_id}</span></p>
                                    {elseif condition="$vo['delivery_type'] eq 'express'"}
                                        <p>快递公司:<span style="color: red">{$vo.delivery_name}</span></p>
                                        <p>快递单号:<span style="color: red">{$vo.delivery_id}</span></p>
                                    {/if}
                                {elseif condition="$vo['paid'] eq 1 && $vo['status'] eq 2 && $vo['refund_status'] eq 0"/}
                                    待评价<br/>
                                    {if condition="$vo['delivery_type'] eq 'send'"}
                                        <p>送货人姓名:<span style="color: red">{$vo.delivery_name}</span></p>
                                        <p>送货人电话:<span style="color: red">{$vo.delivery_id}</span></p>
                                    {elseif condition="$vo['delivery_type'] eq 'express'"}
                                        <p>快递公司:<span style="color: red">{$vo.delivery_name}</span></p>
                                        <p>快递单号:<span style="color: red">{$vo.delivery_id}</span></p>
                                    {/if}
                                {elseif condition="$vo['paid'] eq 1 && $vo['status'] eq 3 && $vo['refund_status'] eq 0"/}
                                    交易完成<br/>
                                    {if condition="$vo['delivery_type'] eq 'send'"}
                                        <p>送货人姓名:<span style="color: red">{$vo.delivery_name}</span></p>
                                        <p>送货人电话:<span style="color: red">{$vo.delivery_id}</span></p>
                                    {elseif condition="$vo['delivery_type'] eq 'express'"}
                                        <p>快递公司:<span style="color: red">{$vo.delivery_name}</span></p>
                                        <p>快递单号:<span style="color: red">{$vo.delivery_id}</span></p>
                                    {/if}
                                {elseif condition="$vo['paid'] eq 1 && $vo['refund_status'] eq 1"/}
                                    <b style="color:#f124c7">申请退款</b><br/>
                                    {if condition="$vo['delivery_type'] eq 'send'"}
                                        <p>送货人姓名:<span style="color: red">{$vo.delivery_name}</span></p>
                                        <p>送货人电话:<span style="color: red">{$vo.delivery_id}</span></p>
                                    {elseif condition="$vo['delivery_type'] eq 'express'"}
                                        <p>快递公司:<span style="color: red">{$vo.delivery_name}</span></p>
                                        <p>快递单号:<span style="color: red">{$vo.delivery_id}</span></p>
                                    {/if}
                                {elseif condition="$vo['paid'] eq 1 && $vo['refund_status'] eq 2"/}
                                    已退款<br/>
                                    {if condition="$vo['delivery_type'] eq 'send'"}
                                        <p>送货人姓名:<span style="color: red">{$vo.delivery_name}</span></p>
                                        <p>送货人电话:<span style="color: red">{$vo.delivery_id}</span></p>
                                    {elseif condition="$vo['delivery_type'] eq 'express'"}
                                        <p>快递公司:<span style="color: red">{$vo.delivery_name}</span></p>
                                        <p>快递单号:<span style="color: red">{$vo.delivery_id}</span></p>
                                    {/if}
                                    {/if}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['remark']"}
                                <b style="color: #733b5c">{$vo.remark}</b>
                                <button class="btn btn-info btn-xs save_mark" type="button" data-id="{$vo['id']}" data-make="{$vo.remark}" data-url="{:Url('remark')}"><i class="fa fa-paste"></i>修改备注</button>
                                {else/}
                                <button class="btn btn-info btn-xs add_mark" type="button" data-id="{$vo['id']}" data-url="{:Url('remark')}"><i class="fa fa-paste"></i>添加备注</button>
                                {/if}
                            </td>
                        {/volist}
                        </tbody>
                    </table>
                </div>
                {include file="public/inner_page"}
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
    });
    $(".open_image").on('click',function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
    $('.btn-danger').on('click',function (e) {
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                }else
                    return Promise.reject(res.data.msg || '收货失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        },{'title':'您确定要修改收货状态吗？','text':'修改后将无法恢复,请谨慎操作！','confirm':'是的，我要修改'})
    })
    $('.offline_btn').on('click',function (e) {
        window.t = $(this);
        var _this = $(this),url =_this.data('url'),pay_price =_this.data('pay');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                }else
                    return Promise.reject(res.data.msg || '收货失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        },{'title':'您确定要修改已支付'+pay_price+'元的状态吗？','text':'修改后将无法恢复,请谨慎操作！','confirm':'是的，我要修改'})
    })

    $('.add_mark').on('click',function (e) {
        var _this = $(this),url =_this.data('url'),id=_this.data('id');
        $eb.$alert('textarea',{},function (result) {
            if(result){
                $.ajax({
                    url:url,
                    data:'remark='+result+'&id='+id,
                    type:'post',
                    dataType:'json',
                    success:function (res) {
                        console.log(res);
                        if(res.code == 200) {
                            $eb.$swal('success',res.msg);
                        }else
                            $eb.$swal('error',res.msg);
                    }
                })
            }else{
                $eb.$swal('error','请输入要备注的内容');
            }
        });
    })
    $('.save_mark').on('click',function (e) {
        var _this = $(this),url =_this.data('url'),id=_this.data('id'),make=_this.data('make');
        $eb.$alert('textarea',{title:'请修改内容',value:make},function (result) {
            if(result){
                $.ajax({
                    url:url,
                    data:'remark='+result+'&id='+id,
                    type:'post',
                    dataType:'json',
                    success:function (res) {
                        console.log(res);
                        if(res.code == 200) {
                            $eb.$swal('success',res.msg);
                        }else
                            $eb.$swal('error',res.msg);
                    }
                })
            }else{
                $eb.$swal('error','请输入要备注的内容');
            }
        });
    })
    var dateInput =$('.datepicker');
    dateInput.daterangepicker({
        autoUpdateInput: false,
        "opens": "center",
        "drops": "down",
        "ranges": {
            '今天': [moment(), moment().add(1, 'days')],
            '昨天': [moment().subtract(1, 'days'), moment()],
            '上周': [moment().subtract(6, 'days'), moment()],
            '前30天': [moment().subtract(29, 'days'), moment()],
            '本月': [moment().startOf('month'), moment().endOf('month')],
            '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "locale" : {
            applyLabel : '确定',
            cancelLabel : '清空',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            format : 'YYYY/MM/DD',
            customRangeLabel : '自定义',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',
                '七月', '八月', '九月', '十月', '十一月', '十二月' ],
            firstDay : 1
        }
    });
    dateInput.on('cancel.daterangepicker', function(ev, picker) {
        $("#data").val('');
    });
    dateInput.on('apply.daterangepicker', function(ev, picker) {
        $("#data").val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
    });
    $('.grant').on('click',function (e) {
        var chk_value =[];
        $('input[name="coupon[]"]:checked').each(function(){
            chk_value.push($(this).val());
            str += $(this).val();
        });
        if(chk_value.length < 1){
            $eb.message('请选择要对账的订单');
            return false;
        }
        var str = chk_value.join(',');
        var mer_id = "{$mer_id}";
        $.ajax({
           url:"{:Url('reconciliation_grant')}",
           data:'id='+str+'&mer_id='+mer_id,
           type:'post',
           dataType:'json',
           success:function (res) {
               if(res.code == 200) {
                   $eb.$swal('success',res.msg);
               }else
                   $eb.$swal('error',res.msg);
           }
        })
    })
    $('.checkbox-all').on('click',function (e) {
        if($(this).text() == '全选'){
            $('input[name="coupon[]"]').each(function(){
                //此处如果用attr，会出现第三次失效的情况
                $('.icheckbox_square-green').addClass('checked');
            });
            $(this).text('取消全选');
        }else{
            $('input[name="coupon[]"]').each(function(){
                $('.icheckbox_square-green').removeClass('checked');
            });
            $(this).text('全选');
            //$(this).removeAttr("checked");
        }
    })
</script>
{/block}
