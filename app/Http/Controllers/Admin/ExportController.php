<?php

namespace App\Http\Controllers\Admin;

use Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\FromViews\ProductView;
use App\FromViews\UserView;

class ExportController extends Controller
{
    public function productExport(){
        return Excel::download(new ProductView, 'product.xlsx');
    }

    public function userExport(){
        return Excel::download(new UserView(3),'user.xlsx');
    }
}
