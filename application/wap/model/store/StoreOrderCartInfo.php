<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
//

namespace app\wap\model\store;


use basic\ModelBasic;
use traits\ModelTrait;

class StoreOrderCartInfo extends ModelBasic
{
    use ModelTrait;

    public static function getCartInfoAttr($value)
    {
        return json_decode($value,true)?:[];
    }

    public static function setCartInfo($oid,array $cartInfo)
    {
        $group = [];
        foreach ($cartInfo as $cart){
            $group[] = [
                'oid'=>$oid,
                'cart_id'=>$cart['id'],
                'product_id'=>$cart['productInfo']['id'],
                'cart_info'=>json_encode($cart),
                'unique'=>md5($cart['id'].''.$oid)
            ];
        }
        return self::setAll($group);
    }

    public static function getProductNameList($oid)
    {
        $cartInfo = self::where('oid',$oid)->select();
        $goodsName = [];
        foreach ($cartInfo as $cart){
            $suk = isset($cart['cart_info']['productInfo']['attrInfo']) ? '('.$cart['cart_info']['productInfo']['attrInfo']['suk'].')' : '';
            $goodsName[] = $cart['cart_info']['productInfo']['store_name'].$suk;
        }
        return $goodsName;
    }

}