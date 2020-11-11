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

class StoreCart extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function setCart($uid,$product_id,$cart_num = 1,$product_attr_unique = '',$type='product',$is_new = 0,$combination_id=0,$seckill_id = 0,$bargain_id = 0,$integral_id=0)
    {
        if($cart_num < 1) $cart_num = 1;
            if(!StoreProduct::isValidProduct($product_id))
                return self::setErrorInfo('该产品已下架或删除');
            if(!StoreProductAttr::issetProductUnique($product_id,$product_attr_unique))
                return self::setErrorInfo('请选择有效的产品属性');
            if(StoreProduct::getProductStock($product_id,$product_attr_unique) < $cart_num)
                return self::setErrorInfo('该产品库存不足'.$cart_num);
        $where = [
            'type'=>$type,
            'uid'=>$uid,
            'product_id'=>$product_id,
            'product_attr_unique'=>$product_attr_unique,
            'is_new'=>$is_new,
            'is_pay'=>0,
            'is_del'=>0,
            'combination_id'=>0,
            'seckill_id'=>0,
            'integral_id'=>0
        ];
        if($cart = self::where($where)->find()){
            $cart->cart_num = $cart_num;
            $cart->add_time = time();
            $cart->save();
            return $cart;
        }else{
            return self::set(compact('uid','product_id','cart_num','product_attr_unique','is_new','type','combination_id','integral_id'));
        }

    }

    public static function removeUserCart($uid,$ids)
    {
        return self::where('uid',$uid)->where('id','IN',$ids)->update(['is_del'=>1]);
    }

    public static function getUserCartNum($uid,$type)
    {
        return self::where('uid',$uid)->where('type',$type)->where('is_pay',0)->where('is_del',0)->where('is_new',0)->count();
    }

    public static function changeUserCartNum($cartId,$cartNum,$uid)
    {
        return self::where('uid',$uid)->where('id',$cartId)->update(['cart_num'=>$cartNum]);
    }

    public static function getUserProductCartList($uid,$cartIds='',$status=0)
    {
        $productInfoField = 'id,image,slider_image,price,cost,ot_price,vip_price,postage,mer_id,give_integral,cate_id,sales,stock,store_name,unit_name,is_show,is_del,is_postage';
        $model = new self();
        $valid = $invalid = [];
        if($cartIds)
            $model = $model->where('uid',$uid)->where('type','product')->where('is_pay',0)
                ->where('is_del',0);
        else
            $model = $model->where('uid',$uid)->where('type','product')->where('is_pay',0)->where('is_new',0)
                ->where('is_del',0);
        if($cartIds) $model->where('id','IN',$cartIds);
        $list = $model->select()->toArray();
        if(!count($list)) return compact('valid','invalid');
        $is_vip=false;
        foreach ($list as $k=>$cart) {
                $product = StoreProduct::field($productInfoField)
                    ->find($cart['product_id'])->toArray();

            $cart['productInfo'] = $product;
            //商品不存在
            if (!$product) {
                $model->where('id', $cart['id'])->update(['is_del' => 1]);
                //商品删除或无库存
            } else if (!$product['is_show'] || $product['is_del'] || !$product['stock']) {
                $invalid[] = $cart;
                //商品属性不对应并且没有seckill_id
            } else if (!$cart['integral_id'] && !$cart['bargain_id'] && !$cart['seckill_id'] && !StoreProductAttr::issetProductUnique($cart['product_id'], $cart['product_attr_unique'])) {
                $invalid[] = $cart;
                //正常商品
            } else {
                if ($status) {
                    if ($cart['product_attr_unique']) {
                        $attrInfo = StoreProductAttr::uniqueByAttrInfo($cart['product_attr_unique']);
                        //商品没有对应的属性
                        if (!$attrInfo || !$attrInfo['stock']){
                            $invalid[] = $cart;
                        }else {
                            $cart['productInfo']['attrInfo'] = $attrInfo;
                            $cart['truePrice'] = $is_vip ? (isset($attrInfo['vip_price']) ? (float)$attrInfo['vip_price']: (float)$attrInfo['price']) :(float)$attrInfo['price'];
                            $cart['costPrice'] = (float)$attrInfo['cost'];
                            $cart['trueStock'] = $attrInfo['stock'];
                            $cart['productInfo']['image'] = empty($attrInfo['image']) ? $cart['productInfo']['image'] : $attrInfo['image'];
                            $valid[] = $cart;
                        }
                    } else {
                        $cart['truePrice'] =$is_vip ? (isset($cart['productInfo']['vip_price']) ? (float)$cart['productInfo']['vip_price']: (float)$cart['productInfo']['price']) : (float)$cart['productInfo']['price'];
                        $cart['costPrice'] = (float)$cart['productInfo']['cost'];
                        $cart['trueStock'] = $cart['productInfo']['stock'];
                        $valid[] = $cart;
                    }
                } else {
                    if ($cart['seckill_id'] == 0) {
                        if ($cart['product_attr_unique']) {
                            $attrInfo = StoreProductAttr::uniqueByAttrInfo($cart['product_attr_unique']);
                            //商品没有对应的属性
                            if (!$attrInfo || !$attrInfo['stock'])
                                $invalid[] = $cart;
                            else {
                                $cart['productInfo']['attrInfo'] = $attrInfo;
                                $cart['truePrice'] =  $is_vip ? (isset($attrInfo['vip_price']) ? (float)$attrInfo['vip_price']: (float)$attrInfo['price']) :(float)$attrInfo['price'];
                                $cart['costPrice'] = (float)$attrInfo['cost'];
                                $cart['trueStock'] = $attrInfo['stock'];
                                $cart['productInfo']['image'] = empty($attrInfo['image']) ? $cart['productInfo']['image'] : $attrInfo['image'];
                                $valid[] = $cart;
                            }
                        } else {
                            $cart['truePrice'] = $is_vip ? (isset($cart['productInfo']['vip_price']) ? (float)$cart['productInfo']['vip_price']: (float)$cart['productInfo']['price']) : (float)$cart['productInfo']['price'];
                            $cart['costPrice'] = (float)$cart['productInfo']['cost'];
                            $cart['trueStock'] = $cart['productInfo']['stock'];
                            $valid[] = $cart;
                        }
                    }

                }

            }
        }
        foreach ($valid as $k=>$cart){
            if($cart['trueStock'] < $cart['cart_num']){
                $cart['cart_num'] = $cart['trueStock'];
                $model->where('id',$cart['id'])->update(['cart_num'=>$cart['cart_num']]);
                $valid[$k] = $cart;
            }
        }
        return compact('valid','invalid');
    }


}