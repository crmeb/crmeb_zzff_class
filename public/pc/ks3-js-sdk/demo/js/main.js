(function(){
    //选择符API   note: suite for IE 9+
    var $ = document.querySelectorAll.bind(document);
    //事件监听
    Element.prototype.on = Element.prototype.addEventListener;
    //在NodeList对象上通过forEach部署监听函数
    NodeList.prototype.on = function (event, fn) {
        // this 为 NodeList
        [].forEach.call(this, function (el) {
            el.on(event, fn);
        });
        return this;
    };

    /**
     * 上传文件示例
     * @type {HTMLElement}
     */

    Ks3.config.AK = '+vB0RuO22DeeanAAb0ig';  //TODO： 请替换为您的AK
    Ks3.config.SK = '3kbTp8pg+LymO84/ZyxG/od/3FeVDjWt+8/m5iBc'; //TODO: 测试时请填写您的secret key  注意：前端计算signature不安全

    Ks3.config.region = 'BEIJING';  //TODO: 需要设置bucket所在region， 如杭州region： HANGZHOU,北京region：BEIJING，香港region：HONGKONG，上海region: SHANGHAI ，美国region:AMERICA ；如果region设置和实际不符，则会返回301状态码； region的endpoint参见：http://ks3.ksyun.com/doc/api/index.html
    Ks3.config.bucket = 'teaseyoulearn'; // TODO : 设置默认bucket name
    var bucketName = Ks3.config.bucket;     //TODO: 请替换为您需要上传文件的bucket名称



    var filelistNode = document.getElementById('filelist');

    /*
     *  如果bucket不是公开读写的，需要先鉴权，即提供policy和signature表单
     *  policy的conditions中需要指明请求体的form中用户添加的字段
     */
    var policy = {
        "expiration": new Date(getExpires(3600)*1000).toISOString(), //一小时后
        "conditions": [
            ["eq","$bucket", bucketName],
            ["starts-with", "$key", ""],
            ["starts-with","$acl", "public-read"],
            ["starts-with", "$name", ""],   //表单中传了name字段，也需要加到policy中
            ["starts-with", "$x-kss-meta-custom-param1",""],
            ["starts-with", "$x-kss-newfilename-in-body",""],//必须只包含小写字符
            ["starts-with", "$Cache-Control",""],
            ["starts-with", "$Expires", ""],
            ["starts-with", "$Content-Disposition", ""],
            ["starts-with", "$Content-Type",""],
            ["starts-with", "$Content-Encoding",""]
        ]
    };
    //policy stringify再经过BASE64加密后的字符串（utf8编码格式）
    var stringToSign = Ks3.Base64.encode(JSON.stringify(policy));

    //建议从后端sdk获取signature签名  算法为：Signature = Base64(HMAC-SHA1(YourSecretKey, stringToSign ) );
    var signatureFromPolicy = Ks3.b64_hmac_sha1(Ks3.config.SK, stringToSign);
    console.log('signatureFromPolicy:' + signatureFromPolicy);


    var ks3UploadUrl;
    //支持https 上传
    if (window.location.protocol === 'https:') {
        Ks3.config.protocol = 'https';
    } else {
        Ks3.config.protocol = 'http';
    }
    ks3UploadUrl =  Ks3.config.protocol + '://' + Ks3.ENDPOINT[Ks3.config.region] + '/';

    var ks3Options = {
        KSSAccessKeyId: Ks3.config.AK,
        policy: stringToSign,
        signature: signatureFromPolicy,
        bucket_name: bucketName,
        key: '${filename}',
        acl: "public-read",
        uploadDomain: ks3UploadUrl  + bucketName,
        autoStart: false,
        'x-kss-meta-custom-param1': 'Hello',
        'x-kss-newfilename-in-body': true,
        'Cache-Control': 'max-age=600',                //设置缓存多少秒后过期
        'Expires': new Date(getExpires(600) * 1000),   //设置缓存过期时间
        'Content-Disposition' :'attachment;filename=', // 触发浏览器下载文件
        //'Content-Type' :' application/octet-stream',
        onUploadProgressCallBack: function(uploader, obj){
            var itemNode = document.getElementById(obj.id);
            var resultNode = itemNode.querySelector('span');
            resultNode.innerHTML = obj.percent + "%";
        },
        onFileUploadedCallBack: function(uploader, obj){ //obj是当前上传的文件对象
            var itemNode = document.getElementById(obj.id);
            var resultNode = itemNode.querySelector('span');
            resultNode.innerHTML = "完成";
            //显示上传的文件的链接
            var linkNode = itemNode.querySelector('a');
            linkNode.href = ks3Options.uploadDomain + "/" + obj.name;
            linkNode.innerHTML = obj.name;

            //增加加水印按钮
            var adpBtn = document.createElement("button");
            adpBtn.innerHTML = '添加水印';
            adpBtn.onclick = function(){
                var objKey =  encodeURIComponent(obj.name);
                var url = ks3UploadUrl + bucketName + '/' + objKey + '?adp' ;
                var kssHeaders = {
                    'kss-async-process': 'tag=imgWaterMark&type=2&dissolve=65&gravity=NorthEast&text=6YeR5bGx5LqR&font=5b6u6L2v6ZuF6buR&fill=I2JmMTcxNw==&fontsize=500&dy=10&dx=20|tag=saveas&bucket=' + bucketName + '&object=imgWaterMark-' + obj.name,
                    'kss-notifyurl': 'http://10.4.2.38:19090/'
                };
                var signature = Ks3.generateToken(Ks3.config.SK, bucketName, objKey + '?adp', 'PUT','', kssHeaders, '');
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4) {
                        if(xhr.status >= 200 && xhr.status < 300 || xhr.status == 304){
                            alert("put请求成功");
                            var waterMarkImgLink = document.createElement('a');
                            waterMarkImgLink.setAttribute('target','_blank');
                            waterMarkImgLink.style.marginLeft = "30px";
                            var processedImgName = "imgWaterMark-" + obj.name;
                            waterMarkImgLink.innerHTML = processedImgName;

                            //10分钟后的时间戳
                            var  timeStamp = getExpires(600);

                            //根据Expires过期时间戳计算外链signature
                            var expiresSignature = Ks3.generateToken(Ks3.config.SK, bucketName, processedImgName, 'GET', '' ,kssHeaders, timeStamp);
                            setTimeout(function(){ //异步任务，等1秒再看处理结果
                                waterMarkImgLink.href = ks3UploadUrl + bucketName + '/imgWaterMark-' + obj.name + '?KSSAccessKeyId=' +  encodeURIComponent(Ks3.config.AK) + '&Expires=' + timeStamp + '&Signature=' + encodeURIComponent(expiresSignature);
                                itemNode.appendChild(waterMarkImgLink);
                            },1000);

                        } else{
                            //alert('Request was unsuccessful: ' + xhr.status);
                        }
                    }
                };

                xhr.open("put", url, true);

                xhr.setRequestHeader('Authorization','KSS ' + Ks3.config.AK + ':' + signature );
                xhr.setRequestHeader('kss-async-process', kssHeaders['kss-async-process']);
                xhr.setRequestHeader('kss-notifyurl',kssHeaders['kss-notifyurl']); //替换成您接收异步处理任务完成通知的url地址
                xhr.send(null);
            };
            itemNode.appendChild(adpBtn);

        },
        onFilesAddedCallBack: function(uploader, objArray){ // objArray是等待上传的文件对象的数组
            for (var i = 0 ; i < objArray.length ; i++){
                var itemNode = document.createElement("li");
                itemNode.innerHTML = objArray[i].name + "<span style='margin:5px 20px;'></span><a style='margin-right: 20px;' target='_blank'></a>";
                itemNode.id = objArray[i].id;
                filelistNode.appendChild(itemNode);
            }

        },
        onBeforeUploadCallBack: function(uploader, obj) {
            //可以在这里更新object key
            //var newObjectKey = 'yourRenamedFileName_' + obj.name;
            //uploader.settings.multipart_params['key'] =  newObjectKey;
            //obj.name = newObjectKey;
        },
        onErrorCallBack: function(uploader, errObject){
            if(errObject.status === 413 || errObject.status === 415){
                var responseXML = Ks3.parseStringToXML(errObject.response );
                alert(Ks3.xmlToJson(responseXML)['Error']['Message']);
            }else{
                alert(errObject.code + " : Error happened in uploading " + errObject.file.name + " ( " + errObject.message + " )");
            }
        }
    };

    var pluploadOptions = {
        browse_button: 'browse', //触发对话框的DOM元素自身或者其ID
        drop_element: document.body, //指定了使用拖拽方式来选择上传文件时的拖拽区域，即可以把文件拖拽到这个区域的方式来选择文件。该参数的值可以为一个DOM元素的id,也可是DOM元素本身，还可以是一个包括多个DOM元素的数组。如果不设置该参数则拖拽上传功能不可用。目前只有html5上传方式才支持拖拽上传。
        filters: {
            mime_types : [ //只允许上传图片和zip文件
                { title : "Video files", extensions : "mp4,mov,qt,ts,rmvb,rm,avi,flv,mkv,wmv,mpg,mpeg,m2v,m4v,3gp,3g2,webm,vob,ogv,ogg" }
            ],
            max_file_size : '2gb', //最大只能上传2GB的文件
            prevent_duplicates : true //不允许选取重复文件
        }
    }

    var tempUpload = new ks3FileUploader(ks3Options, pluploadOptions);

    document.getElementById('start-upload').onclick = function (){
    	console.log("start...");
        tempUpload.uploader.start();
    }



    /**
     * GET Bucket （List Objects)
     *  获取bucket（空间）中object（文件对象）示例
     *  参见：http://ks3.ksyun.com/doc/api/bucket/get.html
     */
    document.getElementById('get-bucket').onclick = function() {
        Ks3.listObject({
            'max-keys': '15'
        },function(json) {
            document.getElementById('responsexml').innerHTML = JSON.stringify(json, null, 4);
        });

    };

    /**
     *  Delete Object
     *  删除指定文件
     */
    (function listObjects() {

        Ks3.listObject(
            {
                Bucket: bucketName,
                'max-keys': '10',
                delimiter:'|'
            },function(json) {
                /**
                 * 以表格展示bucket中的object
                 */
                var tableEle = document.getElementById('exampleForDeleteFile');
                var objectArray =  json['ListBucketResult']['Contents'];
                for(var i= 0, len = objectArray.length; i< len; i++) {
                    var item = document.createElement("tr");
                    var objKey = objectArray[i]['Key'];
                    item.id = objKey;
                    item.innerHTML = '<td>' + objKey + '</td><td>' + objectArray[i]['Size']/1024 + ' KB </td>' + '<td>' + new Date(objectArray[i]['LastModified']).toLocaleString() + '</td>' + '<td class="del-opt">删除</td>';
                    tableEle.appendChild(item);
                };
                $('.del-opt').on('click', function(e) {
                    var key = this.parentNode.firstChild.innerHTML;
                    Ks3.delObject(
                        {
                            Key: key
                        }, function(status) {
                            if( status === 204) {
                                alert( key + " 删除成功");
                                var ele = document.getElementById(key);
                                ele.parentNode.removeChild(ele);
                            }
                        });
                });
            });
    })();


    /**
     *  PUT Object 上传触发处理示例(上传图片增加水印,后端计算签名，转发请求到Ks3 API）
     *  参见：http://ks3.ksyun.com/doc/api/async/trigger.html
     *  注： 这里使用了FormData序列化表单中选取的文件，XMLHttpRequest 2级定义了FormData类型，
     *  支持的浏览器有Firefox 4+，Safari 5+，Chrome 和 Android 3+版的WebKit
     */

    document.getElementById('utp').onclick = function() {
        var imgFile = document.getElementById('imgFile').files[0]; //获取文件对象
        var formData = new FormData();
        var objKey = imgFile.name;
        formData.append("key", objKey);
        formData.append("file", imgFile);

        //10分钟后的时间戳，以秒为单位
        var  timeStamp = getExpires( 10 * 60 );

        var url = 'http://127.0.0.1:3000/' + bucketName + '?t=' + timeStamp ;
        var kssHeaders = {
            'kss-async-process': 'tag=imgWaterMark&type=2&dissolve=65&gravity=NorthEast&text=6YeR5bGx5LqR&font=5b6u6L2v6ZuF6buR&fill=I2JmMTcxNw==&fontsize=500&dy=10&dx=20|tag=saveas&bucket=' + bucketName + '&object=imgWaterMark-' + objKey,
            'kss-notifyurl': 'http://10.4.2.38:19090/',
            'x-kss-storage-class' : 'STANDARD'   // STANDARD | STANDARD_IA 即标准存储和低频访问存储（主要用于备份）
        };

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if(xhr.status >= 200 && xhr.status < 300 || xhr.status == 304){
                    alert("上传触发处理成功");
                    var waterMarkImg = document.getElementById('display-adp-result').firstChild;
                    console.log('Signature:' + xhr.responseText);

                    console.log('timestamp:' + timeStamp);
                    waterMarkImg.src = 'http://kss.ksyun.com/' + bucketName + '/imgWaterMark-' + objKey + '?KSSAccessKeyId=' +  encodeURIComponent(Ks3.config.AK) + '&Expires=' + timeStamp + '&Signature=' + encodeURIComponent(xhr.responseText);
                }else{
                    //alert('Request was unsuccessful: ' + xhr.status);
                }
            }
        };

        function progressFunction(e) {
            var progressBar = document.getElementById("progressBar");
            if (e.lengthComputable) {
                progressBar.max = e.total;
                progressBar.value = e.loaded;
            }
        }
        xhr.upload.addEventListener("progress", progressFunction, false);
        xhr.open("put", url, true);

        //xhr.setRequestHeader('Content-Length',imgFile.size);

        xhr.setRequestHeader('Authorization','KSS ' + Ks3.config.AK );
        xhr.setRequestHeader('kss-async-process', kssHeaders['kss-async-process']);
        xhr.setRequestHeader('kss-notifyurl',kssHeaders['kss-notifyurl']); //替换成您接收异步处理任务完成通知的url地址
        xhr.setRequestHeader('x-kss-storage-class', kssHeaders['x-kss-storage-class']);
        xhr.send(formData);
    };

    /**
     *  PUT Object 上传文件
     *  前端计算signature，put请求直传到ks3 API
     *  注意：前端计算signature容易泄露SK, 只用于调试，生产环境需要从后端获取签名，作为第一个参数params的Signature属性传入sdk api函数
     *
     */
    document.getElementById('utp2').onclick = function() {
        var file = document.getElementById('imgFile2').files[0]; //获取文件对象
        var objKey = Ks3.encodeKey(file.name);
        var contentType = file.type;

        Ks3.putObject({
            Key: 'image/'+objKey,
            File: file,
            ACL: 'public-read',
            ProgressListener: progressFunction,
            Sinature: ''
        },function(err) {
            if(err) {
                alert(JSON.stringify(err));
            }else{
                alert('put上传成功');
            }
        });

        function progressFunction(e) {
            var progressBar = document.getElementById("progressBar2");
            if (e.lengthComputable) {
                progressBar.max = e.total;
                progressBar.value = e.loaded;
            }
        }

    };


    /**
     * 下载文件（支持断点续传）
     */
    document.getElementById('downloadBigFileBtn').onclick = function() {
        blockDownload(
            {
                Bucket: 'sanrui',
                Key:'jssdk/book.pdf'
            },
            function(err, results) {
                if(err) {
                    console.log(err);
                }
                console.log(JSON.stringify(results));
            });
    }

    document.getElementById('stopDownload').onclick = function() {
        Ks3.config.stopFlag = true;
    };


    /**
     * 上传文件（支持断点续传）
     */
    var fileToBeUpload; //被上传的文件的文件名
    document.getElementById('uploadBigFile').onclick = function() {
        /**
         * 文件名，最后修改时间，bucket和object key都没变化的情况，续传
         */
        var file = document.getElementById('bigFile').files[0];
        fileToBeUpload = file.name;

        //设置分块大小
        //Ks3.config.chunkSize = 6*1024*1024; //最小（默认）为5 MB，增大分块会增加部分浏览器崩溃的风险

        multipartUpload({
            Bucket: bucketName,
            Key: fileToBeUpload,
            //region: 'BEIJING',
            ACL: 'public-read',
            //ContentType : 'video/mp4',
            File: file,
            TotalSize: file.size,
            Signature: '',
        }, function(err, res){
            if(err) {
                if(err.msg != 'stop') {
                    console.error(err);
                    alert(err.msg);
                }else{
                    console.log(err);
                }
            }else{
                console.log('res: ' + JSON.stringify(res));
            }
        });

    }

    document.getElementById('suspendMultipartUpload').onclick = function() {
        Ks3.config.stopFlag = true;
    };

    document.getElementById('cancelMultipartUpload').onclick = function() {
        //前端暂停
        Ks3.config.stopFlag = true;
        //通知ks3取消上传
        if(Ks3.config.currentUploadId) {
            Ks3.abort_multipart_upload({
                Bucket: bucketName,
                Key:fileToBeUpload,
                UploadId: Ks3.config.currentUploadId
            },function(err,res) {
                if(err) {
                    console.log(err);
                }else{
                    console.log('res: ' + JSON.stringify(res));

                    //清理前端缓存与重置界面进度条
                    var len = localStorage.length;
                    for(var i=0; i< len; i++) {
                        var itemKey = localStorage.key(i);
                        //自动创建一个临时String对象封装itemKey在IE下会导致内存泄露，故显示转换
                        if(typeof itemKey  === 'string'  && (new String(itemKey)).endWith(bucketName + '-' + Ks3.encodeKey(fileToBeUpload))) {
                            localStorage.removeItem(itemKey);
                        }
                    }
                    var progressBar = document.getElementById("multipartUploadProgressBar");
                    progressBar.value = 0;
                }
            });
        }
    };

})();


