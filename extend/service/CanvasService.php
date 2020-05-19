<?php


namespace service;

use service\FileService;
use app\routine\model\routine\RoutineCode;

class CanvasService
{
    const FILELINK = 'uploads/';
    //背景图
    const VOTEIMG = 'uploads/voteimg/voteimg.jpg';
    //字体
    const FONT = 'uploads/voteimg/simsunb.ttf';

    const FONT_TWO = 'uploads/voteimg/fz-v4.0.ttf';

    const BORDER = 'uploads/voteimg/border.png';

    protected static $canvas = null;

    protected static $image = null;

    protected static $backgroundWidth = 750;

    protected static $backgroundHeight = 1334;

    public static function CreatJpeg($file = '')
    {
        $file = $file ? $file : self::VOTEIMG;
        $imagesize = getimagesize($file);
        $type = image_type_to_extension($imagesize[2], true);
        switch ($type) {
            case '.png':
                $canvas = imagecreatefrompng($file);
                break;
            case '.jpeg':
                $canvas = imagecreatefromjpeg($file);
                break;
            case '.jpg':
                $canvas = imagecreatefromjpeg($file);
                break;
            case '.gif':
                $canvas = imagecreatefromgif($file);
                break;
        }
        return [$canvas, $imagesize];

    }

    public static function ReatetrueColor($w = 0, $h = 0)
    {
        return imagecreatetruecolor($w ? $w : self::$backgroundWidth, $h ? $h : self::$backgroundHeight);
    }

    public static function foundCode($special_id, $url, $backgroundImg, $ext = 'poster_code_')
    {
        vendor('phpqrcode.phpqrcode');
        $qrcodename = self::FILELINK . time() . 'qrcode.png';
        \QRcode::png($url, $qrcodename, 'L', 10, 2);
        $image = self::ReatetrueColor();
        //放背景
        list($canvas, $borderRes) = self::CreatJpeg($backgroundImg);
//        $color = imagecolorallocate($canvas, 255, 255, 255);
//        imagefill($canvas, 0, 0, $color);
        imagecopyresampled($image, $canvas, 0, 0, 0, 0, imagesx($canvas), imagesy($canvas), imagesx($canvas), imagesy($canvas));

        list($code, $codeRes) = self::CreatJpeg($qrcodename);
        imagecopyresampled($image, $code, 505, 1090, 0, 0, 156, 156, (int)$codeRes[0], (int)$codeRes[1]);

        $FileService = new FileService();
        $FileService->create_dir(self::FILELINK);
        $filename = self::FILELINK . $ext . $special_id . '.jpg';
        imagejpeg($image, $filename, 70);

        imagedestroy($image);

        $FileService->unlink_file($qrcodename);

        return $filename;
    }

    public static function startPosterSpeclialIng($special_id, $backgroundImg, $url)
    {
        vendor('phpqrcode.phpqrcode');
        $qrcodename = time() . '_show_qrcode.png';
        \QRcode::png($url, $qrcodename, 'L', 10, 2);
        $image = self::ReatetrueColor();
        //放背景
        list($canvas, $borderRes) = self::CreatJpeg($backgroundImg);
        imagecopyresampled($image, $canvas, 0, 0, 0, 0, imagesx($canvas), imagesy($canvas), imagesx($canvas), imagesy($canvas));

        list($code, $codeRes) = self::CreatJpeg($qrcodename);
        imagecopyresampled($image, $code, 505, 1090, 0, 0, 156, 156, (int)$codeRes[0], (int)$codeRes[1]);

        $FileService = new FileService();
        $FileService->create_dir(self::FILELINK);
        $filename = self::FILELINK . 'poster_' . $special_id . '.jpg';
        imagejpeg($image, $filename, 70);

        imagedestroy($image);

        $FileService->unlink_file($qrcodename);

        return $filename;
    }

    public static function startPosterIng($backgroundImgList, $uid)
    {
        if (!is_array($backgroundImgList)) return false;
        //放二维码
        $path = 'public/uploads/routine/' . $uid . '.jpg';
        if (!file_exists($path)) file_put_contents($path, RoutineCode::getCode($uid));
        $link = [];
        foreach ($backgroundImgList as $item) {
            if ($item['pic']) {
                $image = self::ReatetrueColor();
                //放背景
                list($canvas, $borderRes) = self::CreatJpeg(ROOT_PATH . $item['pic']);
                $color = imagecolorallocate($canvas, 0, 0, 0);
                imagefill($canvas, 0, 0, $color);
                imagecopyresampled($image, $canvas, 0, 0, 0, 0, imagesx($canvas), imagesy($canvas), imagesx($canvas), imagesy($canvas));

                list($code, $codeRes) = self::CreatJpeg($path);
                imagecopyresampled($image, $code, 170, 545, 0, 0, 220, 220, (int)$codeRes[0], (int)$codeRes[1]);

                $res = pathinfo($item['pic']);
                $save_file = self::FILELINK . $res['filename'] . 'haibao_.jpg';
                imagejpeg($image, $save_file, 70);
                imagedestroy($image);

                $link[] = $save_file;
            }
        }
        return $link;
    }

