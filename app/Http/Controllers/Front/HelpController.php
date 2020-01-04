<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\JoshController;

use Sentinel;
use Mail;
use Storage;
use App\Mail\ContactAdmin;

class HelpController extends JoshController
{
    public function sendEmail(Request $request){
        $user = Sentinel::getUser();
        $data['user'] = $user;
        $data['title'] = $this->getParam('title','');
        $data['msg'] = $this->getParam("emailText","");
        // $data['path'] = Storage::putFileAs('avatars', $request->file('attachment'),time());
        $email = "lonelylovelyman@hotmail.com";
        Mail::to($email)
            ->send(new ContactAdmin($data));
        // echo json_encode(array('status'=>1));
        return redirect('front/sell');
    }
}