/**
 * 上传文件
 * 根据文件大小,进行简单上传和分块上传
 * @param params
 * {
 *    Bucket: '' not required, bucket name
 *    Key: ''    Required   object key
 *    region : '' not required  bucket所在region
 *    ContentType: ''  not required  content type of object key
 *    ACL: ''   not required   private | public-read
 *    File: object  required, 需要上传的文件
 *    TotalSize: not required, 需要限定文件总大小时使用
 *    Signature: ''  not required, 请求签名,从服务端获取
 *    callbackurl: '' not required, 回调url
 *    callbackbody: '' not require, 回调自定义参数
 * }
 * @param cb
 */

function multipartUpload (params, cb) {

    var config;
    var bucketName = params.Bucket || Ks3.config.bucket || '';
    var key = params.Key || params.File.name;
    key = Ks3.encodeKey(key);
    var region = params.region || Ks3.config.region;
    if (region ) {
        Ks3.config.baseUrl =  Ks3.ENDPOINT[region];
    }
    var file = params.File;
    var totalSize = file.size; //文件总大小
    var progressKey = getProgressKey(file.name, file.lastModified, bucketName, key);
    console.log(progressKey);

    // 会根据文件大小,进行简单上传和分块上传
    var contentType = params.ContentType || '';

    var progressBar = document.getElementById("multipartUploadProgressBar");
    // 分块上传
    console.log(async);
    async.auto({
            /**
             * 初始化配置文件,如果没有就新建一个
             */
            init: function(callback) {
                //重置暂停标识
                Ks3.config.stopFlag = false;

                if ( !localStorage[progressKey]) {
                    configInit(file, progressKey, function(err) {
                        callback(err);
                    })
                } else {
                    callback(null);
                }

            },
            show: ['init', function(callback) {
                console.log('  开始上传文件: ' + progressKey)
                config = JSON.parse(localStorage.getItem(progressKey));

                progressBar.max = config['count'];
                progressBar.value = config['index'];
                callback(null);
            }],
            /**
             * 获取uploadId,如果有就直接读取,没有就从服务器获取一个
             */
            getUploadId: ['init', function(callback) {
                config = JSON.parse(localStorage.getItem(progressKey));
                var uploadId = config['uploadId'];

                if ( !! uploadId) {
                    callback(null, uploadId);
                } else {
                    Ks3.multitpart_upload_init(params, function(err, uploadId) {
                        if(err) {
                            callback(err);
                        }else {
                            config['uploadId'] = uploadId;
                            localStorage.setItem(progressKey, JSON.stringify(config));
                            callback(null, uploadId);
                        }
                    });
                }
            }],
            /**
             * 对文件进行上传
             * 上传后要把信息写到本地存储配置文件中
             * 如果都上传完了,就把相关本地存储信息删除
             * 并通知服务器,合并分块文件
             */
            upload: ['getUploadId', function(callback, result) {
                if(result.getUploadId) {
                    var uploadId = result.getUploadId;
                    Ks3.config.currentUploadId = uploadId;

                    config = JSON.parse(localStorage.getItem(progressKey));
                    var count = config['count'];
                    var index = config['index'];
                    var chunkSize = config['chunkSize'];
                    var currentRetries = config['retries'];

                    up();
                }else {
                    callback({'msg':'no uploadId'});
                }

                // 在报错的时候重试
                function retry(err) {
                    console.log('upload ERROR:', err);
                    if (currentRetries > Ks3.config.retries) {
                        throw err;
                    } else {
                        currentRetries = currentRetries + 1;
                        config['retries'] = currentRetries;
                        localStorage.setItem(progressKey, JSON.stringify(config));
                        console.log('第 ' + currentRetries + ' 次重试');
                        up();
                    }
                }
                // 真正往服务端传递数据
                function up() {
                    console.log('正在上传 ', 'index: ' + index);
                    var start = (index - 1) * chunkSize;
                    // 判断是否已经全部都传完了
                    if (index <= count) {
                        getFileContent(file, chunkSize, start, function(body) {
                            delete params.File;
                            params.UploadId = uploadId;
                            params.PartNumber = index;
                            params.body = body;
                            params.type = contentType;
                            console.log('正在上传第 ', index, ' 块,总共: ', + count + ' 块');

                            try {
                                Ks3.upload_part(params, function(err, partNumber, etag) {
                                    if (err) {
                                        if(err.status == 413 || err.status == 415) {
                                            callback(err);
                                        }else {
                                            retry(err);
                                        }
                                    } else {
                                        if(!Ks3.config.stopFlag) {
                                            config['index'] = index;
                                            progressBar.value = config['index'];
                                            config['etags'][index] = etag;
                                            localStorage.setItem(progressKey, JSON.stringify(config));
                                            index = index + 1;
                                            up();
                                        }else {
                                            callback({
                                                msg: "stop"
                                            });
                                        }
                                    }
                                });
                            } catch(e) {
                                retry(e);
                            }
                        })
                    } else {
                        console.log('发送合并请求');
                        delete params.File;
                        params.UploadId = uploadId;
                        params.body = generateCompleteXML(progressKey);

                        Ks3.upload_complete(params, function(err, res) {
                            if (err) throw err;
                            callback(err, res);
                        })
                    }
                };

            }]
        },
        function(err, results) {
            if (err) {
                //throw err;
            }else{
                //删除配置
                localStorage.removeItem(progressKey);
            }
            if (cb) {
                cb(err, results);
            }
        });

}