    public static function startPainting($userinfo = [], $merinfo = [], $mer_id)
    {
        $image = self::ReatetrueColor();
        //放背景
        list($canvas, $res) = self::CreatJpeg();
        $color = imagecolorallocate($canvas, 0, 0, 0);
        imagefill($canvas, 0, 0, $color);
        imagecopyresampled($image, $canvas, 0, 0, 0, 0, imagesx($canvas), imagesy($canvas), imagesx($canvas), imagesy($canvas));
        //放边框
        list($border, $borderRes) = self::CreatJpeg(self::BORDER);
        imagecopyresampled($image, $border, 10, 380, 0, 0, 450, 483, (int)$borderRes[0], (int)$borderRes[1]);
        //放二维码
        $path = 'public/uploads/routine/' . $userinfo['uid'] . '.jpg';
        if (!file_exists($path)) file_put_contents($path, RoutineCode::getCode($userinfo['uid']));
        list($code, $codeRes) = self::CreatJpeg($path);
        imagecopyresampled($image, $code, 150, 950, 0, 0, 180, 180, (int)$codeRes[0], (int)$codeRes[1]);
        //放头部
        list($heade, $headeRes) = self::CreatJpeg(self::FILELINK . 'heade.png');
        imagecopyresampled($image, $heade, 10, 50, 0, 0, 446, 281, (int)$headeRes[0], (int)$headeRes[1]);
        //放头像
        //下载头像为jpg头像
        if (!$userinfo['avatar_name'] && !file_exists($userinfo['avatar_name'])) {
            $avatar_name = time() . '.jpg';
            FileService::down_remote_file($userinfo['avatar'], self::FILELINK, $avatar_name);
            $link_image = self::FILELINK . 'user' . time() . '.png';
            self::CutChart(self::FILELINK . $avatar_name, $link_image);
            //删除刚下载的图片
            if (file_exists(self::FILELINK . $avatar_name)) unlink(self::FILELINK . $avatar_name);
        } else {
            $link_image = $userinfo['avatar_name'];
        }
        //放置头像
        list($avatar_c, $avatarRes) = self::CreatJpeg($link_image);
        imagecopyresampled($image, $avatar_c, 85, 58, 0, 0, 95, 95, (int)$avatarRes[0], (int)$avatarRes[1]);
        $text = [
            [
                'fontSize' => 20,
                'fontColor' => '231,180,52',
                'left' => 70,
                'top' => 200,
                'text' => self::getUtf8Str($userinfo['nickname']),
                'fontPath' => self::FONT,
                'angle' => 0,
            ],
            [
                'fontSize' => 19,
                'fontColor' => '255,255,255',
                'left' => 70,
                'top' => 235,
                'text' => self::getUtf8Str('吃货值:' . $userinfo['integral']),
                'fontPath' => self::FONT,
                'angle' => 0,
            ],
            [
                'fontSize' => 19,
                'fontColor' => '255,255,255',
                'left' => 70,
                'top' => 265,
                'text' => self::getUtf8Str('吃货头衔:' . $userinfo['grade_name']),
                'fontPath' => self::FONT,
                'angle' => 0,
            ],
            [
                'fontSize' => 19,
                'fontColor' => '255,255,255',
                'left' => 70,
                'top' => 295,
                'text' => self::getUtf8Str('吃货折扣:' . $userinfo['discount_num'] . '折'),
                'fontPath' => self::FONT,
                'angle' => 0,
            ],
            [
                'fontSize' => 20,
                'fontColor' => '255,255,255',
                'left' => 20,
                'top' => 800,
                'text' => self::getUtf8Str($userinfo['mer_name']),
                'fontPath' => self::FONT,
                'angle' => 0,
            ],
            [
                'fontSize' => 15,
                'fontColor' => '255,255,255',
                'left' => 20,
                'top' => 838,
                'text' => self::getUtf8Str($userinfo['details_address']),
                'fontPath' => self::FONT,
                'angle' => 0,
            ],
            [
                'fontSize' => 15,
                'fontColor' => '255,255,255',
                'left' => 159,
                'top' => 900,
                'text' => self::getUtf8Str('(扫码获取吃货值)'),
                'fontPath' => self::FONT,
                'angle' => 0,
            ]
        ];
        $images = [
            [
                'url' => isset($merinfo[0]) ? $merinfo[0] : '',
                'left' => 15,
                'top' => 390,
                'right' => 0,
                'stream' => 0,
                'bottom' => 0,
                'width' => 218,
                'height' => 155,
                'opacity' => 100,
            ],
            [
                'url' => isset($merinfo[1]) ? $merinfo[1] : '',
                'left' => 235,
                'top' => 390,
                'right' => 0,
                'stream' => 0,
                'bottom' => 0,
                'width' => 218,
                'height' => 155,
                'opacity' => 100,
            ],
            [
                'url' => isset($merinfo[2]) ? $merinfo[2] : '',
                'left' => 15,
                'top' => 548,
                'right' => 0,
                'stream' => 0,
                'bottom' => 0,
                'width' => 218,
                'height' => 155,
                'opacity' => 100,
            ],
            [
                'url' => isset($merinfo[3]) ? $merinfo[3] : '',
                'left' => 235,
                'top' => 548,
                'right' => 0,
                'bottom' => 0,
                'width' => 218,
                'height' => 155,
                'opacity' => 100,
            ]
        ];
        foreach ($images as $item) {
            if ($item['url']) {
                list($mer, $Res) = self::CreatJpeg(ROOT_PATH . $item['url']);
                if ($mer && $Res) imagecopyresampled($image, $mer, $item['left'], $item['top'], $item['right'], $item['bottom'], $item['width'], $item['height'], (int)$Res[0], (int)$Res[1]);
            }
        }
        foreach ($text as $key => $val) {
            list($R, $G, $B) = explode(',', $val['fontColor']);
            $fontColor = imagecolorallocate($image, $R, $G, $B);
            $val['left'] = $val['left'] < 0 ? self::$backgroundWidth - abs($val['left']) : $val['left'];
            $val['top'] = $val['top'] < 0 ? self::$backgroundHeight - abs($val['top']) : $val['top'];
            imagettftext($image, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $fontColor, $val['fontPath'], $val['text']);
        }
        $save_file = self::FILELINK . 'haibao_' . time() . '.jpg';
        imagejpeg($image, $save_file, 70);
        imagedestroy($image);
        return ['file' => $save_file, 'avatar_name' => $link_image];
    }

