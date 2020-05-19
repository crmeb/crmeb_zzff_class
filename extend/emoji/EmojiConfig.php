<?php

namespace emoji;

class EmojiConfig
{

    protected static $emojiList =[
        [0x1f600],
        [0x1f601],
        [0x1f602],
        [0x1f603],
        [0x1f604],
        [0x1f605],
        [0x1f606],
        [0x1f607],
        [0x1f608],
        [0x1f60a],
        [0x1f60b],
        [0x1f60c],
        [0x1f60d],
        [0x1f60e],
        [0x1f60f],
        [0x1f610],
        [0x1f611],
        [0x1f612],
        [0x1f613],
        [0x1f614],
        [0x1f615],
        [0x1f616],
        [0x1f617],
        [0x1f618],
        [0x1f619],
        [0x1f619],
        [0x1f61a],
        [0x1f61b],
        [0x1f61c],
        [0x1f61d],
        [0x1f61e],
        [0x1f61f],
        [0x1f620],
        [0x1f621],
        [0x1f622],
        [0x1f623],
        [0x1f624],
        [0x1f625],
        [0x1f626],
        [0x1f627],
        [0x1f628],
        [0x1f629],
        [0x1f62a],
        [0x1f62b],
        [0x1f62c],
        [0x1f62d],
        [0x1f62e],
        [0x1f62f],
        [0x1f630],
        [0x1f631],
        [0x1f632],
        [0x1f633],
        [0x1f634],
        [0x1f635],
        [0x1f636],
        [0x1f637],
        [0x1f638],
        [0x1f639],
        [0x1f63a],
        [0x1f63b],
        [0x1f63c],
        [0x1f63d],
        [0x1f63e],
        [0x1f63f],
        [0x1f640],
        [0x1f641],
        [0x1f642],
        [0x1f643],
        [0x1f644],
        [0x1f645],
        [0x1f646],
        [0x1f647],
        [0x1f648],
        [0x1f649],
        [0x1f64a],
        [0x1f64b],
        [0x1f64c],
        [0x1f64d],
        [0x1f64e],
        [0x1f64f],
        [0x1f680],
        [0x1f681],
        [0x1f682],
        [0x1f683],
        [0x1f684],
        [0x1f685],
        [0x1f686],
        [0x1f687],
        [0x1f688],
        [0x1f689],
        [0x1f68a],
        [0x1f68b],
        [0x1f68c],
        [0x1f68d],
        [0x1f68e],
        [0x1f68f],
        [0x1f690],
        [0x1f691],
        [0x1f692],
        [0x1f693],
        [0x1f694],
        [0x1f695],
        [0x1f696],
        [0x1f697],
        [0x1f698],
        [0x1f699],
        [0x1f69a],
        [0x1f69b],
        [0x1f69c],
        [0x1f69d],
        [0x1f69e],
        [0x1f69f],
    ];

    public static function getEmjiHtmlList($slicing = 20)
    {
        require_once(ROOT_PATH.'extend/emoji/Emoji.php');
        $list = [];
        foreach (self::$emojiList as $unified){
            $bytes='';
            foreach ($unified as $cp){
                $bytes .= self::utf8_bytes($cp);
            }
            $data = [
                'html'=>emoji_unified_to_html($bytes),
                'unified'=>emoji_html_to_unified(emoji_unified_to_html($bytes))
            ];
            $list[] = $data;
            unset($data);
        }
        return array_chunk($list,$slicing,true);
    }

    public static function utf8_bytes($cp){

        if ($cp > 0x10000){
            # 4 bytes
            return	chr(0xF0 | (($cp & 0x1C0000) >> 18)).
                chr(0x80 | (($cp & 0x3F000) >> 12)).
                chr(0x80 | (($cp & 0xFC0) >> 6)).
                chr(0x80 | ($cp & 0x3F));
        }else if ($cp > 0x800){
            # 3 bytes
            return	chr(0xE0 | (($cp & 0xF000) >> 12)).
                chr(0x80 | (($cp & 0xFC0) >> 6)).
                chr(0x80 | ($cp & 0x3F));
        }else if ($cp > 0x80){
            # 2 bytes
            return	chr(0xC0 | (($cp & 0x7C0) >> 6)).
                chr(0x80 | ($cp & 0x3F));
        }else{
            # 1 byte
            return chr($cp);
        }
    }

}