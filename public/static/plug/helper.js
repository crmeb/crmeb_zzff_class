(function(global,factory){
    typeof define == 'function' && define('helper',[],factory.bind(window));
    global['$h'] = factory();
})(this,function(){
    var $h = $h || {};
    $h._errorMsgOptions = {offset: '80%',anim: 2,time:1200,shadeClose:true,shade:'0.1'};
    $h.returnErrorMsg = function(msg,fn){
        $h.pushMsg(msg,fn);
        return false;
    };
    $h._loadIndex = null;
    $h.pushMsg  = function(msg,fn){
        requirejs(['layer'],function(layer){
            layer.msg(msg.toString(),$h._errorMsgOptions,fn);
        });
    };

    $h.openImage=function(href){
        requirejs(['layer'],function(layer) {
            return layer.open({
                type: 1,
                title: false,
                closeBtn: 0,
                shadeClose: true,
                content: '<img src="' + href + '" style="display: block;width: 100%;" />'
            });
        })
    };

    $h.pushMsgOnceStatus = false;
    $h.pushMsgOnce = function(msg,fn){
        if($h.pushMsgOnceStatus) return ;
        $h.pushMsgOnceStatus = true;
        $h.pushMsg(msg,function(){
            fn && fn();
            $h.pushMsgOnceStatus = false;
        });
    }
    $h.MsgStyle='<style id="_h_msg_style">#_h_msg_html_show{width: 2.3rem;height: 1.8rem;background-color: rgba(0,0,0,0.8);border-radius: 0.2rem;position: fixed;left:0; right:0; top:0; bottom:0;margin:auto;z-index: 10099;}#_h_msg_html_show .msg_title{color: #ffffff;text-align: center;padding: 0.2rem;}#_h_msg_html_show .msg_image{width: 0.8rem;height: 0.8rem;margin: 0 auto;display: inherit;margin-top: 0.2rem;}</style>';
    $h.getMsgHthml=function (title,icon) {
        var html='<div id="_h_msg_html_show">';
        if(icon) html+='<img class="msg_image" src="/wap/first/zsff/images/msg_image/'+icon+'.png">';
        if(title) html+='<p class="line1 msg_title">'+title+'</p>';
        html+='</div>';
        return html;
    };

    $h.showMsg=function(opt,successFn){
        var $body = $('body'),title='',icon='error',endtime=1500,fn=null;
        if(typeof opt=='object'){
            title=opt.title || '';
            icon=opt.icon || 'error';
            endtime=opt.endtime || endtime;
            fn=opt.success || fn;
        }else if(typeof opt=='string'){
            title=opt;
            fn=successFn;
        }
        if(!$body.find('#_h_msg_style').length) $body.append(this.MsgStyle);
        $body.append(this.getMsgHthml(title,icon));
        setTimeout(function () {
            $('#_h_msg_style').remove();
            $('#_h_msg_html_show').remove();
            fn && fn();
        },endtime);
    };

    $h.OpenVideo=function(opt){
        if(typeof opt=='string') opt={U:opt};
        var url=opt.U || '';
        if(!url) return;
        $h.loadFFF();
        requirejs(['layer'],function(layer) {
            $h.loadClear();
            layer.open({
                type: 2,
                title: false,
                area: [window.innerWidth+'px', '360px'],
                shade: 0.8,
                closeBtn: 0,
                shadeClose: true,
                content:url,
                success:function () {
                    console.log(1);
                }
            });
        })
    }

    /**
     * 底部浮动框
     * @param msg
     */
    $h._promptStyle='<style id="_loading_bounce_style"> #_loading_bounce._prompt_hide{animation:down-hide .25s ease-in; animation-fill-mode: forwards; } #_loading_bounce{z-index: 998;position:fixed;bottom:0;background:#fff;width:100%;height:60px;box-shadow:0 1px 15px rgba(0,0,0,0.17);animation:up-show .25s ease-in}@keyframes up-show{0%{transform:translateY(60px)}100%{transform:translateY(0px)}}@keyframes down-hide{0%{transform:translateY(0px)}100%{transform:translateY(60px)}}#_loading_bounce ._double-container{height: 100%;display: table;position: absolute;width: 30%;left: 44%;} #_loading_bounce ._double-container .double-text{display: table-cell;vertical-align: middle;font-size: 12px;}.double-bounce1,.double-bounce2{width:50px;height:50px;border-radius:50%;background-color:#67CF22;opacity:.6;position:absolute;top:50%;margin-top:-25px;left:26%;-webkit-animation:bounce 2.0s infinite ease-in-out;-moz-animation:bounce 2.0s infinite ease-in-out;-o-animation:bounce 2.0s infinite ease-in-out;animation:bounce 2.0s infinite ease-in-out}.double-bounce2{-webkit-animation-delay:-1.0s;-moz-animation-delay:-1.0s;-o-animation-delay:-1.0s;animation-delay:-1.0s}@keyframes bounce{0%,100%{transform:scale(0.0)}50%{transform:scale(1.0)}}</style>';
    $h._promptHtml = '<div id="_loading_bounce" class="_prompt_loading"><div class="mop-css-1 double-bounce"><div class="double-bounce1"></div><div class="double-bounce2"></div></div><div class="_double-container"><span class="double-text">请稍等片刻...</span></div></div>';
    $h.prompt = function(msg){
        var $body = $('body'),$prompt = $($h._promptHtml);
        if(!$body.find('#_loading_bounce_style').length)
            $body.append($h._promptStyle);
        $prompt.find('.double-text').text(msg);
        $body.append($prompt);
    };
    $h.promptClear = function() {
        var that = $('._prompt_loading');
        that.addClass('_prompt_hide');
        setTimeout(function(){
            that.remove();
        },250)
    };
    $h.load = function(){
        if($h._loadIndex !== null) $h.loadClear();
        requirejs(['layer'],function(layer) {
            $h._loadIndex = layer.load(2, {shade: 0.3});
        });
    };
    $h.loadFFF = function(){
        if($h._loadIndex !== null) $h.loadClear();
        requirejs(['layer'],function(layer) {
            $h._loadIndex = layer.load(1, {shade: [0.1,'#fff']});
        });
    };
    $h.loadClear = function(){
        requirejs(['layer'],function(layer){
            setTimeout(function(){
                layer.close($h._loadIndex);
            },250);
        });
    };

    $h.uploadFile = function (name,url,successFn,errorFn) {
        $.ajaxFileUpload({
            url: url,
            type: 'post',
            secureuri: false, //一般设置为false
            fileElementId: name, // 上传文件的id、name属性名
            dataType: 'json', //返回值类型，一般设置为json、application/json
            success:successFn,
            error: errorFn
        });
    };
    $h.ajaxUploadFile = function (name,url,fnGroup) {
        fnGroup.start && fnGroup.start();
        $.ajaxFileUpload({
            url: url,
            type: 'POST',
            secureuri: false, //一般设置为false
            fileElementId: name, // 上传文件的id、name属性名
            dataType: 'JSON', //返回值类型，一般设置为json、application/json
            success:function(res,status){
                fnGroup.success && fnGroup.success(res,status);
                fnGroup.end && fnGroup.end(res,status);
                // var fileInput = $("#"+name),html = fileInput.prop('outerHTML'),p = fileInput.parent();
                // fileInput.remove();
                // p.append(html);
            },
            error: function(res,status){
                fnGroup.error && fnGroup.error(res,status);
                fnGroup.end && fnGroup.end(res,status);
            }
        });
    };

    //除法函数，用来得到精确的除法结果
    //说明：javascript的除法结果会有误差，在两个浮点数相除的时候会比较明显。这个函数返回较为精确的除法结果。
    //调用：_h.Div(arg1,arg2)
    //返回值：arg1除以arg2的精确结果
    $h.div = function(arg1,arg2){
        var t1=0,t2=0,r1,r2;
        try{t1=arg1.toString().split(".")[1].length;}catch(e){}
        try{t2=arg2.toString().split(".")[1].length;}catch(e){}
        with(Math){
            r1=Number(arg1.toString().replace(".",""));
            r2=Number(arg2.toString().replace(".",""));
            return (r1/r2)*pow(10,t2-t1);
        }
    };
    //乘法函数，用来得到精确的乘法结果
    //说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。
    //调用：_h.Mul(arg1,arg2)
    //返回值：arg1乘以arg2的精确结果
    $h.Mul = function(arg1,arg2) {
        var m=0,s1=arg1.toString(),s2=arg2.toString();
        try{m+=s1.split(".")[1].length}catch(e){}
        try{m+=s2.split(".")[1].length}catch(e){}
        return Number(s1.replace(".","")) * Number(s2.replace(".","")) / Math.pow(10,m);
    };

    //加法函数，用来得到精确的加法结果
    //说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的加法结果。
    //调用：_h.Add(arg1,arg2)
    //返回值：arg1加上arg2的精确结果
    $h.Add = function(arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        return (arg1*m+arg2*m)/m;
    };

    //加法函数，用来得到精确的减法结果
    //说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的减法结果。
    //调用：_h.Sub(arg1,arg2)
    //返回值：arg1减去arg2的精确结果
    $h.Sub = function(arg1,arg2){
        var r1,r2,m,n;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        //动态控制精度长度
        n=(r1>=r2)?r1:r2;
        return ((arg1*m-arg2*m)/m).toFixed(n);
    };
    $h.cookie = function(key,val,time) {
        if(val == undefined){
            return _helper.getCookie(key);
        }else if(val == null){
            return _helper.delCookie(key);
        }else{
            return _helper.setCookie(key,val,time);
        }
    };
    //操作cookie
    $h.setCookie = function(key,val,time){//设置cookie方法
        var date=new Date(); //获取当前时间
        if(!time) time = 1;  //将date设置为n天以后的时间
        date.setTime(date.getTime()+time*24*3600*1000); //格式化为cookie识别的时间
        document.cookie=key + "=" + val +";expires="+date.toGMTString();  //设置cookie
    };
    
    $h.getCookie = function(key) {//获取cookie方法
        /*获取cookie参数*/
        var getCookie = document.cookie.replace(/;[ ]/g, ";");  //获取cookie，并且将获得的cookie格式化，去掉空格字符
        var arrCookie = getCookie.split(";");  //将获得的cookie以"分号"为标识 将cookie保存到arrCookie的数组中
        var tips;  //声明变量tips
        for (var i = 0; i < arrCookie.length; i++) {   //使用for循环查找cookie中的tips变量
            var arr = arrCookie[i].split("=");   //将单条cookie用"等号"为标识，将单条cookie保存为arr数组
            if (key == arr[0]) {  //匹配变量名称，其中arr[0]是指的cookie名称，如果该条变量为tips则执行判断语句中的赋值操作
                tips = arr[1];   //将cookie的值赋给变量tips
                break;   //终止for循环遍历
            }
        }
        return tips;
    };

    $h.delCookie = function(key){ //删除cookie方法
        var date = new Date(); //获取当前时间
        date.setTime(date.getTime()-10000); //将date设置为过去的时间
        document.cookie = key + "=v; expires =" +date.toGMTString();//设置cookie
    };

    $pushCookie = function(key){
        var data = $h.getCookie(key);
        $h.delCookie(key);
        return data;
    };

    $h.getParmas = function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return decodeURI(r[2]); return null; //返回参数值
    };

    $h.U = function(opt){
        var m = opt.m || 'wap',c = opt.c || 'public_api', a = opt.a || 'index',q = opt.q || '',p = opt.p || {},_params = '',gets='',ext=opt.ext || '.html';
        _params = Object.keys(p).map(function(key){
            return key+'/'+p[key];
        }).join('/');
        if(typeof q=='object') gets = Object.keys(q).map(function (key) {
            return key + '=' + q[key];
        }).join('&');
        return "/"+m+"/"+c+"/"+a+(_params == '' ? '' : '/'+_params)+ext+(gets == '' ? q : '?'+gets);
    };

    $h.isLogin = function(){
        return !!$h.getCookie('is_login');
    };
    $h.SplitArray=function(list,sp){
        if (typeof list!='object') return [];
        if (sp === undefined) sp=[];
        for (var i = 0; i < list.length;i++){
            sp.push(list[i]);
        }
        return sp;
    }

    $h.EventUtil ={
        addHandler: function (element, type, handler) {
            if (element.addEventListener)
                element.addEventListener(type, handler, false);
            else if (element.attachEvent)
                element.attachEvent("on" + type, handler);
            else
                element["on" + type] = handler;
        },
        removeHandler: function (element, type, handler) {
            if(element.removeEventListener)
                element.removeEventListener(type, handler, false);
            else if(element.detachEvent)
                element.detachEvent("on" + type, handler);
            else
                element["on" + type] = handler;
        },
        /**
         * 监听触摸的方向
         * @param target            要绑定监听的目标元素
         * @param isPreventDefault  是否屏蔽掉触摸滑动的默认行为（例如页面的上下滚动，缩放等）
         * @param upCallback        向上滑动的监听回调（若不关心，可以不传，或传false）
         * @param rightCallback     向右滑动的监听回调（若不关心，可以不传，或传false）
         * @param downCallback      向下滑动的监听回调（若不关心，可以不传，或传false）
         * @param leftCallback      向左滑动的监听回调（若不关心，可以不传，或传false）
         */
        listenTouchDirection: function (target, upCallback,isPreventDefault, rightCallback, downCallback, leftCallback) {
            this.addHandler(target, "touchstart", handleTouchEvent);
            this.addHandler(target, "touchend", handleTouchEvent);
            this.addHandler(target, "touchmove", handleTouchEvent);
            var startX;
            var startY;
            function handleTouchEvent(event) {
                switch (event.type){
                    case "touchstart":
                        startX = event.touches[0].pageX;
                        startY = event.touches[0].pageY;
                        break;
                    case "touchend":
                        var spanX = event.changedTouches[0].pageX - startX;
                        var spanY = event.changedTouches[0].pageY - startY;
                        if(Math.abs(spanX) > Math.abs(spanY)) {
                            if (spanX > 30)
                                rightCallback && rightCallback();
                            else if (spanX < -30)
                                leftCallback && leftCallback();
                        }else {
                            if (spanY > 30)
                                downCallback && downCallback();
                            else if (spanY < -30) {
                                upCallback && upCallback();
                            }
                        }
                        break;
                    case "touchmove":
                        // if (event.cancelable) if(!event.defaultPrevented && !isPreventDefault) event.preventDefault();
                        break;
                }
            }
        }
    };

    return $h;

});