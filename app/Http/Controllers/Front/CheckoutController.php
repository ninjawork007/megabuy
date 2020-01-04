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
use App\User;
use App\ProductCategory;
use App\UserBasket;
use App\UserReceiveAddress;
use App\UserTransLog;
use App\Order;
use DB;

use PayMoney\Api\Payer;
use PayMoney\Api\Amount;
use PayMoney\Api\Transaction;
use PayMoney\Api\RedirectUrls;
use PayMoney\Api\Payment;

class CheckoutController extends JoshController{

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $immeditaly_type = $this->getParam("immeditaly_type", "0");
        $basketList = UserBasket::getBasketList($immeditaly_type);
        view()->share('basketList', $basketList);
        $shipInfo = UserReceiveAddress::getShippingAddress();
        view()->share("shipInfo", $shipInfo);
        view()->share('countries', $this->countries);
        view()->share('immeditaly_type',$immeditaly_type);
        return view("front/checkout");
    }

    public function createOrder(){
        $immeditaly_type = $this->getParam("immeditaly_type", "0");
        $is_pay = $this->getParam("is_pay","0");
        $user_id = $this->getUserId();
        $userReceiveAddressCount = UserReceiveAddress::where(array("user_id"=>$user_id, "is_active"=>'1'))->count();
        if($userReceiveAddressCount == 0){
            echo json_encode(array("status"=>"0", "msg"=>'Receive Address is Empty!'));
            return;
        }
       
        $user = Sentinel::getUser();
        $roleName = $user->getUserRoleName();
        if($roleName == 'seller'){
            $success = url('front/my/seller/activity_buying');
        }
        else
            $success = url('front/my/activity_purchase_history');
        $paymentMethods = array();
        $totalPrices = array();
        $currencies = array();
        $merchants = array();
        $payer = new Payer();
        if($is_pay == 1){
            if($immeditaly_type == 0){
                $data = UserBasket::getSellerPayments();
                print_r($data);
                $index = 0;
                
                foreach($data as $row){
                    $paymentMethods[$index] = 'PayMoney';
                    $totalPrices[$index] = $row['amount'];
                    $currencies[$index] = 'USD';
                    $merchants[$row['merchant_id']] = $row['merchant_security'];
                    $index++;
                }
            }
            else{
                $data = UserBasket::where(array('user_id'=>$user_id,'immeditaly_type'=>1))->first();
                $paymentMethods[0] = 'PayMoney';
                $perPrice = Product::find($data['product_id']);
                $totalPrices[0] = $perPrice['price'] * $data['product_count'];
                $currencies[0] = 'USD';
                $seller = User::find($data['supplier_id']);
                $merchants[$seller['merchant_id']] = $seller['merchant_security'];
                
            }
            $payer->setPaymentMethod($paymentMethods); //preferably, your system name, example - PayMoney

            //Amount Object
            $amountIns = new Amount();

            $amountIns->setTotal($totalPrices)->setCurrency($currencies); //must give a valid currency code and must exist in merchant wallet list

            //Transaction Object
            $trans = new Transaction();
            $trans->setAmount($amountIns);

            //RedirectUrls Object
            $urls = new RedirectUrls();
            
                
            $urls->setSuccessUrl($success) //success url - the merchant domain page,
            ->setCancelUrl(url('front/checkout/index?immeditaly_type='.$immeditaly_type)); //cancel url - the merchant domain page, to redirect after

            //Payment Object
            $payment = new Payment();
            
            $payment->setCredentials($merchants)->setRedirectUrls($urls)
                ->setPayer($payer)
                ->setTransaction($trans);
            try {
                
                $payment->create(); //create payment
                $orderIds = UserBasket::createOrder($immeditaly_type);
                //=============  2019-12-01 demo ==============================
                $urls = $payment->getApprovedUrl();

                header("Location: ".$payment->getApprovedUrl()); //checkout url
                
                exit;
            } catch (\Exception $ex) {
                print $ex;
                exit;
            }
        }
        else{
            $orderIds = UserBasket::createOrder($immeditaly_type);
            return redirect($success);
        }
        

        // echo json_encode(array("status"=>"1", "orderIds" => $orderIds));
    }

    public function payForUnpaid(){
        $orderIds = $this->getParam('orderIds','');
        $trans_money = $this->getParam('trans_money',0);
        $trans_id = $this->getParam('trans_id',0);
        $user = Sentinel::getUser();
        $roleName = $user->getUserRoleName();
        if($roleName == 'seller'){
            $success = url('front/my/seller/activity_buying');
        }
        else{
            $success = url('front/my/activity_purchase_history');
        }
        $ids = explode(",", $orderIds);
        $paymentMethods = array();
        $totalPrices = array();
        $currencies = array();
        $merchants = array();
        $payer = new Payer();
        $index = 0;
        foreach($ids as $id){
            $order = Order::find($id);
            $sellerId = $order['seller_id'];
            $seller = User::find($sellerId);
            $paymentMethods[$index] = 'PayMoney';
            $totalPrices[$index] = $order['total_price'];
            $currencies[$index] = 'USD';
            $merchants[$seller['merchant_id']] = $seller['merchant_security'];
            $index++;
        }
        // print_r($merchants);
        // $payer->setPaymentMethod($paymentMethods); //preferably, your system name, example - PayMoney

        // //Amount Object
        // $amountIns = new Amount();

        // $amountIns->setTotal($totalPrices)->setCurrency($currencies); //must give a valid currency code and must exist in merchant wallet list

        // //Transaction Object
        // $trans = new Transaction();
        // $trans->setAmount($amountIns);

        // //RedirectUrls Object
        // $urls = new RedirectUrls();
        
            
        // $urls->setSuccessUrl($success) //success url - the merchant domain page,
        // ->setCancelUrl($success); //cancel url - the merchant domain page, to redirect after

        // //Payment Object
        // $payment = new Payment();
        
        // $payment->setCredentials($merchants)->setRedirectUrls($urls)
        //     ->setPayer($payer)
        //     ->setTransaction($trans);
        // try {
            
        //     $payment->create(); //create payment
        //     // $orderIds = UserBasket::createOrder($immeditaly_type);
            UserTransLog::paySuccess($trans_id);
            return redirect($success);
        //     //=============  2019-12-01 demo ==============================
        //     $urls = $payment->getApprovedUrl();

        //     header("Location: ".$payment->getApprovedUrl()); //checkout url
            
        //     exit;
        // } catch (\Exception $ex) {
        //     print $ex;
        //     exit;
        // }

    }

    public function createTransLog(){
        $order_ids = $this->getParam("order_ids", "");
        $trans_type = $this->getParam("trans_type", "");
        $ret = UserTransLog::createTransLog($order_ids, $trans_type);
        echo json_encode(array("status"=>"1", "transId" => $ret['id'], "transMoney" =>$ret['money'] ));
    }

}