<?php namespace App\Http\Controllers\Front;

use\App\Http\Controllers\JoshController;
use App\ProductHistory;
use App\Message;
use Illuminate\Http\Request;
use Mail;
use Reminder;
use Sentinel;
use URL;
use Validator;
use View;
use App\UserProductHistory;
use App\UserSaveSeller;
use App\SearchHistory;
use App\Brand;
use DB;


class MyController extends JoshController
{

	public function __construct(){
		parent::__construct();
	}

	public function getBrowserHistoryList(){
        $browserHistoryList = UserProductHistory::where(array("user_id"=>$this->getUserId()))->orderBy("id", "DESC")->take(5)->get();
        view()->share("browserHistoryList", $browserHistoryList);
    }

    public function index(Request $request){
        $user = Sentinel::getUser();
        $roleName = $user->getUserRoleName();
        if($roleName == ""){
           return  redirect("login");
        }
        switch($roleName){
            case  "admin":
                return redirect("admin");
                break;
            case "buyer":
                view()->share("top_menu", "account");
                $countries = $this->countries;
                view()->share("user", $user);
                view()->share("countries", $countries);
                return view("front/front_buyer/account");
                break;
            case "seller":
                view()->share("top_menu", "account");
                $countries = $this->countries;
                view()->share("user", $user);
                view()->share("countries", $countries);
                return view("front/front_seller/account");
                break;
        }
    }

    public function messages(Request $request){
        $search_key = $request->get("search_key", "");
        $model = new Message();
        $myId = $this->getUserId();
        $user = Sentinel::getUser();
        $roleName = $user->getUserRoleName();
        if($roleName == 'buyer') {
        } else if($roleName == 'seller') {
        }else {
            return json_encode(array("status"=>"0", "msg"=>"Please login"));
        }
        // get new message count
        $where = "receiver_id = $myId and read_time = '0000-00-00 00:00:00'";
        $count = $model->whereRaw($where)->count();

        $where = "(sender_id = $myId or receiver_id = $myId) and users.id <> $myId";
        if($search_key != "") {
            $where = "(sender_id = $myId or receiver_id = $myId) and (first_name like '%".$search_key."%' OR last_name like '%".$search_key."%')";
        }

        switch($roleName){
            case  "admin":
                return redirect("admin");
                break;
            case "buyer":
                view()->share("top_menu", "messages");
                view()->share("user", $user);
                view()->share("new_message", $count);
                $result = $model->join("users", "memo.receiver_id", "=", "users.id")
                    ->whereRaw($where)
                    ->orderby("last_send_time", "desc")
                    ->groupby("receiver_id")
                    ->select("*",DB::raw("getMemoCount(sender_id, receiver_id) as memo_count"), DB::raw("getMemoMaxTime(sender_id, receiver_id) as last_send_time"))
                    ->get();
                return view("front/front_buyer/messages")
                    ->with("userList", $result)
                    ->with("search_key", $search_key)
                    ->with("myId", $myId);
                break;
            case "seller":
                view()->share("top_menu", "messages");
                view()->share("user", $user);
                view()->share("new_message", $count);
                $result = $model->join("users", "memo.sender_id", "=", "users.id")
                    ->whereRaw($where)
                    ->orderby("last_send_time", "desc")
                    ->groupby("sender_id")
                    ->select("*",DB::raw("getMemoCount(sender_id, receiver_id) as memo_count"), DB::raw("getMemoMaxTime(sender_id, receiver_id) as last_send_time"))
                    ->get();
                return view("front/front_seller/messages")
                    ->with("userList", $result)
                    ->with("search_key", $search_key)
                    ->with("myId", $myId);
                break;
        }
    }

    public function ajaxGetMessage(Request $request) {
        $user_id = $request->get("id", "");
        $myId = $this->getUserId();
        if($user_id == "" || $myId == ""){
            return json_encode(array("status"=>"0", "msg"=>"fail"));
        }
        $model = new Message();
        $where = "(sender_id = $myId and receiver_id = $user_id) or (sender_id = $user_id and receiver_id = $myId)";
        $model->where("sender_id", $user_id)
              ->where("receiver_id", $myId)
              ->update(array("read_time" => date("Y-m-d H:i:s")));

        $result = $model->whereRaw($where)
              ->orderby("send_time")
              ->get();
        return json_encode(array("status"=>"1", "msgList"=>$result, "myId"=>$myId));
    }

    public function ajaxSendMessage(Request $request) {
        $model = new Message();
        $model["receiver_id"]= $request->get("receiver_id", "");
        $model["content"] = $request->get("content", "");
        $model["sender_id"] = $this->getUserId();
        if($model["receiver_id"] == ""){
            return json_encode(array("status"=>"0"));
        }else{
            $model["send_time"] = date("Y-m-d H:i:s");
            $model->save();
            return json_encode(array("status"=>"1"));
        }
    }

    public function ajaxGetMemoStatus(Request $request) {
        $myId = $this->getUserId();
        $user = Sentinel::getUser();
        $roleName = $user->getUserRoleName();
        $model = new Message();
        if($roleName == 'buyer') {
            $model->where("buyer_id", $myId);
        } else if($roleName == 'seller') {
            $model->where("seller_id", $myId);
        } else {
            return json_encode(array("status"=>"0", "msg"=>"Please login"));
        }

        $model->where("read_time", "0000-00-00 00:00:00");
        $newList = $model->get();
        $userList = array();
        $i = 0;
        foreach($newList as $new) {
            if($roleName == 'buyer')
                $userList[$i] = $new['seller_id'];
            else if($roleName == 'seller')
                $userList[$i] = $new['buyer_id'];
            $i++;
        }
        if($model->count() > 0) {
            return json_encode(array("status"=>"1", "newList"=>$userList));
        }else {
            return json_encode(array("status"=>"0"));
        }
    }


