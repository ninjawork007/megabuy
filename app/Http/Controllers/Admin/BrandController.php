<?php namespace App\Http\Controllers\Admin;

use App\Brand;
use Sentinel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $pageParam = $this->getPageParam();
        $search = $this->getParam("search", "");
        view()->share('search', $search);

        $is_upload_admin = $this->getParam("is_upload_admin", "");
        view()->share('is_upload_admin', $is_upload_admin);

        $model = new Brand();
        if($search != ''){
            $model = $model->whereRaw("title LIKE '%".$search."%'");
        }

        if($is_upload_admin != ''){
            $model = $model->where("is_upload_admin",$is_upload_admin);
        }


        $count = $model->count();
        $list = $model->skip($pageParam["start"])->take($pageParam["perPageSize"])->get();
        $pageParam = $this->setPageParam($pageParam, $count);
        view()->share('pageParam', $pageParam);
        view()->share("list", $list);
        return view('admin.brand.index');
    }

    public function  getInfo($id){
        view()->share("id", $id);
        $info = Brand::find($id);
        if(!isset($info['id'])) {$info = new Brand();}

        view()->share("info", $info);
        return view('admin.brand.info');
    }

    public function ajaxSaveBrand(Request $request){
        $id = $request->get("id");
        $info = Brand::find($id);
        if(isset($info['id'])){
            $this->getBoClass($info, $request);
        }else{
            $info = new Brand();
            $ret = $this -> getBoClass($info,$request, 'au_brand');
            $info = $ret['model'];
        }
        $img = $request->get("log_img_val","");
        if($img != '' && 0 !== strpos($img, 'http')){
            $img = $this->genImage($img);
        }

        if(!isset($img)){
            $img ='';
        }
        $info['log_img'] = $img;
        $info['is_upload_admin'] = 1;
        $info->save();
        return json_encode(array('status'=>1));
    }

    public function ajaxDeleteBrand(Request $request){
        $id = $request->get("id");
        if(Brand::isDelete($id)){
            Brand::where(array("id"=>$id))->delete();
            return json_encode(array('status' => 1));
        }else{
            return json_encode(array('status' => 0, 'msg'=> 'Can not delete this item'));
        }
    }




}