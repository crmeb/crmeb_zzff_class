{extend name="public/container"}
{block name='head'}
<style>
    .dowload-box{margin: 30px;background-color: #ffffff}
    .dowload-box .item{padding: 10px 0;}
    .dowload-box .item .item-yun{border-radius: 50%;width: 33px;height: 33px;border:1px solid #eee;text-align: center;float: left;line-height: 33px;}
    .dowload-box .item .item-text{margin-left: 20px;height: 33px;line-height: 33px;float: left;}
    .dowload-box .item .item-url{margin-left: 20px;height: 33px;line-height: 24px;float: left;padding: 5px;background-color: #eee;border-radius: 5px;width: 80%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;}
    .dowload-box .item .item-button{margin-left: 12px;width: 64px;height: 28px;line-height: 28px;text-align: center;border-radius: 2px;background-color: #2a75ed;color: #fff;cursor: pointer;font-size: 12px;float: left}
    .dowload-box .item .item-img{width: 100%;padding: 10px;}
    .clearfloat:after{display:block;height:0;content:"";clear:both;visibility:hidden;}

</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="dowload-box">
            <div class="item clearfloat">
                <span class="item-yun">1</span>
                <p class="item-text">下载视频导出工具</p>
                <a class="item-button" href="http://xiaoetong-1252524126.file.myqcloud.com/m3u8-downloader-win-0.1.3 (1).exe">立即下载</a>
            </div>
            <div class="item clearfloat">
                <span class="item-yun">2</span>
                <p class="item-text">完成下载后，复制以下链接至工具视频地址处</p>
            </div>
            <div class="item clearfloat">
                <div class="item-text" style="margin-left: 55px;">
                    <p class="item-url" id="content">{$record_url}</p>
                    <p class="item-button copy">复制</p>
                </div>
            </div>
            <div class="item clearfloat">
                <span class="item-yun">3</span>
                <p class="item-text">在下载的导出工具中，操作步骤如图：</p>
                <img class="item-img" src="https://admin.xiaoe-tech.com/images/admin/shopDiy/img_live_export.png" alt="">
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script type="text/javascript">
    $('.copy').click(function () {
        copy('content',function () {
            layList.msg('复制成功');
        })
    })
    function copy (id, attr,errorFn)
    {
        var target = null,successFn=null;
        if(typeof attr=='function'){
            successFn=attr;
            attr='';
        }
        if (attr && typeof attr=='string') {
            target = document.createElement('div');
            target.id = 'tempTarget';
            target.style.opacity = '0';
            if (id) {
                var curNode = document.querySelector('#' + id);
                target.innerText = curNode[attr];
            } else {
                target.innerText = attr;
            }
            document.body.appendChild(target);
        } else {
            target = document.querySelector('#' + id);
        }

        try {
            var range = document.createRange();
            range.selectNode(target);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            window.getSelection().removeAllRanges();
            successFn && successFn();
        } catch (e) {
            errorFn && errorFn();
        }

        if (attr) {
            // remove temp target
            target.parentElement.removeChild(target);
        }
    }
</script>
{/block}

