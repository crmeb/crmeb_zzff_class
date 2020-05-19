<?php

namespace service;
use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerService extends PHPMailer
{
    const HOST='smtp.qq.com';

    const USER='136327134@qq.com';

    const PSD='fkjeyxwuvujtbibh';

    protected static $debug=0;

    protected static $mail=null;

    protected static function setConfig(){

        self::$mail = new self();
        //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        self::$mail->SMTPDebug =self::$debug;
        //使用smtp鉴权方式发送邮件
        self::$mail->isSMTP();
        //smtp需要鉴权 这个必须是true
        self::$mail->SMTPAuth=true;
        //链接qq域名邮箱的服务器地址
        self::$mail->Host =self::HOST;
        //设置使用ssl加密方式登录鉴权
        self::$mail->SMTPSecure = 'ssl';
        //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
        self::$mail->Port = 465;
        //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        self::$mail->CharSet = 'UTF-8';
        //smtp登录的账号 这里填入字符串格式的qq号即可
        self::$mail->Username =self::USER;
        //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
        self::$mail->Password =self::PSD;
        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        self::$mail->isHTML(true);
    }
    /*
     * $addresser 发件人
     * $to 发送至邮箱
     * $title 标题
     * $content 内容
     * */
    public static function sendMail($title,$content,$to,$name='',$addresser=self::USER){
        self::setConfig();
        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        self::$mail->From=$addresser;
        //设置发件人
        if(is_array($to)){
            foreach ($to as $item){
                if(isset($item['to_mail']) && isset($item['name']) && $item['to_mail']) self::$mail->addAddress($item['to_mail'],$item['name']);
            }
        }else{
            self::$mail->addAddress($to,$name);
        }

        self::$mail->Subject = $title;

        self::$mail->Body = $content;

        return self::$mail->send();
    }

}