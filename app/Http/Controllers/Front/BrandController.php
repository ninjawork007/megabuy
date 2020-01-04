<?php namespace App\Http\Controllers\Front;

use\App\Http\Controllers\JoshController;
use App\Http\Requests\ConfirmPasswordRequest;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Mail;
use Reminder;
use Sentinel;
use URL;
use Validator;
use View;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ForgotRequest;
use stdClass;
use App\Mail\ForgotPassword;

use App\Product;
use App\ProductCategory;
use App\Brand;

class BrandController extends JoshController{
    private $categoryModel;
    private $productModel;
    private $brandModel;
    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new ProductCategory();
        $this->productModel = new Product();
        $this->brandModel = new Brand();
    }

    public function showBrand(Request $request, $id)
    {
        $productArr = array();
        if($id == 0 || $id == ""){
            return view("404");
        }else{
            $brandInfo = $this->brandModel->find($id);
            if(!isset($brandInfo['title'])){
                return view("404");
            }else{
                $products = $this->productModel
                                 ->where('brand_id',$id)
                                //  ->groupBy('category_id')
                                 ->get();
                $categories = $this->productModel
                                   ->getCategoryIds($id);
                $i = 0;
                foreach ($categories as $key => $category_id) {
                    $category = $this->categoryModel
                                     ->where('id',$category_id['category_id'])
                                     ->first();
                    $j = 0;
                    foreach ($products as $product) {
                        if($product['category_id'] == $category_id['category_id'])
                        {
                            $productArr[$i]['category'] = $category;
                            $productArr[$i]['productList'][$j] = $product;
                            $j++;
                        }
                    }
                    $i++;
                }
            }
        }
        return view("front/brand")
            ->with("brandInfo", $brandInfo)
            ->with("productArr", $productArr);
    }
}