<?php

namespace App\FromViews;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Product;
use App\ProductVariant;
use App\ProductCategory;
use App\Brand;
use App\User;

class ProductView implements FromView
{
    public function view(): View
    {
        $products = Product::all();
        $allProducts = array();
        foreach($products as $product){
            $seller = User::find($product['seller_id']);
            $variants = ProductVariant::find($product['id']);
            $category = ProductCategory::find($product['category_id']);
            $brand = Brand::find($product['brand_id']);
            if(count($variants)){
                
                foreach($variants as $variant){
                    $variant['category'] = $category['path'];
                    $variant['brand'] = $brand['title'];
                    $variant['retail_price'] = $product['retail_price'];
                    $variant['img'] = url('/').'/'.$product['img'];
                    $variant['condition'] = $product['condition'] == 0 ? 'New' : 'Used';
                    $variant['seller_id'] = $product['seller_id'];
                    $variant['seller_name'] = $seller['first_name'].' '.$seller['last_name'];
                    array_push($allProducts,$variant);
                }
            }
            else{
                $product['category'] = $category['path'];
                $product['brand'] = $brand['title'];
                $product['img'] = $product['img'] == '' ? '' : url('/').'/'.$product['img'];
                $product['condition'] = $product['condition'] == 0 ? 'New' : 'Used';
                $product['seller_name'] = $seller['first_name'].' '.$seller['last_name'];
                array_push($allProducts,$product);
            }
        }
        return view('export.product',['products'=>$allProducts]);
    }
}