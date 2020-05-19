(function KS3_UPLOAD(win) {
	var JSY={
        host:'http://ks3-cn-beijing.ksyun.com/teaseyoulearn',
        fileToBeUpload:'',
	};
	JSY.Config=function(opt){
		var opt=opt || {};
		Ks3.config.AK = opt.ak || '+vB0RuO22DeeanAAb0ig';  //TODO： 请替换为您的AK
	    Ks3.config.SK = opt.sk || '3kbTp8pg+LymO84/ZyxG/od/3FeVDjWt+8/m5iBc'; //TODO: 测试时请填写您的secret key 
	    Ks3.config.region = 'BEIJING';  //TODO: 需要设置bucket所在region， 如杭州region： HANGZHOU,北京region：BEIJING，香港region：HONGKONG，上海region: SHANGHAI ，美国region:AMERICA ；如果region设置和实际不符，则会返回301状态码； region的endpoint参见：http://ks3.ksyun.com/doc/api/index.html
	    Ks3.config.bucket = opt.bucket_name || 'teaseyoulearn'; // TODO : 设置默认bucket name
		Ks3.config.protocol=window.location.protocol === 'https:' ? 'https' : 'http';
        Ks3.config.baseUrl=Ks3.ENDPOINT[Ks3.config.region];
        window.Ks3Host=Ks3.config.protocol + '://' + Ks3.config.baseUrl+ '/' + Ks3.config.bucket;
	}
	JSY.upload=function(opt,successFn,errorFn){
        var opt=opt || {};
		var ks3Options = {
	        KSSAccessKeyId: Ks3.config.AK,
	        signature: opt.signature || '',
	        bucket_name: Ks3.config.bucket,
	        key: (opt.dirname || 'image')+'/${filename}',
	        acl: "public-read",
	        uploadDomain: Ks3.config.protocol + '://' + Ks3.config.baseUrl+ '/' + Ks3.config.bucket,
	        autoStart: opt.autoStart || true,
	        onFilesAddedCallBack:function (uploader,obj) {
	            opt.FileAdd && opt.FileAdd(uploader,obj);
	        },
	        onErrorCallBack:function (uploader, errObject) {
	            errorFn && errorFn(uploader,errObject);
	        },
	        onFileUploadedCallBack:function (uploader,obj) {
	            successFn && successFn(uploader,obj);
	        }
	    };
		var uploader=new ks3FileUploader(ks3Options,{
	        browse_button:opt.browse_button || 'browse',
	        flash_swf_url :'/public/pc/ks3-js-sdk/src/Moxie.swf',
	        silverlight_xap_url : '/public/pc/ks3-js-sdk/src/Moxie.xap',
	        filters:{
	            mime_types:[
	                { title : "Image files", extensions : "jpg,gif,png" },
                    { title : "Video files", extensions : "mp4,mov,qt,ts,rmvb,rm,avi,flv,mkv,wmv,mpg,mpeg,m2v,m4v,3gp,3g2,webm,vob,ogv,ogg" }
	            ],
	            prevent_duplicates:true,
	            max_file_size:'2gb',
	        },
	        multi_selection:opt.max_length || false,
	    });
	    return uploader;
	}
	JSY.LoadEvent=function(){
		$('.upload-image-box').on({
            mouseover:function () {
                $(this).find('.mask').show();
            },
            mouseout:function () {
                $(this).find('.mask').hide();
            }
        })
        $('.open_image').on('click',function () {
            $eb.openImage($(this).data('url'));
        });
	}
    JSY.getImgBoxHtml=function ($name,inputname) {
        var host=this.host+'/'+$name,inputname=inputname || 'image';
        return '<div class="upload-image-box">\n' +
            '   <img src="'+host+'" alt="">\n' +
            '   <input type="hidden" name="'+inputname+'" value="'+host+'">\n' +
            '   <div class="mask">\n' +
            '   <p><i class="fa fa-eye open_image" data-url="'+host+'"></i><i class="fa fa-trash-o delete_image" data-url="'+$name+'"></i></p>\n' +
            '   </div>\n' +
            '</div>';
    };
	JSY.delObjectKey=function (keyArray) {
		$.each(keyArray,function (key) {
			delete keyArray[key];
        });
    }
    /**
     * 获取指定的文件部分内容
     */
    JSY.getFileContent=function(file, chunkSize, start, cb) {
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
    JSY.getProgressKey=function(name, lastModified, bucket, key) {
        return name + "-" + lastModified + "-" + bucket + "-" + key;
    }
    JSY.configInit=function (file, progressKey,successFn) {
        var fileSize = file.size;
        var count = parseInt(fileSize / Ks3.config.chunkSize) + ((fileSize % Ks3.config.chunkSize == 0 ? 0: 1));
        if (count == 0){
            successFn && successFn({msg: 'The file is empty.'})
        }else {
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
            successFn && successFn();
        }
    }
    //分片上传
    JSY.multipartUpload=function (opt) {
        var file = opt.File,key=opt.key || '',totalSize = file.size,that=this,changeVal=opt.changeVal || null,
			successFn=opt.success || null,errorFn=opt.error || null,contentType = opt.ContentType || '';
        var progressKey =this.getProgressKey(file.name, file.lastModified,Ks3.config.bucket, key);
        async.auto({
			/**
			 * 初始化配置文件,如果没有就新建一个
			 */
			init: function(callback) {
				//重置暂停标识
				Ks3.config.stopFlag = false;

				if ( !localStorage[progressKey]) {
                    that.configInit(file, progressKey, function(err) {
						callback(err);
					})
				} else {
					callback(null);
				}

			},
			show: ['init', function(callback) {
				console.log('  开始上传文件: ' + progressKey);
				config = JSON.parse(localStorage.getItem(progressKey));
                changeVal && changeVal(config);
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
					Ks3.multitpart_upload_init(opt, function(err, uploadId) {
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
                        errorFn && errorFn(err);
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
                        that.getFileContent(file, chunkSize, start, function(body) {
							delete opt.File;
                            opt.UploadId = uploadId;
                            opt.PartNumber = index;
                            opt.body = body;
                            opt.type = contentType;
							console.log('正在上传第 ', index, ' 块,总共: ', + count + ' 块');
							try {
								Ks3.upload_part(opt, function(err, partNumber, etag) {
									if (err) {
										if(err.status == 413 || err.status == 415) {
											callback(err);
										}else {
											retry(err);
										}
									} else {
										if(!Ks3.config.stopFlag) {
											config['index'] = index;
                                            changeVal && changeVal(config);
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
						delete opt.File;
                        opt.UploadId = uploadId;
                        opt.body = that.generateCompleteXML(progressKey);

						Ks3.upload_complete(opt, function(err, res) {
							if (err) throw err;
							callback(err, res);
						})
					}
				};
			}]
		},function(err, results) {
			if (!err) localStorage.removeItem(progressKey);
			if(err) errorFn && errorFn(err);
            else successFn && successFn(results);
        });
    }
    /**
     * 生成合并分块上传使用的xml
     */
    JSY.generateCompleteXML=function(progressKey) {
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
    //暂停
    JSY.suspendMultipartUpload=function () {
        Ks3.config.stopFlag = true;
    }
    //清除localStorage
    JSY.emptyLocalStorage=function(){
        var len = localStorage.length;
        for(var i=0; i< len; i++) {
            var itemKey = localStorage.key(i);
            //自动创建一个临时String对象封装itemKey在IE下会导致内存泄露，故显示转换
            if(typeof itemKey  === 'string'  && (new String(itemKey)).endWith(Ks3.config.bucket + '-' + Ks3.encodeKey(this.fileToBeUpload))) {
                localStorage.removeItem(itemKey);
            }
        }
    }
    //取消上传
    JSY.cancelMultipartUpload=function (findFn) {
	    var that=this;
        //前端暂停
        Ks3.config.stopFlag = true;
        //通知ks3取消上传
        if(Ks3.config.currentUploadId) {
            Ks3.abort_multipart_upload({
                Bucket:Ks3.config.bucket,
                Key:this.fileToBeUpload,
                UploadId: Ks3.config.currentUploadId
            },function(err,res) {
                if(err) {
                    findFn && findFn(err);
                }else{
                    findFn && findFn(err);
                    that.emptyLocalStorage();
                }
            });
        }
    }

	return win.JSY=JSY;
})(window)