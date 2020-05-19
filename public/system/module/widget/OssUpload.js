(function (global) {

    var ossUpload = {
        accessid: '',
        host: "",
        expire: 0,
        uploader: null,
        data: {},
        files: [],
        key: '',
    };

    /**
     * 初始化
     */
    ossUpload.init = function () {

    }

    /**
     * 获取配置签名
     * @returns {Promise<any>}
     */
    ossUpload.getSignature = function () {
        var that = this;
        return new Promise(function (resolve, reject) {
            var now = timestamp = Date.parse(new Date()) / 1000;
            if (that.expire > now + 3) {
                return resolve(that.data);
            }
            requestGet(getUrl({c: "widget.images", a: 'get_signature'})).then(function (res) {
                that.accessid = res.data.accessid;
                that.host = res.data.host;
                that.expire = parseInt(res.data.expire);
                that.data = res.data;
                resolve(res.data);
            }).catch(function (res) {
                reject(res);
            })
        })
    }

    /**
     * 获取key
     * @param len
     * @returns {string}
     */
    ossUpload.randomString = function (len) {
        len = len || 32;
        var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
        var maxPos = chars.length;
        var pwd = '';
        for (i = 0; i < len; i++) {
            pwd += chars.charAt(Math.floor(Math.random() * maxPos));
        }
        return pwd;
    }

    /**
     * 设置上传参数
     * @param up
     */
    ossUpload.setUploadParam = function (up, fileName) {
        var that = this;
        this.getSignature().then(function (res) {
            that.key = that.randomString(18) + that.getSuffix(fileName);
            up.setOption({
                url: that.host,
                multipart_params: {
                    key: that.key,
                    policy: res.policy,
                    OSSAccessKeyId: res.accessid,
                    success_action_status: "200",
                    callback: res.callback,
                    signature: res.signature,
                }
            });
            up.start();
        }).catch(function (res) {
            console.log(res);
        })
    }

    /**
     *
     * @param filename
     * @returns {string}
     */
    ossUpload.getSuffix = function (filename) {
        var pos = filename.lastIndexOf('.'), suffix = ''
        if (pos != -1) {
            suffix = filename.substring(pos)
        }
        return suffix;
    }

    /**
     * 上传文件
     * @param opt
     */
    ossUpload.upload = function (opt) {
        var that = this;
        if (typeof opt !== 'object') {
            opt = {};
        }
        var config = {
            idName: opt.id,
            mime_types: [
                {title: "Image files", extensions: "jpg,gif,png,bmp"},
                {title: "Mp4 files", extensions: "mp4"},
                {title: "Mp3 files", extensions: "mp3"}
            ],
            max_file_size: '1000mb',
            prevent_duplicates: true,
            multi_selection: false,
            init: function (uploader) {
            },
            FilesAddedSuccess: function (files) {
            },
            uploadIng: function (file) {
            },
            success: function (res) {
            },
            fail: function (err) {
            }
        };
        Object.assign(config, opt);
        that.uploader = new plupload.Uploader({
            runtimes: 'html5,flash,silverlight,html4',
            browse_button: config.idName,
            multi_selection: config.multi_selection,
            flash_swf_url: 'lib/plupload-2.1.2/js/Moxie.swf',
            silverlight_xap_url: 'lib/plupload-2.1.2/js/Moxie.xap',
            url: 'http://oss.aliyuncs.com',
            filters: {
                mime_types: config.mime_types,
                max_file_size: config.max_file_size, //最大只能上传10mb的文件
                prevent_duplicates: config.prevent_duplicates //不允许选取重复文件
            },
            init: {
                PostInit: function () {
                    config.init(that.uploader);
                },
                FilesAdded: function (up, files) {
                    if (config.multi_selection === false) {
                        that.setUploadParam(up, files[0].name);
                    }
                    config.FilesAddedSuccess(files);
                    that.files = files;
                },
                BeforeUpload: function (up, file) {

                },
                UploadProgress: function (up, file) {
                    config.uploadIng(file);
                },
                FileUploaded: function (up, file, info) {
                    var key = that.key;
                    that.key = '';
                    if (info.status == 200) {
                        config.success({key: key, host: that.host, url: that.host + '/' + key})
                    } else if (info.status == 203) {
                        config.fail(info.response)
                    } else {
                        config.success({key: key, host: that.host, url: that.host + '/' + key})
                    }
                    that.files = [];
                },
                Error: function (up, err) {
                    if (err.code == -600) {
                        config.fail('选择的文件太大了');
                    } else if (err.code == -601) {
                        config.fail('选择的文件后缀不对');
                    } else if (err.code == -602) {
                        config.fail('这个文件已经上传过一遍了');
                    } else {
                        config.fail(err.response);
                    }
                }
            }
        });
        that.uploader.init();

        return that.uploader;
    }

    /**
     * 手动上传文件
     */
    ossUpload.start = function () {
        var that = this;
        that.files.map(function (file) {
            that.setUploadParam(that.uploader, file.name);
        })
    }

    /**
     * 获取图片html
     * @param $name
     * @param inputname
     * @returns {string}
     */
    ossUpload.getImageHtml = function ($name, inputname, host) {
        var host = (host !== undefined ? host : this.host + '/') + $name, inputname = inputname || 'image';
        return '<div class="upload-image-box">\n' +
            '       <img src="' + host + '" alt="">\n' +
            '       <input type="hidden" name="' + inputname + '" value="' + host + '">\n' +
            '       <div class="mask">\n' +
            '           <p><i class="fa fa-eye open_image" data-url="' + host + '"></i><i class="fa fa-trash-o delete_image" data-url="' + $name + '"></i></p>\n' +
            '       </div>\n' +
            '</div>';
    }

    /**
     * 绑定事件
     * @constructor
     */
    ossUpload.LoadEvent = function () {
        $('.upload-image-box').on({
            mouseover: function () {
                $(this).find('.mask').show();
            },
            mouseout: function () {
                $(this).find('.mask').hide();
            }
        })
        $('.open_image').on('click', function () {
            $eb.openImage($(this).data('url'));
        });
    }

    /**
     * 删除指定资源
     * @param key
     * @returns {Promise<any>}
     */
    ossUpload.delete = function (key, url) {
        return new Promise(function (resolve, reject) {
            requestGet(getUrl({
                c: "widget.images",
                a: 'del_oss_key',
                q: {key: key, url: url ? url : ''}
            })).then(function (res) {
                resolve(res);
            }).cache(function (res) {
                reject(res);
            })
        });
    }

    /**
     * 打开一个窗口
     * @param title
     * @param src
     * @param opt
     * @returns {*}
     */
    ossUpload.createFrame = function (title, p, opt) {
        opt === undefined && (opt = {});
        var h = 0;
        if (window.innerHeight < 800 && window.innerHeight >= 700) {
            h = window.innerHeight - 50;
        } else if (window.innerHeight < 900 && window.innerHeight >= 800) {
            h = window.innerHeight - 100;
        } else if (window.innerHeight < 1000 && window.innerHeight >= 900) {
            h = window.innerHeight - 150;
        } else if (window.innerHeight >= 1000) {
            h = window.innerHeight - 200;
        } else {
            h = window.innerHeight;
        }
        var src = getUrl({c: 'widget.images', a: 'index', q: p || {}});
        var area = [(opt.w || window.innerWidth / 2) + 'px', (!opt.h || opt.h > h ? h : opt.h) + 'px'];
        return layer.open({
            type: 2,
            title: title,
            area: area,
            fixed: false, //不固定
            maxmin: true,
            moveOut: false,//true  可以拖出窗外  false 只能在窗内拖
            anim: 5,//出场动画 isOutAnim bool 关闭动画
            offset: 'auto',//['100px','100px'],//'auto',//初始位置  ['100px','100px'] t[ 上 左]
            shade: 0,//遮罩
            resize: true,//是否允许拉伸
            content: src,//内容
            move: '.layui-layer-title'
        });
    }

    ossUpload.init();

    global.ossUpload = ossUpload;

    return ossUpload;

}(this));