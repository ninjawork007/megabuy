<?php
namespace PayMoney\Api;

use PayMoney\Common\PayMoneyModel;

/**
 * Class Payment
 * @property \PayMoney\Api\Payer payer
 * @property \PayMoney\Api\Transaction transaction
 * @property \PayMoney\Api\RedirectUrls redirectUrls
 * @property array credentials
 * @property string approvedUrl
 *
 */
class Payment extends PayMoneyModel
{

    /**
     * @param \PayMoney\Api\Payer $payer
     *
     * @return $this
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;
        return $this;
    }

    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @param \PayMoney\Api\Transaction $transaction
     *
     * @return $this
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
        return $this;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param \PayMoney\Api\RedirectUrls $redirectUrls
     *
     * @return $this
     */
    public function setRedirectUrls($redirectUrls)
    {
        $this->redirectUrls = $redirectUrls;
        return $this;
    }

    public function getRedirectUrls()
    {
        return $this->redirectUrls;
    }

    /**
     * @param array $credentials
     *
     * @return $this
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
        return $this;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function setApprovedUrl($urls)
    {
        $this->approvedUrl = $urls;
        return $this;
    }

    public function getApprovedUrl()
    {
        $actuallUrls = 'https://megapay.hk/merchant/payment?grant_ids=';
        $grant_ids = '[';
        $tokens = '[';
        $ii = 0;

        foreach( $this->approvedUrl as $url) :
            
            $ii += 1;
            $ttt = explode("?grant_id=", $url);
            $gt = explode("&token=", $ttt[1]);

            $grant_ids .= $gt[0];
            if($ii != count($this->approvedUrl)) {
                $grant_ids .= ',';
            }
            if($ii == count($this->approvedUrl)) {
                $grant_ids .= ']';
            }

            $tokens .= $gt[1];
            if($ii != count($this->approvedUrl)) {
                $tokens .= ',';
            }
            if($ii == count($this->approvedUrl)) {
                $tokens .= ']';
            }

        endforeach;
        $actuallUrls .= $grant_ids . '&tokens=' . $tokens;
        return $actuallUrls;
    }

    public function create()
    {
        $accessTokens = $this->getAccessToken();
        $approveUrls = array();
        foreach($accessTokens as $i => $accessToken) :
            $approveUrl  = $this->sendTransactionInfo($accessToken, $i);
            array_push($approveUrls, $approveUrl);
        endforeach;
        $this->setApprovedUrl($approveUrls);
    }

    private function getAccessToken()
    {
        $array = $this->getCredentials();
        // if (!$array['client_id'] || !$array['client_secret'])
        // {
        //     echo 'Parameter array must contain with client_id, client_secret.';
        //     exit;
        // }
        // $client_id                = $array['client_id'];
        // $client_secret            = $array['client_secret'];
        // $payload['client_id']     = $client_id;
        // $payload['client_secret'] = $client_secret;
        // $res = $this->execute(BASE_URL . 'merchant/api/verify', 'post', $payload);
        // $res = json_decode($res);
        
        // if (!$res)
        // {
        //     echo "Please check you client iD or client secret again";
        //     exit;
        // }

        // if ($res->status == 'error')
        // {
        //     echo $res->message;exit;
        // }
        
        // $response = $res->data->access_token;
        // return $response;
        //=========== 2019-11-30 demo =================
        // $payload = array();
        foreach($array as $key=>$arr) {
            if (!$key || !$arr)
            {
                echo 'Parameter array must contain with client_id, client_secret.';
                exit;
            }
            $payload[$key] = $arr;

        }
        $payload = json_encode(array('merchants' => $payload));
        $res = $this->execute(BASE_URL . 'merchant/api/verifys', 'post', $payload);
       
        $res = json_decode($res);
        $tokens = array();
        foreach($res as $r) : 
            if (!$r)
            {
                echo "Please check you client iD or client secret again";
                exit;
            }

            if ($r->status == 'error')
            {
                echo $r->message;exit;
            }
            array_push($tokens, $r->data->access_token);
        endforeach;
        return $tokens;
    }

    private function sendTransactionInfo($token, $i)
    {
        $trans        = $this->getTransaction();
        $payer        = $this->getPayer();
        $redirectUrls = $this->getRedirectUrls();

        $amount        = $trans->amount->getTotal();
        $currency      = $trans->amount->getCurrency();
        $successUrl     = $redirectUrls->getSuccessUrl();
        $cancelUrl     = $redirectUrls->getCancelUrl();
        $paymentMethod = $payer->getPaymentMethod();
        
        $req['payer']     = $paymentMethod[$i];
        $req['amount']    = $amount[$i];
        $req['currency']  = $currency[$i];
        $req['successUrl'] = $successUrl;
        $req['cancelUrl'] = $cancelUrl;
        $header = ['Authorization: Bearer ' . $token];
        
        $res = $this->execute(BASE_URL . 'merchant/api/transaction-info', 'post', $req, $header);
        $res = json_decode($res);
        if (!$res)
        {
            echo "Please check your transaction details again !";
            exit;
        }

        if ($res->status == 'error')
        {
            echo $res->message;exit;
        }

        $response = $res->data->approvedUrl;
        return $response;
    }

}