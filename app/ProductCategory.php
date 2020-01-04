<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model {

    protected $table = 'au_product_category';
    protected $guarded = ['id'];
    public  $timestamps = true;

    protected $appends = ['category_text'];
    public function getCategoryTextAttribute()
    {
        if($this->show_text == ''){
            return str_limit($this->title, 30);
        }else{
            return str_limit($this->show_text, 30);
        }
    }

    public function getParentPath($parent_id){
        $parentInfo = ProductCategory::find($parent_id);
        if(!isset($parentInfo['id'])){
            return "";
        }else{
            return $parentInfo['path'].">";
        }
    }
    public function getParentIds($parent_id){
        $parentInfo = ProductCategory::find($parent_id);
        if(!isset($parentInfo['id'])){
            return "";
        }else{
            return $parentInfo['parent_ids'].",".$parent_id;
        }
    }

    static public function getCategoryPath($category_id){
        $categoryInfo = ProductCategory::find($category_id);
        $ret = array();
        $parentIds = explode(",", $categoryInfo["parent_ids"]);
        $i = 0;
        foreach ($parentIds as $parent){
            $parentInfo = ProductCategory::find($parent);
            $ret[$i]["id"] = $parentInfo["id"];
            $ret[$i]["title"] = $parentInfo["title"];
        }
        return $ret;
    }
    static public  function isDelete($id){
        return true;
    }

    static public  function deleteItem($id){
        ProductCategory::whereRaw("FIND_IN_SET('".$id."', parent_ids)")->delete();
        ProductCategory::where(array("id"=>$id))->delete();
    }

    public function getCategoryAllList(){
        $list = $this->where("parent_id", 0)->get();
        foreach($list as $key => $rootCategory){
            $id = $rootCategory["id"];
            $leavedCategoryList = $this
                ->whereRaw("FIND_IN_SET($id, parent_ids)")
                ->where("is_leaved", 1)
                ->get();
            $list[$key]["leavedCategoryList"] = $leavedCategoryList;
        }
        return $list;
    }

    public function getLeafCategoryList($root_id){
        $whereRaw = "FIND_IN_SET($root_id,parent_ids)";
        $where = array("is_leaved"=> "1");
        $list = ProductCategory::whereRaw($whereRaw)->where($where)->get();
        return $list;
    }

    public function getLeafCategoryMainShowList($root_id){
        $whereRaw = "FIND_IN_SET($root_id,parent_ids)";
        $where = array("is_leaved"=> "1", "is_main"=>"1");
        $list = ProductCategory::whereRaw($whereRaw)->where($where)->get();
        return $list;
    }

    public function getLeafCategoryMainNoneList($root_id){
        $whereRaw = "FIND_IN_SET($root_id,parent_ids)";
        $where = array("is_leaved"=> "1", "is_main"=>"0");
        $list = ProductCategory::whereRaw($whereRaw)->where($where)->get();
        return $list;
    }

    public function getRootId($id){
        $info = ProductCategory::find($id);
        $parent_ids = $info['parent_ids'];
        $parent_ids_a = explode(",", $parent_ids);
        if($parent_ids_a[0] != ''){
            return $parent_ids_a[0];
        }else{
            return $parent_ids_a[1];
        }
    }

    public function isAppendMainCategory($id){
        $root_id = $this->getRootId($id);
        $whereRaw = "id<>$id AND FIND_IN_SET($root_id, parent_ids)";
        $count = ProductCategory::where(array("is_leaved"=>"1", "is_main"=>'1'))->whereRaw($whereRaw)->count();
        if($count > 5){
            return false;
        }else{
            return true;
        }
    }



}
