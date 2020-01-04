<?php namespace App\Http\Controllers\Admin;

use App\Brand;
use App\ProductCategory;
use App\Configs;
use Sentinel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function index()
    {
        $rootCategoryList =ProductCategory::where("parent_id", "0")->get();
        view()->share("rootCategoryList", $rootCategoryList);

        $config_list = Configs::where(array())->orderBy("id")->get();
        view()->share("config_list", $config_list);
        return view('admin.general.index');
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