/**
 * 计算用于记录上传任务进度的key
 * @param name
 * @param lastModified
 * @param bucket
 * @param key
 */
function getProgressKey(name, lastModified, bucket, key) {
    var result = name + "-" + lastModified + "-" + bucket + "-" + key;
    return result;
}

/**
 * 把配置信息写到localStorage里,作为缓存
 * @param file 上传文件的句柄
 * @param progressKey  文件上传进度缓存在localStorage中的标记key
 * @param cb
 */
function configInit(file, progressKey, cb) {
    var fileSize = file.size;
    var count = parseInt(fileSize / Ks3.config.chunkSize) + ((fileSize % Ks3.config.chunkSize == 0 ? 0: 1));

    if (count == 0) {
        cb({
            msg: 'The file is empty.'
        })
    } else {
        config = {
            name: file.name,
            size: fileSize,
            chunkSize: Ks3.config.chunkSize,
            count:count,
            index: 1,
            etags:{},
            retries: 0
        }
        localStorage.setItem(progressKey, JSON.stringify(config));
        if(cb) {
            cb(null);
        }
    }
}

/**
 * 获取指定的文件部分内容
 */
function getFileContent(file, chunkSize, start, cb) {
    var start = start;
    var bufferSize = file.size;
    var index = start / chunkSize;
    console.log('正在读取下一个块的文件内容 index:' + index);
    if (start + chunkSize > bufferSize) {
        chunkSize = bufferSize - start;
    }
    console.log('分块大小:', chunkSize);

    if(file.slice) {
        var blob = file.slice(start, start + Ks3.config.chunkSize);
    }else if(file.webkitSlice) {
        var blob = file.webkitSlice(start, start + Ks3.config.chunkSize);
    }else if(file.mozSlice) {
        var blob = file.mozSlice(start, start + Ks3.config.chunkSize);
    }else{
        throw new Error("blob API doesn't work!");
    }

    var reader = new FileReader();
    reader.onload = function(e) {
        cb(e.target.result);
    };
    reader.readAsArrayBuffer(blob);
}

