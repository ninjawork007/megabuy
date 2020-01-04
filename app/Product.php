<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attr;
use App\AttrVal;
use App\ProductAttrVal;
use App\ProductVariant;
use DB;

class Product extends Model {

    protected $table = 'au_product';
    protected $guarded = ['id'];
    public  $timestamps = true;



    public function getSimilarProductList()
    {
        $whereRaw = "id <> $this->id";
        return $this->where(array("seller_id"=>$this->seller_id, 'state'=>'1'))->whereRaw($whereRaw)->take(20)->get();
    }

    static public  function isDelete($id){
        return true;
    }

    public function getHomeStatisticsInfo($now, $lastWeek, $lastMonth){
        $ret = array();
        $where = "DATE_FORMAT(created_at, '%Y-%m-%d') = '".$now."'";
        $nowCount = $this->whereRaw($where)->count();
        $ret['nowCount'] = $nowCount;
        $where = "DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$lastWeek[2]."' AND DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$now."'";
        $weekCount = $this->whereRaw($where)->count();
        $ret['weekCount'] = $weekCount;
        $where = "DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$lastMonth[2]."' AND DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$now."'";
        $monthCount = $this->whereRaw($where)->count();
        $ret['monthCount'] = $monthCount;
        return $ret;
    }

    public function getProductStatisticsTimeLineData(){
        $ret = array();
        for($i = 5; $i<22; $i++){
            $H = sprintf("%02d", $i);
            $where = "DATE_FORMAT(created_at, '%H')='".$H."'";
            $count = $this->whereRaw($where)->count();
            array_push($ret, $count);
        }
        return $ret;
    }

    public function getAttrList($categoryId, $id = 0){
        $ret = array();
        $requireList = Attr::where(array("category_id"=>$categoryId, "is_variant"=>0,"is_require"=>1))->whereRaw("FIND_IN_SET(product_id, '0,".$id."')")->orderBy("product_id")->orderBy("order_num")->get();
        array_push($ret, $requireList);
        $additionalList = Attr::where(array("category_id"=>$categoryId, "is_variant"=>0,"is_require"=>0,))->whereRaw("FIND_IN_SET(product_id, '0,".$id."')")->orderBy("product_id")->orderBy("order_num")->get();
        array_push($ret, $additionalList);
        foreach($ret as $key => $eleList){
            foreach($eleList as $key1 => $item){
                $where = array("attr_id"=>$item['id'], "product_id"=>$id);
                $productAttrValInfo = ProductAttrVal::where($where)->first();
                if($item['attr_type']*1 == 0){
                    if(isset($productAttrValInfo['id'])){
                        $ret[$key][$key1]['textVal']= $productAttrValInfo['text_val'];
                    }else{
                        $ret[$key][$key1]['textVal']= '';
                    }
                }else{
                    $where = array("attr_id"=>$item['id']);
                    $valList = AttrVal::where($where)->get();
                    foreach($valList as $key2 => $item2){
                        $checked = 0;
                        if(isset($productAttrValInfo['id'])){
                            if(in_array($item2['id'], explode(",", $productAttrValInfo['val_ids']))){
                                $checked = 1;
                            }
                        }
                        $valList[$key2]['checked'] = $checked;
                    }
                    $ret[$key][$key1]['valList'] = $valList;

                }
            }
        }
        return $ret;
    }

    public function getVariantFieldList($product_id){
        $variantList = ProductVariant::where(array("product_id"=>$product_id, "state"=>1))->get();
        $ret = array();
        if(count($variantList)==0) return $ret;
        $item = $variantList[0];
        $varient_val_ids = $item['varient_val_ids'];
        if($varient_val_ids == null) return $ret;
        $varient_val_ids_a = explode(",", $varient_val_ids);
        foreach($varient_val_ids_a as $item1){
            $item1_a = explode("_", $item1);
            $attr_id = $item1_a[0];
            $attrInfo = Attr::find($attr_id);
            if(isset($attrInfo['id'])){
                array_push($ret, array('title'=>$attrInfo['title'], 'id'=>$attrInfo['id']));
            }
        }

        return $ret;
    }