    public static function getUtf8Str($str, $utf8len = 28, $chaet = 'UTF-8', $file = '...')
    {
        if (mb_strlen($str, $chaet) > $utf8len) {
            $str = mb_substr($str, 0, $utf8len, $chaet) . $file;
        }
        return $str;
    }

    public static function CutChart($imgpath, $savefilename)
    {
        $ext = pathinfo($imgpath);
        $src_img = null;
        switch ($ext['extension']) {
            case 'jpg':
                $src_img = imagecreatefromjpeg($imgpath);
                break;
            case 'png':
                $src_img = imagecreatefrompng($imgpath);
                break;
            case 'gif':
                $src_img = imagecreatefromgif($imgpath);
                break;
        }
        $wh = getimagesize($imgpath);
        $w = $wh[0];
        $h = $wh[1];
        $w = min($w, $h);
        $h = $w;
        $img = imagecreatetruecolor($w, $h);
        //这一句一定要有
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        $r = $w / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
        imagepng($img, $savefilename, 0);
        imagedestroy($img);
    }

    /*
     * 生成画笔
     * */
    public static function brush($one, $two, $three)
    {
        if (self::$canvas === null) return false;
        return imagecolorallocate(self::$canvas, $one, $two, $three);
    }

    /*
     * 粉丝介绍字体拆分为数组排列
     * */
    public static function getContentArray($str, $len = 4, $strlen = 24, $chaet = 'UTF-8')
    {
        $array = [];
        for ($i = 0; $i < $len; $i++) {
            array_push($array, '');
        }
        if (mb_strlen($str, $chaet) > $strlen) {
            for ($i = 0; $i < $len; $i++) {
                $lin = mb_substr($str, $i * $strlen, $strlen, $chaet);
                if ($lin) $array[$i] = $lin;
            }
        } else {
            $array[0] = $str;
        }
        return $array;
    }

}