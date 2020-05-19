<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
if (!function_exists('unThumb')) {
    function unThumb($src)
    {
        return str_replace('/s_', '/', $src);
    }
}
/**
 *判断拼团是否结束*/
function isPinkStatus($pink)
{
    if (!$pink) return false;
    return \app\wap\model\store\StorePink::isSetPinkOver($pink);
}

/**
 *  设置浏览信息
 * @param $uid
 * @param int $product_id
 * @param int $cate
 * @param string $type
 * @param string $content
 * @param int $min
 */
function setView($uid, $product_id = 0, $cate = 0, $type = '', $product_type = 'product', $content = '', $min = 20)
{
    $Db = think\Db::name('store_visit');
    $view = $Db->where(['uid' => $uid, 'product_id' => $product_id, 'product_type' => $product_type])->field('count,add_time,id')->find();
    if ($view && $type != 'search') {
        $time = time();
        if (($view['add_time'] + $min) < $time) {
            $Db->where(['id' => $view['id']])->update(['count' => $view['count'] + 1, 'add_time' => time()]);
        }
    } else {
        $cate = explode(',', $cate)[0];
        $Db->insert([
            'add_time' => time(),
            'count' => 1,
            'product_id' => $product_id,
            'cate_id' => $cate,
            'type' => $type,
            'uid' => $uid,
            'product_type' => $product_type,
            'content' => $content
        ]);
    }
}


function Trust($URL)
{
    $isSrc = false;
    if (is_array($URL)) {
        $URL = isset($URL[4]) ? $URL[4] : '';
        $isSrc = true;
        $URL = strpos($URL, 'src=') !== false ? substr($URL, 4) : $URL;
    }
    if ($URL == '') return '';
    $URL = str_replace(['"', '\''], '', $URL);
    $strKs3 = 'ks3-cn-beijing.ksyun.com/buttomsup';
    $strKs4 = 'ks3-cn-beijing.ksyun.com/learn';
    if (strpos($URL, $strKs3) !== false) {
        $strCdn = str_replace($strKs3, 'buttomsup.dounixue.net', $URL);
    } else {
        $strCdn = str_replace($strKs4, 'cdn.dounixue.net', $URL);
    }
    $strCdn = str_replace('https', 'http', $strCdn);
    $slhttp = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https:' : 'http:';
    if ($slhttp == 'https:') {
        $strCdn = str_replace('http', 'https', $strCdn);
    } elseif ($slhttp == 'http:') {
        $strCdn = str_replace('https', 'http', $strCdn);
    }
    $pathinfo = pathinfo($URL);
    $t = substr(time(), 0, 10);
    $k = substr(md5('ptteng' . urldecode($pathinfo['filename']) . $t), 0, 16);
    if ($isSrc) {
        return 'scr="' . $strCdn . "?k={$k}&t={$t}\"";
    } else {
        return $strCdn . "?k={$k}&t={$t}";
    }

}

/**
 * 图片处理
 * @param $link
 * @param int $type 1=大图,2=宫图,3=全图,4=小图
 * @return string
 */
function get_oss_process($link, $type = 0)
{
    switch ($type) {
        case 1:
            $link .= '?x-oss-process=image/resize,m_lfit,h_254,w_690';
            break;
        case 2:
            $link .= '?x-oss-process=image/resize,m_lfit,h_200,w_330';
            break;
        case 3:
            $link .= '?x-oss-process=image/resize,m_lfit,h_130,w_219';
            break;
        case 4:
            $link .= '?x-oss-process=image/resize,m_lfit,h_254,w_690';
            break;
    }
    return $link;
}