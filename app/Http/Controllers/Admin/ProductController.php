<?php namespace App\Http\Controllers\Admin;

use App\Attr;
use App\AttrVal;
use App\ProductCategory;
use App\Product;
use App\Brand;
use App\ProductAttrVal;
use App\ProductImg;
use App\ProductVariant;
use App\ProductDescription;
use App\RoleUsers;
use Sentinel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ProductController extends Controller
{
    public function index()
    {
        $roleUsers = new RoleUsers();
        $sellerList = $roleUsers->getSellerList();
        view()->share('sellerList',$sellerList);
        return view('admin.product.index');
    }



    public function ajaxVariantTableList(Request $request){
        $attrList = $request->get("attrList", '');
        $product_id = $request->get('product_id', '0');
        $attrList = json_decode($attrList,1);
        $variant_val_ids_a = array();
        foreach($attrList as $key1 => $item1){
            $variant_val_ids_first = $item1['attr_id'];
            if(count($variant_val_ids_a) == 0){
                foreach($item1['list'] as $item2) {
                    $variant_val_ids_seconde = $item2['val_id'];
                    array_push($variant_val_ids_a, $variant_val_ids_first . "_" . $variant_val_ids_seconde);
                }
            }else{
                $variant_val_ids_a1 = $variant_val_ids_a;
                $variant_val_ids_a = array();
                foreach($variant_val_ids_a1 as $item2){
                    foreach($item1['list'] as $item3) {
                        $variant_val_ids_seconde = $item3['val_id'];
                        array_push($variant_val_ids_a, $item2.",".$variant_val_ids_first . "_" .$variant_val_ids_seconde );
                    }
                }
            }
        }
        $variant_val_ids_a1 = array();
        foreach($variant_val_ids_a as $item){
            $item1 = ProductVariant::where(array("product_id"=>$product_id,'varient_val_ids'=>$item))->first();
            if(!isset($item1['id'])){
                $item1 = new ProductVariant();
            }
            $item1['varient_val_ids'] = $item;
            array_push($variant_val_ids_a1, $item1);
        }
        $product = new Product();
        view()->share("variantList", $variant_val_ids_a1);
        view()->share("variantFieldList", $product->getVariantFieldList1($product_id, $variant_val_ids_a1,$attrList));
        view()->share("variantValKeyArray", $product->getVariantValKeyArray1($product_id,$variant_val_ids_a1,$attrList));
        return view("admin.product.variant_product_list");
    }

    public function customize_variant_content(Request $request){
        $data = $request->get('data');
        $data = json_decode($data,1);
        view()->share('data', $data);
        return view('admin.product.customize_variant_content');
    }

    public function ajaxCategoryList(Request $request){
        $search = $request -> get("search");
        $categoyList = ProductCategory::where("is_leaved",1)->whereRaw("path LIKE '%$search%'")->orderBy('parent_id')->orderBy("id")->orderBy("order_num")->get();
        view()->share("categoryList", $categoyList);
        view()->share("search", $search);
        return view('admin.product.category_list');
    }

    public function ajaxProductList(){
        $pageParam = $this->getPageParam();
        $search = $this->getParam("search", "");
        $categoryId = $this->getParam('categoryId', '0');
        $model = new Product();
        if($search != ''){
            $model = $model->whereRaw("title LIKE '%".$search."%'");
        }

        if($categoryId*1 != 0){
            $model = $model->where('category_id', $categoryId);
        }
        $count = $model->count();
        $productList = $model->skip($pageParam["start"])->take($pageParam["perPageSize"])->get();
        $pageParam = $this->setPageParam($pageParam, $count);
        view()->share('pageParam', $pageParam);
        view()->share("productList", $productList);
        return view("admin.product.product_list");
    }

    public function productInfo($id, $categoryId){
        if($id *1 == 0){
            $product = new Product();
        }else{
            $product = Product::find($id);
        }
        view()->share("product", $product);
        view()->share("category_id", $categoryId);
        $list = $product->getAttrList($categoryId, $id);
        view()->share("attrList", $list);
        $variantList = ProductVariant::where(array("product_id"=>$id, "state"=>1))->get();
        $imgList = ProductImg::where(array("product_id"=>$id))->orderBy("order_num")->get();
        $imgList1 = array();
        for($i=0;$i<12;$i++){
            $imgList1[$i] = '';
        }
        $k = 0;
        foreach($imgList as $item){
            $imgList1[$item['order_num']] = $item['img'];
            $k=$item['order_num'];
        }
        view()->share("imgList", $imgList1);
        $description = "";
        $info = ProductDescription::find($id);
        if(isset($info['id'])){
            $description = $info['description'];
        }
        view()->share("description", $description);
        view()->share("imgListMax", $k);
        view()->share("variantList", $variantList);
        view()->share("variantFieldList", $product->getVariantFieldList($id));
        view()->share("variantValKeyArray", $product->getVariantValKeyArray($id));
        view()->share("brandList", Brand::all());
        $roleUsers = new RoleUsers();
        view()->share("sellerList", $roleUsers->getSellerList());
        return view("admin.product.product_info");
    }

    public function ajaxSaveProductInfo(Request $request){
        $param = $request->get('param');
        $param = json_decode($param,1);
        $param_product = $param['product'];
        if($param_product['id']*1 == 0){
            $product = new Product();
            foreach($param_product as $key =>$val){
                $product[$key] = $val;
            }
        }else{
            $product = Product::find($param_product['id']);
        }
        foreach($param_product as $key => $val){
            if($key != 'id'){
                $product[$key] = $val;
            }
        }

        $product->save();
        $product_id = $product['id'];
        $product_only_attr_arr = $param['product_only_attr_arr'];
        $old_attr_list = array();
        foreach($product_only_attr_arr as $product_only_attr){
            $attr = new Attr();
            $attr['product_id'] = $product_id;
            $attr['title'] = $product_only_attr['title'];
            $attr['attr_type'] = $product_only_attr['attr_type'];
            $attr['category_id'] = $product['category_id'];
            $attr['order_num'] = "100";
            $attr['is_require'] = $product_only_attr['is_require'];
            $attr->save();
            $old_attr_list[$product_only_attr['id']] = $attr['id'];
        }

        $old_attr_val_list = array();
        $product_only_attr_val_arr = $param['product_only_attr_val_arr'];
        foreach($product_only_attr_val_arr as $product_only_attr_val){
            $attr_val = new AttrVal();
            $attr_id = $product_only_attr_val['attr_id'];
            if(isset($old_attr_list[$product_only_attr_val['attr_id']])){
                $attr_id = $old_attr_list[$product_only_attr_val['attr_id']];
            }
            $attr_val['attr_id'] = $attr_id;
            $attr_val['val'] = $product_only_attr_val['val'];
            $attr_val['product_id'] = $product_id;
            $attr_val->save();
            $old_attr_val_list[$product_only_attr_val['id']] = $attr_val['id'];
        }



        $product_attr_val_arr1 = $param['product_attr_val_arr'];
        foreach($product_attr_val_arr1 as $key => $item){
            if($item['attr_id'] *1 <0){
                $product_attr_val_arr1[$key]['attr_id'] = $old_attr_list[$item['attr_id']];
            }
        }
        $this->removeProductAttrVal($product_attr_val_arr1, $product_id);

        foreach($product_attr_val_arr1 as $item){
            $product_attr_val = ProductAttrVal::where(array("product_id"=>$product_id,"attr_id"=>$item['attr_id']))->first();
            if(!isset($product_attr_val['id'])){
                $product_attr_val = new ProductAttrVal();
            }
            $product_attr_val['product_id'] = $product_id;
            foreach($item as $key => $val){
                if($key != 'attr_id' && $key != 'is_required' && $key !='attr_name' ){
                    $product_attr_val[$key] = $val;
                }

                if($key == 'attr_id'){
                    if(isset($old_attr_list[$item['attr_id']])){
                        $product_attr_val['attr_id'] = $old_attr_list[$item['attr_id']];
                    }else{
                        $product_attr_val['attr_id'] = $item['attr_id'];
                    }
                }

                if($key == 'val_ids' && $item['val_ids'] != ''){
                    $val_ids = $item['val_ids'];
                    $val_ids_a = explode(",", $val_ids);
                    for($i=0; $i<count($val_ids_a); $i++){
                        if($val_ids_a[$i]*1 < 0){
                            if(isset($old_attr_val_list[$val_ids_a[$i]])){
                                $val_ids_a[$i] = $old_attr_val_list[$val_ids_a[$i]];
                            }

                        }
                    }
                    $val_ids = implode(",", $val_ids_a);
                    $product_attr_val['val_ids'] = $val_ids;
                }
            }


            $product_attr_val->save();
        }

        $product_variant_arr = $param['product_variant_arr'];
        $this->removeProductVariantVal($product_variant_arr, $product_id);
        foreach($product_variant_arr as $product_variant){
            $info = ProductVariant::where("varient_val_ids", $product_variant['varient_val_ids'])->first();
            if(!isset($info['id'])){
                $varient_val_ids = $product_variant['varient_val_ids'];
                $varient_val_ids_a = explode(",",$varient_val_ids);
                foreach($varient_val_ids_a as $key1 => $varient_val_ids_ele){
                    $varient_val_ids_ele_a = explode("_", $varient_val_ids_ele);
                    if($varient_val_ids_ele_a[0]*1 < -1){
                        if(isset($old_attr_list[$varient_val_ids_ele_a[0]])){
                            $varient_val_ids_ele_a[0] = $old_attr_list[$varient_val_ids_ele_a[0]];
                        }
                    }

                    if($varient_val_ids_ele_a[1]*1 < -1){
                        if(isset($old_attr_val_list[$varient_val_ids_ele_a[1]])){
                            $varient_val_ids_ele_a[1] = $old_attr_val_list[$varient_val_ids_ele_a[1]];
                        }
                    }
                    $varient_val_ids_a[$key1] = implode("_", $varient_val_ids_ele_a);
                }
                $varient_val_ids = implode(",", $varient_val_ids_a);
                $info = new ProductVariant();
                $info['varient_val_ids'] = $varient_val_ids;
            }

            foreach($product_variant as $key => $val){
                if($key != 'varient_val_ids' && $key != 'id'){
                    $info[$key] = $val;
                }
            }
            $info['product_id'] = $product_id;
            $info->save();
        }

        $this->uploadProductImage($request, $product_id);

        $description = $request->get('description', '');
        $productDescription = ProductDescription::find($product_id);
        if(!isset($productDescription['id'])){
            $productDescription = new ProductDescription();
            $productDescription['id'] = $product_id;
        }
        $productDescription['description'] = $description;

        $productDescription ->save();

        echo json_encode(array('status'=>1, 'msg'=>'The operation success'));
    }

    public function uploadProductImage(Request $request, $product_id){
        $mainProductImg_val = $request->get('mainProductImg_val','');
        $path = $mainProductImg_val;
        if($mainProductImg_val != '' && 0 !== strpos($mainProductImg_val, 'http')){
            $path = $this->genImage($mainProductImg_val);
        }
        $product = Product::find($product_id);
        $product['img'] = $path;
        $product->save();
        for($i=0; $i<12 ; $i++){
            $img = $request->get('mainProductImg'.$i.'_val', '');
            $path = $img;
            if($img != '' && 0 !== strpos($img, 'http')){
                $path = $this->genImage($img);
            }
            if($path == ''){
                $info = ProductImg::where(array('product_id'=>$product, 'order_num'=>$i))->first();
                if(isset($info['img']) && $info['img'] != ''){
                    @unlink(public_path($info['img']));
                }
                ProductImg::where(array('product_id'=>$product, 'order_num'=>$i))->delete();
            }else{
                $productImg = ProductImg::where(array('product_id'=>$product, 'order_num'=>$i))->first();
                if(!isset($productImg['id'])){
                    $productImg = new ProductImg();
                }else{
                    if(isset($productImg['img']) && $productImg['img'] != ''){
                        @unlink(public_path($productImg['img']));
                    }
                }

                $productImg['product_id'] = $product_id;
                $productImg['img'] = $path;
                $productImg['order_num'] = $i;
                $productImg->save();
            }
        }
    }



    public function removeProductAttrVal($product_attr_val_arr, $product_id){
        $attr_ids = "";
        foreach($product_attr_val_arr as $item){
            $attr_ids .= ("" == $attr_ids ? "":",").$item['attr_id'];
        }
        ProductAttrVal::whereRaw("!FIND_IN_SET(attr_id, '".$attr_ids."')")->where("product_id", $product_id)->delete();

        Attr::whereRaw("!FIND_IN_SET(id, '".$attr_ids."') AND category_id > 0") -> where("product_id", $product_id)->delete();

    }

    public function removeProductVariantVal($product_variant_arr, $product_id){
        $varient_val_ids_s = "";
        foreach($product_variant_arr as $item){
            $varient_val_ids_s .= ("" == $varient_val_ids_s ? "":",").$item['varient_val_ids'];
        }

        ProductVariant::whereRaw("!FIND_IN_SET(varient_val_ids, '".$varient_val_ids_s."')")->where("product_id", $product_id)->delete();

    }







    public function ajaxSaveCategory(Request $request){
        $id = $request->get("id");
        $info = ProductCategory::find($id);
        if(isset($info['id'])){
            $this->getBoClass($info, $request);
        }else{
            $info = new ProductCategory();
            $ret = $this -> getBoClass($info,$request, 'au_product_category');
            $info = $ret['model'];
            $info['path'] = $info->getParentPath($info['parent_id']).$info['title'];
            $info['parent_ids'] = $info->getParentIds($info['parent_id']);
        }
        if ($request->hasFile('imgFile')) {
            $fileData = $request->file('imgFile');
            $filename = $fileData->getClientOriginalName();
            $ext = $fileData->getClientOriginalExtension();
            $filepath = makeUploadFileName().'.'.$ext;
            $fileData->move(public_path('uploads/category'), $filepath);
            $info['img'] = '/uploads/category/'.$filepath;
            $info['img_filename'] = $filename;
        }

        $info->save();
        return json_encode(array('status'=>1));
    }

    public function ajaxDeleteProduct(Request $request){
        $id = $request->get("id");
        if(Product::isDelete($id)){
            product::deleteItem($id);
            return json_encode(array('status' => 1));
        }else{
            return json_encode(array('status' => 0, 'msg'=> 'Can not delete this item'));
        }
    }




}