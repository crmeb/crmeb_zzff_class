{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">
            <div class="layui-btn-group">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame('添加直播域名','{:Url('create')}',{w:800,h:560})"><i class="layui-icon">&#xe608;</i>添加直播域名</button>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"  style="margin-left: 5px;"><i class="layui-icon">&#xe669;</i>刷新</button>
            </div>
        </div>
        <div class="layui-card-body">
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">域名</th>
                            <th class="text-center">CNAME</th>
                            <th class="text-center">业务类型</th>
                            <th class="text-center">直播中心</th>
                            <th class="text-center">状态</th>
                            <th class="text-center">CDN 加速区域</th>
                            <th class="text-center">推流域名</th>
                            <th class="text-center">oss桶</th>
                            <th class="text-center">主KEY</th>
                            <th class="text-center">是否使用</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        {volist name="list" id="vo"}
                        <tr>
                            <td class="text-center">
                                {$vo.domain_name}
                            </td>
                            <td class="text-center">
                                {$vo.cname}
                                <button class="btn btn-primary btn-xs configuring configuring{$vo.id}" data-id="{$vo.id}" data-domain_name="{$vo.domain_name}" data-cname="{$vo.cname}" type="button">配置</button>
                            </td>
                            <td class="text-center">
                                {if condition="$vo['live_domain_type'] eq 'liveVideo' "}
                                播流域名<br/>
                                {else/}
                                推流域名<br/>
                                {/if}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['region'] eq 'cn-beijing' "}
                                华北2(北京)<br/>
                                {elseif condition="$vo['region'] eq 'cn-shanghai' "}
                                华东2(上海)<br/>
                                {elseif condition="$vo['region'] eq 'cn-shenzhen' "}
                                华南1(深圳)<br/>
                                {elseif condition="$vo['region'] eq 'cn-qingdao' "}
                                华北1(青岛)<br/>
                                {elseif condition="$vo['region'] eq 'ap-southeast-1' "}
                                亚太东南1(新加坡）<br/>
                                {elseif condition="$vo['region'] eq 'eu-central-1' "}
                                德国<br/>
                                {elseif condition="$vo['region'] eq 'ap-northeast-1' "}
                                亚太东北1(东京)<br/>
                                {elseif condition="$vo['region'] eq 'ap-south-1' "}
                                印度(孟买)<br/>
                                {elseif condition="$vo['region'] eq 'ap-southeast-5' "}
                                印度尼西亚(雅加达)<br/>
                                {/if}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['domain_status'] eq 'online'"}
                                正常运行<br/>
                                {elseif condition="$vo['domain_status'] eq 'offline'"}
                                停用<br/>
                                {else /}
                                配置中<br/>
                                {/if}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['scope'] eq 'domestic' "}
                                中国大陆<br/>
                                {elseif condition="$vo['scope'] eq 'overseas' "}
                                海外及港澳台加速<br/>
                                {else/}
                                全球加速<br/>
                                {/if}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['live_domain_type'] eq 'liveVideo' "}
                                    {if condition="$vo['push_domain'] eq '' "}
                                        <button class="btn btn-primary btn-xs" onclick="$eb.createModalFrame(this.innerText,'{:Url('addStreaming',array('id'=>$vo['id']))}',{w:800,h:400})" type="button">添加推流域名</button>
                                    {else /}
                                        {$vo.push_domain}
                                        <button class="layui-btn layui-btn-danger layui-btn-xs del_push_domain" data-url="{:Url('delStreaming',array('id'=>$vo['id']))}" type="button">删除</button>
                                    {/if}
                                {else/}
                                不需要配置<br/>
                                {/if}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['live_domain_type'] eq 'liveVideo' "}
                                    {if condition="$vo['bucket_name'] eq '' "}
                                        <button class="btn btn-primary btn-xs" onclick="$eb.createModalFrame(this.innerText,'{:Url('toConfigure',array('id'=>$vo['id']))}',{w:800,h:400})" type="button">录制设置</button>
                                    {else /}
                                        {$vo.bucket_name}
                                        <button class="layui-btn layui-btn-danger layui-btn-xs del_app_record" data-url="{:Url('delLiveAppRecordConfig',array('id'=>$vo['id']))}" type="button">删除</button>
                                    {/if}
                                {else/}
                                不需要配置<br/>
                                {/if}
                            </td>
                            <td class="text-center">
                               {$vo.auth_key1}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['is_use'] eq 1"}
                                使用中<br/>
                                {else/}
                                未使用<br/>
                                    {if condition="$vo['live_domain_type'] eq 'liveVideo' "}
                                    <button data-url="{:url('userLiveUse',['id'=>$vo['id']])}" class="j-success btn btn-primary btn-xs" type="button"><i class="fa fa-check"></i> 使用</button>
                                    {/if}
                                {/if}
                            </td>
                            <td class="text-center">
                                {if condition="$vo['domain_status'] eq 'online'"}
                                <button class="btn btn-primary btn-xs offlines" data-url="{:Url('offlines',array('id'=>$vo['id']))}" type="button">停用</button>
                                {elseif condition="$vo['domain_status'] eq 'offline'"}
                                <button class="btn btn-primary btn-xs onlines" data-url="{:Url('onlines',array('id'=>$vo['id']))}" type="button">启用</button>
                                {else /}
                                <button class="btn btn-primary btn-xs" type="button">配置中</button>
                                {/if}
                                <button class="layui-btn layui-btn-danger layui-btn-xs detail" data-url="{:Url('delete',array('id'=>$vo['id']))}" type="button">删除</button>
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
    $('.detail').on('click',function(){
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
    $('.offlines').on('click',function(){
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
            title:'确定停用域名吗?',
            text:'域名停用，请谨慎操作！',
            confirm:'确认'
        });
    });
    $('.onlines').on('click',function(){
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
            title:'确定启用域名吗?',
            text:'域名启用，请谨慎操作！',
            confirm:'确认'
        });
    });
    $('.del_push_domain').on('click',function(){
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
            title:'确定要删除推流域名吗?',
            text:'删除推流域名，请谨慎操作！',
            confirm:'确认'
        });
    });
    $('.del_app_record').on('click',function(){
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
            title:'确定要删除录制配置吗?',
            text:'删除录制配置，请谨慎操作！',
            confirm:'确认'
        });
    });
    $('.configuring').click(function () {
        var domain_name = $(this).data('domain_name');
        var cname = $(this).data('cname');
        var id = $(this).data('id');
       var arr= domain_name.split('.');
        layer.tips('<div style="color: #0092DC;margin-top: 5px;">解析推流域名:'+domain_name+'</div> '+
            '<div style="color: #0092DC;margin-top: 5px;">解析域名为:'+arr[0]+'</div>'+
            '<div style="color: #0092DC;margin-top: 5px;">解析值为:</div>'+
            '<div style="color: #0092DC;margin-top: 5px;">'+cname+'</div>'+
            '<div style="color: #0092DC;margin-top: 5px;">解析类型为: CNAME</div>', '.configuring'+id);
    });
    $('.j-success').click(function () {
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
            title:'确定使用该直播域名吗?',
            text:'使用后无法撤销，请谨慎操作！',
            confirm:'确认'
        });
    });
</script>
{/block}