/**
 * 生成合并分块上传使用的xml
 */
function generateCompleteXML(progressKey) {
    var content = JSON.parse(localStorage.getItem(progressKey));
    var index = content.index;
    var str = '';
    if (index > 0) {
        str = '<CompleteMultipartUpload>';
        for (var i = 1; i <= index; i++) {
            str += '<Part><PartNumber>' + i + '</PartNumber><ETag>' + content.etags[i] + '</ETag></Part>'
        }
        str += '</CompleteMultipartUpload>';
    }
    return str;
}




/**
 * 断点续传下载
 * 文件分片下载进度存储于localStorage（以filePath做关键字索引文件）
 * @param {object} params
 * {
 *      Bucket: '' not required, bucket name
 *      Key   : ''   Required ,   object key
 *      filePath: '' Required,    file path to save
 *      chunk: ''  not required, chunk size by bytes, default is 100 * 1024 B
 * }
 */
function blockDownload (params, cb) {
    var bucketName = params.Bucket || Ks3.config.bucket;
    // 这个地方不能进行encode
    var key = params.Key;
    var filePath =  params.filePath || bucketName + '-' + key.replace('/','-') ; //因为浏览器文件系统不能在不存在的目录下直接创建文件，故将目录转为文件名的前缀

    if (!key) {
        alert('require the object Key');
    }

    var TEMP_SPACE = 1024 * 1024 * 1024 ; // 1GB


    window.requestFileSystem  = window.requestFileSystem || window.webkitRequestFileSystem;
    function onError(e) {
        console.log('Error', e);
    }
    // 用户要下载生成的文件
    var downFileName = filePath + '.download';

    var config = null; //配置信息集合
    // 分块大小
    var chunk = params.chunk || 1 * 1024 * 100;
    var count = 0;
    var index = 0;

    async.auto({
            /**
             * 初始化或者读取configFile
             * 1. 获取文件大小
             * 2. 并且根据分块大小计算出总共请求次数
             */
            init: function(callback) {
                /**
                 * 重置记录进度的配置文件，删除下载了部分的重名文件
                 * @param config
                 */
                function resetConfig(conf) {
                    localStorage.setItem(filePath, JSON.stringify(conf));
                    index = 0;

                    //如果有重名文件downFileName，删除
                    window.requestFileSystem(window.TEMPORARY, TEMP_SPACE, function(fs) {
                        fs.root.getFile(downFileName, {create: false}, function(fileEntry) {
                            fileEntry.remove(function() {
                                console.log(downFileName + ' File removed.');
                            }, onError);
                        }, onError);
                    }, onError);
                };

                //重置暂停标示
                Ks3.config.stopFlag = false;
                /**
                 * 从云端获取文件元数据
                 */
                console.log('远程获取元数据');
                Ks3.headObject(params, function(err, res) {
                    if (err) {
                        callback(err);
                    } else {
                        var length = res.getResponseHeader('content-length');
                        count = parseInt(length / chunk) + (length % chunk == 0 ? 0 : 1);
                        if (count == 0) {
                            callback({
                                msg: '文件大小为0'
                            })
                        } else if (localStorage && localStorage[filePath]) { // 之前已经有配置信息了
                            config = JSON.parse(localStorage.getItem(filePath));
                            if (config['lastModifyTime'] == res.getResponseHeader('Last-Modified')) {
                                console.log('本地读取数据');
                                count = config['count'];
                                index = config['index'];
                            } else {
                                config = {
                                    "BUCKET": bucketName,
                                    "KEY": key,
                                    "path": filePath,
                                    "chunk": chunk,
                                    "count": count,
                                    "index": 0,
                                    "lastModifyTime": res.getResponseHeader('Last-Modified')
                                };
                                resetConfig(config);
                            }
                        } else { //无配置信息
                            config = {
                                "BUCKET": bucketName,
                                "KEY": key,
                                "path": filePath,
                                "chunk": chunk,
                                "count": count,
                                "index": 0,
                                "lastModifyTime": res.getResponseHeader('Last-Modified')
                            };
                            resetConfig(config);
                        }
                        callback(null);
                    }
                });
            },
            /**
             * 下载分块数据,并追加到文件末尾
             */
            down: ['init', function(callback, result) {
                // 下载逻辑
                var progressBar = document.getElementById("downloadProgressBar");
                progressBar.max = count;

                function writeToFileSystem(blob, path) {
                    window.requestFileSystem(window.TEMPORARY,  TEMP_SPACE, function(fs) {
                        fs.root.getFile(path, {create: true}, function(fileEntry) {
                            fileEntry.createWriter(function(writer) {
                                var len = writer.length;
                                writer.seek(len); // Start write position at EOF.
                                console.log('download file length: ' + len);
                                writer.write(blob);
                            }, onError);
                        }, onError);
                    }, onError);
                }


                var downHandler = function() {
                    if(Ks3.config.stopFlag) {
                        callback({
                           msg: 'stop'
                        });
                    }else{
                        //更新下载进度
                        progressBar.value = index;
                        var percent = index + '/' + count;
                        document.getElementById('downloadPercent').innerText = percent;

                        if (index + 1 > count) { // 下载结束
                            console.log('下载结束')
                            callback(null);
                        } else { // 还没下载完,继续进行下载
                            console.log('进行下载:', index, '/', count);
                            Ks3.getObject({
                                    Bucket:bucketName,
                                    Key: key,
                                    range: 'bytes=' + index * chunk + '-' + ((index + 1) * chunk - 1)
                                },
                                function(err, data, res) {
                                    if (err) {
                                        callback(err, data);
                                    } else {
                                        writeToFileSystem(data, downFileName);
                                        index = index + 1;
                                        config['index'] = index;
                                        localStorage.setItem(filePath, JSON.stringify(config));
                                        downHandler();
                                    }
                                });
                        }
                    }
                };

                downHandler();
            }]
        },
        function(err, results) {
            /**
             * 将浏览器文件系统中的文件拷贝到用户文件系统中
             */
            function copyFileInBrowserRootToUserOS(fromFilePath, toFilePath) {
                window.requestFileSystem(window.TEMPORARY, TEMP_SPACE, function(fs) {
                    fs.root.getFile(fromFilePath, {create: false}, function(fileEntry) {
                        fileEntry.file(function(file) {
                            //downloadFile
                            var aLink = document.createElement('a');
                            var evt = document.createEvent("HTMLEvents");
                            evt.initEvent("click", false, false);//initEvent 不加后两个参数在FF下会报错
                            aLink.download = toFilePath;
                            window.URL = window.URL || window.webkitURL;
                            aLink.href = window.URL.createObjectURL(file);
                            aLink.dispatchEvent(evt);
                        }, onError);
                    }, onError);
                }, onError);
            }

            if (err) {
                if (cb) {
                    cb(err, results);

                }
            } else {
                localStorage.removeItem(filePath);

                copyFileInBrowserRootToUserOS(downFileName, filePath);
                if (cb) {
                    cb(err, {msg:'success',path:filePath});
                }
            }
        })
}