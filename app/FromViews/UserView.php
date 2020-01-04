<?php

namespace App\FromViews;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\User;
use App\RoleUsers;

class UserView implements FromView
{
    public $role;
    public function __construct($role = ''){
        $this->role = $role;
    }
    public function view(): View
    {
        $users = array();
        if($this->role == '')
            $users = User::all();
        else{
            $roleusers = new RoleUsers();
            $sellers = $roleusers->getSellerList();
            foreach ($sellers as $seller) {
                array_push($users,$seller['user']);
            }
        }

        return view('export.user',['users'=>$users]);

    }
}