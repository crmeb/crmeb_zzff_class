(function (global) {
    var socketDebug = window.socketDebug == undefined ? false : window.socketDebug, port = window.workermanConfig === undefined ? '20005' : window.workermanConfig.port;
    window.uid = window.uids === undefined ? 0 : window.uids;
    window.room = window.room === undefined ? 0 : window.room;
    var socket = {
        ws: null,
        connect: function () {
            var that = this;
            that.ws = new WebSocket("ws://" + document.domain + ":" + port);//这里如果使用127.0.0.1或者localhost会出现连接失败。当时为了方便以后的维护，这里在php的全局文件里定义了一个常量来定义ip，后来本地开发完提交到linux服务器环境之后发现链接失败！按照此行代码会有效连接~
            that.ws.onopen = this.onopen;
            that.ws.onmessage = this.onmessage;
            that.ws.onclose = function (e) {
                socketDebug && console.log("连接关闭，定时重连");
                that.connect();
            };
            that.ws.onerror = function (e) {
                socketDebug && console.log("出现错误");
            };
        },
        onopen: function () {
            var joint = '{"type":"handshake","role":"user","uid":' + window.uid + ',"room":' + window.room + '}';
            socket.ws.send(joint);
            socket.heartCheck.start();
        },
        sendMsg: function (content, type, id) {
            socket.ws.send("{content:'" + content + "',m_type:'" + type + "',room:" + id + ",type:'send',uid:" + window.uid + "}")
        },
        onmessage: function (e) {
            try {
                var data = JSON.parse(e.data);
                socketDebug && console.log(data)
                switch (data.type) {
                    case 'init':
                        break;
                    // 服务端ping客户端
                    case 'ping':
                        console.log("我去心跳检测了：" + data);
                        break;
                    // 登录 更新用户列表
                    case 'handshake':
                        break;
                    // 提醒
                    case 'reception':
                        break;
                    //直播进行中
                    case 'live_ing':
                        vm.changLive(true, data.pull_url);
                        break;
                    //直播结束
                    case 'live_end':
                        vm.changLive(false);
                        break;
                    //消息提醒
                    case 'message':
                        vm.setCommentArea(data.message, data.m_type, data.userInfo, data.user_type, data.id);
                        break;
                    //消息撤回
                    case 'recall':
                        vm.CommentRecall(data.id);
                        break;
                    case 'ban':
                        vm.setBanUser(data.value);
                        break;
                    case "room_user_count":
                        vm.setUserCount(data.onLine_user_count, data.notice_content, data.user_type);
                        break;
                    // 打赏
                    case "live_reward":
                        vm.setGiftFloat(data);
                        break;
                }
            } catch (e) {
                socketDebug && console.info(e);
            }
        },
        heartCheck: {
            timeout: 3000,
            timeoutObj: null,
            start: function () {
                this.timeoutObj = setInterval(function () {
                    socket.ws.send("{'type':'ping'}");
                }, this.timeout);
            },
            /* countRoomUser: function(){
                 var con = '{"type":"room_user_count","uid":'+window.uid+',"room":'+window.room+'}';
                 socket.ws.send(con);
             }*/
        },
    };

    // window.onload=function () {
    socket.connect();
    //};

    global.socket = socket;

    return socket
}(this));