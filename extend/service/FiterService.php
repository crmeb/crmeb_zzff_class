<?php
namespace service;

class Fiter
{
    private $dict;
    private $dictPath = '';
 
    public function __construct($dictPath = '')
    {
        $this->dict = array();
        $this->dictPath = $dictPath;
        $this->initDict();
    }
 
    private function initDict()
    {
        $handle = fopen($this->dictPath, 'r');
        if (!$handle) {
            throw new \Exception('未找到敏感词过滤文件');
        }
        while (!feof($handle)) {
            $word = trim(fgets($handle, 128));
            if (empty($word)) {
                continue;
            }
            $uWord = $this->unicodeSplit($word);
            $pdict = &$this->dict;
            $count = count($uWord);
            for ($i = 0; $i < $count; $i++) {
                if (!isset($pdict[$uWord[$i]])) {
                    $pdict[$uWord[$i]] = array();
                }
                $pdict = &$pdict[$uWord[$i]];
            }
            $pdict['end'] = true;
        }
        fclose($handle);
    }
 
    public function filter($str, $maxDistance = 5)
    {
        if ($maxDistance < 1) {
            $maxDistance = 1;
        }
        $uStr = $this->unicodeSplit($str);
        $count = count($uStr);
        for ($i = 0; $i < $count; $i++) {
            if (isset($this->dict[$uStr[$i]])) {
                $pdict = &$this->dict[$uStr[$i]];
                $matchIndexes = array();
                for ($j = $i + 1, $d = 0; $d < $maxDistance && $j < $count; $j++, $d++) {
                    if (isset($pdict[$uStr[$j]])) {
                        $matchIndexes[] = $j;
                        $pdict = &$pdict[$uStr[$j]];
                        $d = -1;
                    }
                }
                if (isset($pdict['end'])) {
                   // $uStr[$i] = '*';
                    $uStr[$i] = "<font color='red'>{$uStr[$i]}</font>";
                    foreach ($matchIndexes as $k) {
                        if ($k - $i == 1) {
                            $i = $k;
                        }
                        //$uStr[$k] = '*';
                        $uStr[$k] = "<font color='red'>{$uStr[$i]}</font>";
                    }
                }
            }
        }
        return implode($uStr);
    }
 
    public function unicodeSplit($str)
    {
        $str = strtolower($str);
        $ret = array();
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($str[$i]);
            if ($c & 0x80) {
                if (($c & 0xf8) == 0xf0 && $len - $i >= 4) {
                    if ((ord($str[$i + 1]) & 0xc0) == 0x80 && (ord($str[$i + 2]) & 0xc0) == 0x80 && (ord($str[$i + 3]) & 0xc0) == 0x80) {
                        $uc = substr($str, $i, 4);
                        $ret[] = $uc;
                        $i += 3;
                    }
                } else if (($c & 0xf0) == 0xe0 && $len - $i >= 3) {
                    if ((ord($str[$i + 1]) & 0xc0) == 0x80 && (ord($str[$i + 2]) & 0xc0) == 0x80) {
                        $uc = substr($str, $i, 3);
                        $ret[] = $uc;
                        $i += 2;
                    }
                } else if (($c & 0xe0) == 0xc0 && $len - $i >= 2) {
                    if ((ord($str[$i + 1]) & 0xc0) == 0x80) {
                        $uc = substr($str, $i, 2);
                        $ret[] = $uc;
                        $i += 1;
                    }
                }
            } else {
                $ret[] = $str[$i];
            }
        }
        return $ret;
    }
}