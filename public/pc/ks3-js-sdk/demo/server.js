/**
 * Created by Marlon on 15-12-25.
 */
const http = require('http');
const querystring = require('querystring');
const url = require('url');
const fs = require('fs');
const path = require('path');
const formidable = require('formidable');
const KS3 = require('ks3');
const auth = require('ks3/lib/auth');
const util = require('ks3/lib/util');

const ROOT = '/Users/web/ks3-js/sdk/demo';
const hostname = '127.0.0.1';
const port = 3000;
const AK = 'your Access Key'; //replace with your AK
const SK = 'your secret key';  // your secret key (SK)

var responseHeader = {
    "Access-Control-Allow-Origin": "*",
    "Access-Control-Allow-Methods": "PUT,POST,GET,DELETE,OPTIONS",
    "Access-Control-Allow-Headers": "Authorization,kss-async-process,kss-notifyurl,x-kss-storage-class"
};

function get(req, res) {
    var pathname = url.parse(req.url).pathname;
    fs.readFile(path.join(ROOT, pathname), function (err, file) {
        if (err) {
            res.writeHead(404);
            res.end('找不到相关文件。 - -');
            return;
        }
        res.writeHead(200);
        res.end(file);
    });
}

function hasBody(req) {
    return 'transfer-encoding' in req.headers || ('content-length' in req.headers && req.headers['content-length'] !== '0');
}


function mime(req) {
    var str = req.headers['content-type'] || '';
    return str.split(';')[0];
}


//POST请求 处理函数
function handle(req, res) {
    //TODO: not implemented
}

function post(req, res) {
    if (hasBody(req)) {

        var buffers = [];
        req.on('data', function (chunk) {
            buffers.push(chunk);
        }).on('end', function () {
            req.rawBody = Buffer.concat(buffers).toString();
            console.log('postData: ' + req.rawBody);


            if (mime(req) === 'application/json') {
                try {
                    req.body = JSON.parse(req.rawBody);
                    handle(req, res);
                } catch (e) {
                    res.writeHead(400);
                    req.end('Invalid JSON');
                    return;
                }
            } else if (mime(req) === 'application/xml') {
            }
            else if (mime(req) === 'multipart/form-data') {
            }
        });
    } else {
        handle(req, res);
    }
}

function put(req, res) {
    var auth = req.headers['authorization'];
    var async_process = req.headers['kss-async-process'];
    var notifyurl = req.headers['kss-notifyurl'];
    console.log('adp: ' + async_process + '\nnotifyUrl: ' + notifyurl);
    if(auth !== 'KSS ' + AK){
        res.writeHead(403, responseHeader);
        res.end('Authorization not match');
        return;
    }
    if (hasBody(req)) {
        if (mime(req) === 'multipart/form-data') {
            var form = new formidable.IncomingForm();
            form.parse(req, function (err, fields, files) {
                req.body = fields;
                req.files = files; // don't forget to delete all req.files when done
                next(req, res);
            });
        }else{
            res.writeHead(400, responseHeader);
            res.end('mime type not match');
        }
    }else{
        res.writeHead(400, responseHeader);
        res.end('not found body');
    }
};

function next(req, res) {
    var bucketName = url.parse(req.url).pathname.split('/')[1];
    req.query = url.parse(req.url, true).query;

    var client = new KS3(AK, SK, bucketName);
    var key = req.body.key;
    var filePath = req.files.file.path;
    var outerRes = res;
    client.object.put({
            Bucket: bucketName,
            Key: key,
            filePath: filePath
        },
        function (err, data, res) {
            if (err) {
                console.log("Error in put : " + err.message);
                return;
            }
            console.log(JSON.stringify(res));
            if (res.status === 200 && res.statusCode === 200) {
                outerRes.writeHead(200, responseHeader);

                //计算处理之后的文件的signature
                var resource = '/' + util.encodeKey('imgWaterMark-' + key);
                var getReq = {
                    method: 'GET',
                    date: req.query.t,
                    uri:  'http://' + bucketName + '.kss.ksyun.com' + resource,
                    resource: '/' + bucketName + resource,
                    headers: {}
                };
                var signature = auth.generateToken(SK, getReq);
                console.log('signature:' + signature);
                outerRes.end(signature);

                //下载加过水印的图片到assets目录
                setTimeout(getAdpResult, 2000);
                function getAdpResult() {
                    client.object.get({
                        Bucket: bucketName,
                        Key: 'imgWaterMark-' + key
                    }, function (err, data, res, originData) {
                        if (err) {
                            console.log("Error in get : " + err.message);
                            return;
                        }
                        var newFileName = path.join(__dirname, 'assets/imgWaterMark-' + key);
                        fs.writeFileSync(newFileName, originData);
                    });
                }
            }
        },
        {
            'kss-async-process': req.headers['kss-async-process'],
            'kss-notifyurl': req.headers['kss-notifyurl'],
            'x-kss-storage-class' : req.headers['x-kss-storage-class']
        });
}


//启动服务器
var server = http.createServer(function (req, res) {
    //console.log( req.headers);


    switch (req.method) {
        case 'OPTIONS':
            res.writeHead(200, responseHeader);
            res.end();
            /*让options请求快速返回*/
            break;
        case 'POST':
            post(req, res);
            break;
        case 'PUT':
            put(req, res);
            break;

        case 'GET':
        default :
            get(req, res);
    }
}).listen(port, hostname, function () {
    console.log('Server running at http://' + hostname + ':' + port + '/');
});

