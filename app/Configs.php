<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configs extends Model {

    protected $table = 'au_config';
    protected $guarded = ['id'];
    public  $timestamps = false;

    static public  function isDelete($id){
        return true;
    }

    public function getConfVal($conf_name){
        $ret = "";
        $info = Configs::where("conf_name", $conf_name)->first();
        if(isset($info['id'])){
            $ret  = $info['conf_val'];
        }
        return $ret;
    }
}
