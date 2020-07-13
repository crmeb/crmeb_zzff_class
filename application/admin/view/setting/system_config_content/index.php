{extend name="public/container"}
{block name="head_top"}
    <title>编辑内容</title>
<link href="{__ADMIN_PATH}plug/umeditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
<link href="{__ADMIN_PATH}module/wechat/news/css/style.css" type="text/css" rel="stylesheet">
<link href="{__FRAME_PATH}css/plugins/chosen/chosen.css" rel="stylesheet">
<script type="text/javascript" src="{__ADMIN_PATH}plug/umeditor/third-party/jquery.min.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}plug/umeditor/third-party/template.min.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/umeditor/umeditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/umeditor/umeditor.min.js"></script>
<script src="{__ADMIN_PATH}plug/validate/jquery.validate.js"></script>
<script src="{__FRAME_PATH}js/plugins/chosen/chosen.jquery.js"></script>
<script src="{__ADMIN_PATH}plug/vue/dist/vue.min.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" charset="utf-8"
        src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}plug/ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/aliyun-oss-sdk-4.4.4.min.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/lib/plupload-2.1.2/js/plupload.full.min.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/OssUpload.js"></script>
    <style>
        .edui-btn-toolbar .edui-btn.edui-active .edui-icon-fullscreen.edui-icon {
            display: none;
        }

        .edui-container {
            overflow: initial !important;
        }

        button.btn-success.dim {
            box-shadow: inset 0 0 0 #1872ab, 0 5px 0 0 #1872ab, 0 10px 5px #999;
        }

        .float-e-margins .btn {
            margin-bottom: 5px;
        }

        button.dim {
            display: inline-block;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            text-align: center;
            padding-top: 6px;
            margin-right: 10px;
            position: relative;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 600;
            margin-bottom: 20px !important;
        }

        .btn-success {
            background-color: #1c84c6;
            border-color: #1c84c6;
            color: #FFF;
        }

        .btn {
            border-radius: 3px;
        }

        .btn-success {
            color: #fff;
            background-color: #5cb85c;
            border-color: #4cae4c;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        button, input, select, textarea {
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }

        button.btn-success.dim:active {
            box-shadow: inset 0 0 0 #1872ab, 0 2px 0 0 #1872ab, 0 5px 3px #999;
        }

        button.dim:active {
            bottom: 4px;
        }

        .btn-success.active, .btn-success:active, .open .dropdown-toggle.btn-success {
            background-image: none;
        }

        .btn-success.active, .btn-success:active, .btn-success:focus, .btn-success:hover, .open .dropdown-toggle.btn-success {
            background-color: #1a7bb9;
            border-color: #1a7bb9;
            color: #FFF;
        }

        .dim {
            bottom: 7px;
            right: 8px;
            z-index: 1003;
            position: fixed !important;
        }
        .m-t-5 {
            margin-top: 5px;
        }

        .edui-default .edui-for-image .edui-icon {
            background-position: -380px 0px;
        }
    </style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">{$title}</div>
                <div class="layui-card-body">
                    <button class="btn btn-success  dim" @click="submit" type="button"><i class="fa fa-upload"></i>
                    </button>
                    <textarea id="myEditor" style="width:100%;">{$content ? $content : ''}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script type="text/javascript">
    var _vue = new Vue({
        el:'#app',
        data:{
            uploader: null,
            ue: null,
            //上传类型
            mime_types: {
                Image: "jpg,gif,png,JPG,GIF,PNG",
                Video: "mp4,MP4",
            },
            host: ossUpload.host + '/',
        },
        methods:{
            submit:function () {
                var that = this;
                $.ajax({
                    url:"{:Url('save',['id'=>$id])}",
                    data:{content:that.ue.getContent()},
                    type:'post',
                    dataType:'json',
                    success:function(res){
                        if(res.code == 200){
                            location.reload();
                            return $eb.message('success',res.msg);
                        }else{
                            return $eb.message('error',res.msg);
                        }
                    }
                })
            }
        },
        mounted:function () {
            var that = this;
            //选择图片插入到编辑器中
            window.insertEditor = function (list) {
                that.ue.execCommand('insertimage', list);
            };
            that.$nextTick(function () {
                layList.form.render();
                //实例化编辑器
                UE.registerUI('imagenone', function (editor, name) {
                    var $btn = new UE.ui.Button({
                        name: 'image',
                        onclick: function () {
                            ossUpload.createFrame('选择图片', {fodder: 'editor'},{w:800,h:550});
                        },
                        title: '选择图片'
                    });

                    return $btn;

                });
                that.ue = UE.getEditor('myEditor');
            });
            that.$nextTick(function () {
                that.uploader = ossUpload.upload({
                    id: 'ossupload',
                    FilesAddedSuccess: function () {
                    },
                    uploadIng: function (file) {
                    },
                    success: function (res) {
                        layList.msg('上传成功');

                    },
                    fail: function (err) {
                        layList.msg(err);
                    }
                })
            });
        }
    })
    function changeIMG(index,pic){
        _vue._data.newListIndex.image_input = pic;
        _vue._data.newList[_vue._data.indexItem].image_input = pic;
    };
</script>
</script>
{/block}