<?php
namespace App;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentTaggable\Taggable;
use DB;



class User extends EloquentUser {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */

	protected $table = 'users';

	/**
	 * The attributes to be fillable from the model.
	 *
	 * A dirty hack to allow fields to be fillable by calling empty fillable array
	 *
	 * @var array
	 */
    use Taggable;

	protected $fillable = [];
	protected $guarded = ['id'];
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
	* To allow soft deletes
	*/
	use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $appends = ['full_name'];
    public function getFullNameAttribute()
    {
        return str_limit($this->first_name . ' ' . $this->last_name, 30);
    }

    public function getIsTopSellerAttribute(){
        $mark = $this->getAvgMark();
        if($mark >=3 ) return true;
        return false;
    }

    public function getUserRoleName(){
        $roleUser = RoleUsers::find($this->id);
        if(!isset($roleUser['role_id'])) return "";
        $roles = Roles::find($roleUser['role_id']);
        if(!isset($roles['id'])) return "";
        return $roles['slug'];
    }

    public function getProductCount(){
        return Product::where(array("seller_id"=>$this->id, "state"=>"1"))->count();
    }



    public function getAvgMark(){
        return Order::where("seller_id", $this->id)->whereRaw("eval_mark > 0")->avg('eval_mark');
    }

    public function getSellerProductAvgMark($product_id){
        $whereRaw = " eval_mark > 0 AND isProductInOrder($product_id,id) = 1";
        return Order::where("seller_id", $this->id)->whereRaw($whereRaw)->avg('eval_mark');
    }


    public function getCartCount(){
        return UserBasket::where(array("user_id"=>$this->id,'immeditaly_type'=>0))->count();
    }

    public function getHomeStatisticsSellerInfo($now, $lastWeek, $lastMonth){
        $ret = array();
        $where = "a.role_id = 3 AND DATE_FORMAT(b.created_at, '%Y-%m-%d') = '".$now."'";
        $sql = "SELECT COUNT(*) cnt FROM role_users a INNER JOIN users b ON  a.user_id = b.id  WHERE ".$where;
        $info = DB::select($sql);
        $ret['nowCount'] = $info[0]->cnt;
        $where = "a.role_id = 3 AND DATE_FORMAT(b.created_at, '%Y-%m-%d') >= '".$lastWeek[2]."' AND DATE_FORMAT(b.created_at, '%Y-%m-%d') <= '".$now."'";
        $sql = "SELECT COUNT(*) cnt FROM role_users a INNER JOIN users b ON  a.user_id = b.id  WHERE ".$where;
        $info = DB::select($sql);
        $ret['weekCount'] = $info[0]->cnt;
        $where = "a.role_id = 3 AND DATE_FORMAT(b.created_at, '%Y-%m-%d') >= '".$lastMonth[2]."' AND DATE_FORMAT(b.created_at, '%Y-%m-%d') <= '".$now."'";
        $sql = "SELECT COUNT(*) cnt FROM role_users a INNER JOIN users b ON  a.user_id = b.id  WHERE ".$where;
        $info = DB::select($sql);
        $ret['monthCount'] = $info[0]->cnt;
        return $ret;
    }

    public function getHomeStatisticsUserInfo($now, $lastWeek, $lastMonth){
        $ret = array();
        $where = "a.role_id = 2 AND DATE_FORMAT(b.created_at, '%Y-%m-%d') = '".$now."'";
        $sql = "SELECT COUNT(*) cnt FROM role_users a INNER JOIN users b ON  a.user_id = b.id  WHERE ".$where;
        $info = DB::select($sql);
        $ret['nowCount'] = $info[0]->cnt;
        $where = "a.role_id = 2 AND DATE_FORMAT(b.created_at, '%Y-%m-%d') >= '".$lastWeek[2]."' AND DATE_FORMAT(b.created_at, '%Y-%m-%d') <= '".$now."'";
        $sql = "SELECT COUNT(*) cnt FROM role_users a INNER JOIN users b ON  a.user_id = b.id  WHERE ".$where;
        $info = DB::select($sql);
        $ret['weekCount'] = $info[0]->cnt;
        $where = "a.role_id = 2 AND DATE_FORMAT(b.created_at, '%Y-%m-%d') >= '".$lastMonth[2]."' AND DATE_FORMAT(b.created_at, '%Y-%m-%d') <= '".$now."'";
        $sql = "SELECT COUNT(*) cnt FROM role_users a INNER JOIN users b ON  a.user_id = b.id  WHERE ".$where;
        $info = DB::select($sql);
        $ret['monthCount'] = $info[0]->cnt;
        return $ret;
    }

    public function role(){
        return  $this->hasOne('App\RoleUsers', 'user_id', 'id');
    }

    public function getPurchasedNum(){
        $sql = "SELECT IFNULL(SUM(quantity), 0) sold FROM au_order WHERE user_id = $this->id";
        $info = DB::select($sql);
        return $info[0]->sold;
    }

    public function getSoldNum(){
        $sql = "SELECT IFNULL(SUM(quantity), 0) sold FROM au_order WHERE seller_id = $this->id";
        $info = DB::select($sql);
        return $info[0]->sold;
    }

    public function isTopSeller(){
        $ret = false;
        $configModel = new Configs();
        $seller_count = $configModel->getConfVal("top_count");
        if($seller_count=="") {
            $seller_count = "1";
        }
        $sql = "SELECT seller_id FROM au_order  GROUP BY seller_id ORDER BY getSellerTotalPrice(seller_id) DESC   LIMIT ".$seller_count;
        $topList = DB::select($sql);
        foreach($topList as $key=>$item){
            if($item->seller_id == $this['id']){
                $ret = true;
            }
        }

        return $ret;
    }

}
