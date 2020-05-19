<?php
namespace app\admin\model\store;

use traits\ModelTrait;
use basic\ModelBasic;
use service\PHPExcelService;
use app\admin\model\store\StoreCategory as CategoryModel;
use think\Db;
/**
 * 积分产品管理 model
 * Class IntegralProduct
 * @package app\admin\model\store
 */
class IntegralProduct extends ModelBasic
{
    use ModelTrait;


    /**
     * 获取连表查询条件
     * @param $type
     * @return array
     */
    public static function setData($type){
        switch ((int)$type){
            case 1:
                $data = ['p.is_show'=>1,'p.is_del'=>0];
                break;
            case 2:
                $data = ['p.is_show'=>0,'p.is_del'=>0];
                break;
            case 3:
                $data = ['p.is_del'=>0];
                break;
            case 4:
                $data = ['p.is_show'=>1,'p.is_del'=>0,'pav.stock|p.stock'=>0];
                break;
            case 5:
                $data = ['p.is_show'=>1,'p.is_del'=>0,'pav.stock|p.stock'=>['elt',1]];
                break;
            case 6:
                $data = ['p.is_del'=>1];
                break;
        };
        return isset($data) ? $data: [];
    }
    /**
     * 获取连表MOdel
     * @param $model
     * @return object
     */
    public static function getModelObject($where=[]){
        $model=new self();
        $model=$model->alias('p')->join('integral_product_attr_value pav','p.id=pav.product_id','LEFT');
        if(!empty($where)){
            $model=$model->group('p.id');
            if(isset($where['type']) && $where['type']!='' && ($data=self::setData($where['type']))){
                $model = $model->where($data);
            }
            if(isset($where['store_name']) && $where['store_name']!=''){
                $model = $model->where('p.store_name|p.keyword|p.id','LIKE',"%$where[store_name]%");
            }
            if(isset($where['cate_id']) && trim($where['cate_id'])!=''){
                $model = $model->where('p.cate_id','LIKE',"%$where[cate_id]%");
            }
            if(isset($where['order']) && $where['order']!=''){
                $model = $model->order(self::setOrder($where['order']));
            }
        }
        return $model;
    }
    /*
     * 获取产品列表
     * @param $where array
     * @return array
     *
     */
    public static function ProductList($where){
        $model=self::getModelObject($where)->field(['p.*','sum(pav.stock) as vstock']);
        if($where['excel']==0) $model=$model->page((int)$where['page'],(int)$where['limit']);
        $data=($data=$model->select()) && count($data) ? $data->toArray():[];
        foreach ($data as &$item){
            $cateName = CategoryModel::where('id','IN',$item['cate_id'])->column('cate_name','id');
            $item['cate_name']=is_array($cateName) ? implode(',',$cateName) : '';
            $item['collect'] = StoreProductRelation::where('product_id',$item['id'])->where('type','collect')->count();//收藏
            $item['like'] = StoreProductRelation::where('product_id',$item['id'])->where('type','like')->count();//点赞
            $item['stock'] = self::getStock($item['id'])>0?self::getStock($item['id']):$item['stock'];//库存
            $item['stock_attr'] = self::getStock($item['id'])>0 ? true : false;//库存
            $item['sales_attr'] = self::getSales($item['id']);//属性销量
            $item['visitor'] = Db::name('store_visit')->where('product_id',$item['id'])->where('product_type','product')->count();
        }
        if($where['excel']==1){
            $export = [];
            foreach ($data as $index=>$item){
                $export[] = [
                    $item['store_name'],
                    $item['store_info'],
                    $item['cate_name'],
                    '￥'.$item['price'],
                    $item['stock'],
                    $item['sales'],
                    $item['like'],
                    $item['collect']
                ];
            }
            PHPExcelService::setExcelHeader(['产品名称','产品简介','产品分类','价格','库存','销量','点赞人数','收藏人数'])
                ->setExcelTile('产品导出','产品信息'.time(),' 生成时间：'.date('Y-m-d H:i:s',time()))
                ->setExcelContent($export)
                ->ExcelSave();
        }
        $count=self::getModelObject($where)->count();
        return compact('count','data');
    }
    //获取库存数量
    public static function getStock($productId)
    {
        return IntegralProductAttrValue::where(['product_id'=>$productId])->sum('stock');
    }
    //获取总销量
    public static function getSales($productId)
    {
        return IntegralProductAttrValue::where(['product_id'=>$productId])->sum('sales');
    }
}
