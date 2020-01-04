<?php namespace App\Http\Controllers\Admin;

use App\Product;
use App\ProductCategory;
use App\Configs;
use Sentinel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $pageParam = $this->getPageParam();
        $search = $this->getParam("search", "");
        view()->share("search", $search);
        $model = new Product();
        if($search != ''){
            $model = $model->whereRaw("title LIKE '%".$search."%'");
        }
        $count = $model->count();
        $productList = $model->skip($pageParam["start"])->take($pageParam["perPageSize"])->get();
        $pageParam = $this->setPageParam($pageParam, $count);
        view()->share('pageParam', $pageParam);
        view()->share("productList", $productList);
        return view("admin.gallery.index");
    }

    public function downloadImgZip(){
        $id = $this->getParam("id", "0");
        $product = Product::find($id);
        if(isset($product['id'])){
            $zip_name = $product['title']."_img_".date("YmdHis").'.zip';
            $zip = new \ZipArchive();
            $zip->open($zip_name, \ZipArchive::CREATE);
            $files =  $product->getProductImgFileList();
            if(count($files) > 0){
                foreach ($files as $file) {
                    $zip->addFile($file);
                }
                $zip->close();

                $headers = array('Content-Type'        => 'application/zip',
                    'Content-Disposition' => 'attachment; filename="' .$zip_name. '"');
                return response()->download(public_path($zip_name), $zip_name, $headers);
            }

        }
    }

    public function getLeafCategoryList(){
        $root_id = $this->getParam("root_id", "0");
        $productCategoryModel = new ProductCategory();
        $childList = $productCategoryModel->getLeafCategoryMainShowList($root_id);
        view()->share("childList", $childList);
        return view('admin.general.list');
    }

    public function getInfo($id, $root_id){
        view()->share("id", $id);
        $info = ProductCategory::find($id);
        if(!isset($info['id'])) {$info = new ProductCategory();}
        if($id*1 == 0){
            view()->share("noneChildList", $info->getLeafCategoryMainNoneList($root_id));
        }
        view()->share("info", $info);
        return view('admin.general.info');
    }

    public function ajaxGetCategoryInfo($id){
        view()->share("id", $id);
        $info = ProductCategory::find($id);
        view()->share("info", $info);
        return view("admin.general.other_info");
    }

    public function ajaxSaveCategory(Request $request){
        $id = $request->get("id");
        $info = ProductCategory::find($id);

        if(isset($info['id'])){
            $this->getBoClass($info, $request);
        }else{
            return json_encode(array('status'=>0, 'msg'=> 'category info is incorrect!'));
        }
        if(!$info->isAppendMainCategory($id)){
            return json_encode(array('status'=>0, 'msg'=> 'The main category  is greater then 5 categories'));
        }
        $img = $request->get("log_img_val","");
        if($img != '' && 0 !== strpos($img, 'http')){
            $img = $this->genImage($img, 'category');
        }

        if(!isset($img)){
            $img ='';
        }
        $info['img'] = $img;
        $info['is_main'] = '1';
        $info->save();
        return json_encode(array('status'=>1));
    }


    public function ajaxDeleteCategory(Request $request){
        $id = $request->get("id");
        $info = ProductCategory::find($id);
        if(!isset($info['id'])){
            return json_encode(array('status' => 0, 'msg'=> 'The category info is incorrect'));

        }
        $info['is_main'] = 0;
        $info->save();
        return json_encode(array('status' => 1));
    }

    public function ajaxSaveConfigs(Request $request){
        $id = $this->getParam("id", "0");
        $info = Configs::find($id);
        $this->getBoClass($info, $request);
        $info->save();
        return json_encode(array('status'=>1, 'msg'=> 'The operation is success!'));

    }




}