    public function getVariantFieldList1($product_id = 0 , $variantList = array(), $attrList = array()){
        if(count($variantList) == 0){
            $variantList = ProductVariant::where(array("product_id"=>$product_id, "state"=>1))->get();
        }
        $ret = array();
        if(count($variantList)==0) {
            return $ret;
        }
        $item = $variantList[0];
        $varient_val_ids = $item['varient_val_ids'];
        if($varient_val_ids == null) return $ret;
        $varient_val_ids_a = explode(",", $varient_val_ids);
        foreach($varient_val_ids_a as $item1){
            $item1_a = explode("_", $item1);
            $attr_id = $item1_a[0];
            $attrInfo = Attr::find($attr_id);
            if(isset($attrInfo['id'])){
                array_push($ret, array('title'=>$attrInfo['title'], 'id'=>$attrInfo['id']));
            }else{
                foreach($attrList as $attr){
                    if($attr['attr_id']*1 == $attr_id){
                        array_push($ret, array('title'=>$attr['attr_name'], 'id'=>$attr['attr_id']));
                    }
                }
            }
        }

        return $ret;
    }


    public function getVariantValKeyArray($product_id){
        $variantList = ProductVariant::where(array("product_id"=>$product_id, "state"=>1))->get();
        $ret = array();
        if(count($variantList)==0) return $ret;
        foreach($variantList as $item) {
            $varient_val_ids = $item['varient_val_ids'];
            if ($varient_val_ids == null) continue;
            $varient_val_ids_a = explode(",", $varient_val_ids);
            foreach ($varient_val_ids_a as $item1) {
                $item1_a = explode("_", $item1);
                $val_id = $item1_a[1];
                $attrValInfo = AttrVal::find($val_id);
                if (isset($attrValInfo['id'])) {
                    $ret[$attrValInfo['id']] = $attrValInfo['val'];
                }
            }
        }
        return $ret;
    }

