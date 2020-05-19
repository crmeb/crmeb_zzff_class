(function (global) {
    var RequestAxios={
        baseGet:function(url,successCallback,errorCallback,isMsg){
            axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200){
                    successCallback && successCallback(res.data);
                }else{
                    var err = res.data.msg || '请求失败!';
                    errorCallback && errorCallback(err);
                    isMsg || layer.open({content: err,skin: 'msg',time: 2});
                }
            }).catch(function(err){
                errorCallback && errorCallback(err);
                layer.open({content: err,skin: 'msg',time: 2});
            });
        },
        basePost:function(url,data,successCallback,errorCallback,isMsg){
            axios.post(url,data).then(function(res){
                if(res.status == 200 && res.data.code == 200){
                    successCallback && successCallback(res.data);
                }else{
                    var err = res.data.msg || '请求失败!';
                    errorCallback && errorCallback(err);
                    isMsg || layer.open({content: err,skin: 'msg',time: 2});
                }
            }).catch(function(err){
                errorCallback && errorCallback(err);
                layer.open({content: err,skin: 'msg',time: 2});
            });
        },
        Url :function (opt) {
            var m = opt.m || 'wap', c = opt.c || '', a = opt.a || 'index', q = opt.q || '',
                p = opt.p || {}, params = '',gets='';
            params = Object.keys(p).map(function (key) {
                return key + '/' + p[key];
            }).join('/');
            gets = Object.keys(q).map(function (key) {
                return key+'='+ q[key];
            }).join('&');
            return '/' + m + '/' + c + '/' + a + (params == '' ? '' : '/' + params) + (gets == '' ? '' : '?' + gets);
        }
    };

    global.RequestAxios=RequestAxios
    window.Url = RequestAxios.Url;
    return RequestAxios;
}(this))