    public function activity_index(Request $request){
        $user = Sentinel::getUser();
        $roleName = $user->getUserRoleName();
        print_r($roleName);
        if($roleName == ""){
            return  redirect("login");
        }

        switch($roleName){
            case  "admin":
                return redirect("admin");
                break;
            case "buyer":
                return redirect("front/my/activity_recent_view");
                break;
            case "seller":
                return redirect("front/my/seller/activity");
                break;
        }
    }

    public function activity_summary(Request $request){
        $user = Sentinel::getUser();
        view()->share("top_menu", "activity");
        view()->share("left_menu", "summary");
        view()->share("user", $user);
        return view("front/front_buyer/activity_summary");
    }

    public function activity_recent_view(Request $request){
        $user = Sentinel::getUser();
        view()->share("top_menu", "activity");
        view()->share("left_menu", "recent_view");
        view()->share("user", $user);
        $where = "user_id = ".$this->getUserId();
        $pageParam = $this->getPageParam(12);
        $model = new UserProductHistory();
        $model->orderBy("id", "DESC");
        $model = $model->whereRaw($where);
        $count = $model->count();
        $list = $model->skip($pageParam["start"])->take($pageParam["perPageSize"])->get();
        $pageParam = $this->setPageParam($pageParam, $count);
        view()->share('pageParam', $pageParam);
        view()->share("list", $list);
        view()->share("count", $count);
        $this->getBrowserHistoryList();
        return view("front/front_buyer/activity_recent_view");
    }

    public function activity_purchase_history(Request $request){
        $user = Sentinel::getUser();
        view()->share("top_menu", "activity");
        view()->share("left_menu", "history");
        view()->share("user", $user);
        $this->getBrowserHistoryList();
        return view("front/front_buyer/activity_purchase_history");
    }
    public function activity_watching(Request $request){
        $user = Sentinel::getUser();
        view()->share("top_menu", "activity");
        view()->share("left_menu", "watching");
        view()->share("user", $user);
        return view("front/front_buyer/activity_watching");
    }

    public function activity_saved_searches(Request $request){
        $user = Sentinel::getUser();
        view()->share("top_menu", "activity");
        view()->share("left_menu", "searches");
        view()->share("user", $user);
        $where = "user_id = ".$this->getUserId();
        $pageParam = $this->getPageParam();
        $model = new SearchHistory();
        $model->orderBy("id", "DESC");
        $model = $model->whereRaw($where);
        $count = $model->count();
        $list = $model->skip($pageParam["start"])->take($pageParam["perPageSize"])->get();
        $pageParam = $this->setPageParam($pageParam, $count);
        view()->share('pageParam', $pageParam);
        view()->share("list", $list);
        view()->share("count", $count);
        $this->getBrowserHistoryList();
        return view("front/front_buyer/activity_saved_searches");
    }

    public function activity_saved_sellers(Request $request){
        $user = Sentinel::getUser();
        view()->share("top_menu", "activity");
        view()->share("left_menu", "sellers");
        view()->share("user", $user);
        $where = "user_id = ".$this->getUserId();
        $pageParam = $this->getPageParam();
        $model = new UserSaveSeller();
        $model->orderBy("id", "DESC");
        $model = $model->whereRaw($where);
        $count = $model->count();
        $list = $model->skip($pageParam["start"])->take($pageParam["perPageSize"])->get();
        $pageParam = $this->setPageParam($pageParam, $count);
        view()->share('pageParam', $pageParam);
        view()->share("list", $list);
        view()->share("count", $count);
        $this->getBrowserHistoryList();
        return view("front/front_buyer/activity_saved_sellers");
    }

    public function activity_sell(Request $request){
        $user = Sentinel::getUser();
        view()->share("top_menu", "activity");
        view()->share("left_menu", "sell");
        view()->share("user", $user);
        return view("front/front_buyer/activity_sell");
    }

    public function ajaxClearProductHistory(){
        $user_id = $this->getUserId();
        UserProductHistory::where(array("user_id"=>$user_id))->delete();
        echo json_encode(array('status'=>"1", 'msg' => "The operation successful"));
        return;
    }

    public function ajaxSaveSeller(Request $request){
        $user_id = $this->getUserId();
        $userSaveSeller = new UserSaveSeller();
        $info = $this->getBoClass($userSaveSeller, $request, "au_user_save_seller");
        $userSaveSeller = $info['model'];
        $userSaveSeller['user_id'] = $user_id;
        $userSaveSeller['log_date'] = date("Y-m-d H:i:s");
        $userSaveSeller->save();
        echo json_encode(array('status'=>"1", 'msg' => "The operation successful"));

    }

    public function ajaxDeleteSaveHistory($id=0){
        $user_id = $this->getUserId();
        SearchHistory::where(array("id"=>$id))->delete();
        echo json_encode(array('status'=>"1", 'msg' => "The operation successful"));
        return;
    }

}