    public function getVariantValKeyArray1($product_id = 0,$variantList = array(), $attrList = array() ){
        if(count($variantList) ==0){
            $variantList = ProductVariant::where(array("product_id"=>$product_id, "state"=>1))->get();
        }
        $ret = array();
        if(count($variantList)==0) return $ret;

        foreach($variantList as $item) {
            $varient_val_ids = $item['varient_val_ids'];
            if ($varient_val_ids == null) continue;
            $varient_val_ids_a = explode(",", $varient_val_ids);
            foreach ($varient_val_ids_a as $item1) {
                $item1_a = explode("_", $item1);
                $val_id = $item1_a[1];
                $attrValInfo = AttrVal::find($val_id);
                if (isset($attrValInfo['id'])) {
                    $ret[$attrValInfo['id']] = $attrValInfo['val'];
                }else{
                    foreach($attrList as $attr){
                        if($attr['attr_id']*1 == $item1_a[0]*1){
                            foreach($attr['list'] as $valItem){
                                if($valItem['val_id']*1 == $val_id){
                                    $ret[$val_id] =$valItem['val_name'];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $ret;
    }

    static public function getProductPrice($product_id, $sku_id){
        if($sku_id*1 ==0){
           $info = Product::find($product_id);
           return $info['price'];
        }else{
            $info = ProductVariant::find($sku_id);
            return $info['price'];
        }
    }

    public function getCommonPropertierStr($product_id = 0){
        if(isset($this->id)) $product_id = $this->id;
        $ret = '';
        $valList = ProductAttrVal::where(array("product_id"=>$product_id))->get();
        foreach($valList as $item){
            $attrTitle = Attr::getAttrTitle($item['attr_id']);
            if($item['text_val'] != ''){
                $val = $item['text_val'];
            }else{
                $val = AttrVal::getAttrVals($item['val_ids']);
            }

            $ret.= ($ret == ""? "": ","). $attrTitle.":".$val;
        }
        return $ret;
    }

    public function getCommonPropertierHtml(){
        $html = '';
        $valList = ProductAttrVal::where(array("product_id"=>$this->id))->get();
        foreach($valList as $item){
            $attrTitle = Attr::getAttrTitle($item['attr_id']);
            if($item['text_val'] != ''){
                $val = $item['text_val'];
            }else{
                $val = AttrVal::getAttrVals($item['val_ids']);
            }
            $eleHtml = "<li class=\"font-12 color-black text-right mr-10\" style=\"width:20%; vertical-align: top;\">".$attrTitle." : </li>";
            $eleHtml .= "<li class=\"font-12 color-black\" style=\"vertical-align: top;word-break:break-all; width:calc(30% - 10px);\">".str_limit($val,30)."</li>";

            $html.= $eleHtml;
        }
        return $html;
    }


    public function category() {
        return $this->hasOne('App\ProductCategory', 'id', 'category_id');
    }

    public function brand() {
        return $this->hasOne('App\Brand', 'id', 'brand_id');
    }

    public function getVariantCount($id){
        $cnt = ProductVariant::where(array("product_id"=>$id))->count();
        return $cnt;
    }

    public function seller(){
        return  $this->hasOne('App\User', 'id', 'seller_id');
    }

    public function getMinMax(){
        $info = ProductVariant::select(DB::raw("IFNULL(MIN(price),0) minPrice,IFNULL(MAX(price),0) maxPrice"))->whereRaw("product_id =".$this->id)->first();
        if($info['minPrice']==$info['maxPrice']){
            return null;
        }

        return $info;

    }

    static public function deleteItem($id){
        Product::where(array("id"=>$id))->delete();
        ProductVariant::where(array("product_id"=>$id))->delete();
        ProductDescription::where(array("id"=>$id))->delete();
        Attr::where(array("product_id"=>$id))->delete();
        AttrVal::where(array("product_id"=>$id))->delete();
        ProductImg::where(array("product_id"=>$id))->delete();
    }

    public function getProductPagePrice(){
        $info1 = Product::find($this->id);
        $info = ProductVariant::select(DB::raw("IFNULL(MIN(price),0) minPrice,IFNULL(MAX(price),0) maxPrice"))->whereRaw("product_id =".$this->id)->first();
        if($info['minPrice']==$info['maxPrice']){
            return "$".$info1['price'];
        }else{
            return  $info['minPrice']."$-".$info['maxPrice']."$";
        }
    }

    public function getBrandHtml(){
        $info = Brand::find($this->brand_id);
        $ret = '';
        if(isset($info['id'])){
            $ret = '<img src = "'.correctImgPath($info['log_img']).'" style = "width:20px;" onerror = "noExitImg(this)"/>&nbsp;&nbsp;&nbsp;'.'<span class="bold font-14">'.str_limit($info['title'],10).'</span>';
        }
        return $ret;
    }

    public function getBrandTitle(){
        $info = Brand::find($this->brand_id);
        $ret = '';
        if(isset($info['id'])){
            $ret = str_limit($info['title'],10);
        }
        return $ret;
    }

    public function getProductImgCount(){
        $ret = 0;
        $imgCount = ProductImg::where("product_id", $this->id)->count();
        $ret += $imgCount;
        if($this->img != ''){
            $ret++;
        }
        return $ret;
    }

    public function getProductImgFileList(){
        $ret = array();
        $list = ProductImg::where("product_id", $this->id)->get();
        foreach($list as $item){
            if($item['img'] != ''){
                array_push($ret, $item['img']);
            }

        }
        if($this->img != ''){
            array_push($ret, $this->img);
        }
        return $ret;
    }

    public function getCategoryIds($id){
        $ids = DB::select("select distinct category_id from au_product where brand_id = ?",[$id]);
        $ids = convertAttrToArray($ids);
        return $ids;
    }




}
