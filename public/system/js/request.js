var headers = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'X-Requested-With': 'XMLHttpRequest',
};

var requestApi = function (url, data, type) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            headers: headers,
            url: url,
            data: data,
            type: type || "post",
            dataType: 'json',
            success: function (rem) {
                if (rem.code == 200 || rem.status == 200)
                    resolve(rem);
                else
                    reject(rem);
            },
            error: function (err) {
                reject(err);
            }
        })
    });
}

window.requestGet = function (url, data) {
    return requestApi(url, data, 'get');
}

window.requestGet = function (url, data) {
    return requestApi(url, data, 'post');
}

window.getUrl = function (opt) {
    var m = opt.m || window.module, c = opt.c || window.controlle, a = opt.a || 'index', q = opt.q || '',
        p = opt.p || {}, params = '',gets='';
    params = Object.keys(p).map(function (key) {
        return key + '/' + p[key];
    }).join('/');
    gets = Object.keys(q).map(function (key) {
        return key+'='+ q[key];
    }).join('&');

    return '/' + m + '/' + c + '/' + a + (params == '' ? '' : '/' + params) + (gets == '' ? '' : '?' + gets);
}
