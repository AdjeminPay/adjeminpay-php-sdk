<?php

namespace AdjeminPay;

use AdjeminPay\Exception\AdjeminPayArgumentException;
use AdjeminPay\Exception\AdjeminPayAuthException;
use GuzzleHttp\Client;
use AdjeminPay\Exception\AdjeminPayException;

/**
 * AdjeminPay Class
 *
 * @version 1.0.0
 */
class AdjeminPay implements AdjeminPayInterface {

    const API_BASE_URL = "https://api.adjeminpay.com";

    /**
     * @var string $clientId Client ID
     */
    private $clientId;

    /**
     * @var string $clientSecret Client Secret
     */
    private $clientSecret;

    /**
     * @var array $data All information about the application or transaction
     */
    public $data;

    /**
     * @var string $token Access token
     */
    private $token;

    /**
     * @var array $response Transaction reponse data
     */
    private $response;

    /**
     * Class constructor
     * Initialize some private value and check if they are available
     *
     * @param string $clientId
     * @param string $clientSecret
     * @throws AdjeminPayAuthException
     */
    public function __construct($clientId, $clientSecret){
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->token = $this->obtainAccessToken();
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return self::API_BASE_URL;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function obtainAccessToken()
    {
        $client = new Client();
        $url = $this->getBaseUrl()."/oauth/token";
        $body = [
            'client_id' => ''.$this->getClientId(),
            'client_secret'=> ''.$this->getClientSecret(),
            'grant_type' => 'client_credentials'
        ];

        $response = $client->post($url, [
            "headers" => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            "form_params" => $body

        ]);

        if ($response->getStatusCode() == 200){
            $body = $response->getBody()->getContents();
            $json = (array) json_decode($body, true);

            if(array_key_exists('access_token', $json) && !empty( $json['access_token'])){
                return $json['access_token'];
            }else{
                if(array_key_exists('message', $json) && !empty( $json['message'])){
                    $message  = $json['message'];
                }else{
                    $message  = "Client authentication failed";
                }
                throw  new AdjeminPayAuthException($message,$response->getStatusCode());
            }

        }else{
            $body = $response->getBody()->getContents();
            $json = (array) json_decode($body, true);
            if(array_key_exists('message', $json) && !empty( $json['message'])){
                $message  = $json['message'];
            }else{
                $message  = "Client authentication failed";
            }
            throw  new AdjeminPayAuthException($message,$response->getStatusCode());
        }
    }

    /**
     * Obtain Access Token
     * @throws AdjeminPayAuthException
     */
    public function getAccessToken()
    {
        return $this->token;
    }

    /**
     * @throws AdjeminPayException
     * @throws AdjeminPayAuthException
     */
    public function createCheckout($params): Transaction
    {
        //Validate parameters
        if(!array_key_exists('amount',$params) || empty($params['amount'])) throw new AdjeminPayArgumentException("Bad request,  amount is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);
        if(!array_key_exists('currency_code',$params) || empty($params['currency_code'])) throw new AdjeminPayArgumentException("Bad request,  currency_code is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);
        if(!array_key_exists('designation',$params) || empty($params['designation'])) throw new AdjeminPayArgumentException("Bad request,  designation is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);
        if(!array_key_exists('merchant_trans_id',$params) || empty($params['merchant_trans_id'])) throw new AdjeminPayArgumentException("Bad request,  merchant_trans_id is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);

        if(empty($this->getAccessToken())){
            $message = 'The requested service needs credentials, but the ones provided were invalid.';
            throw  new AdjeminPayAuthException($message,401);
        }

        $client = new Client();
        $url = $this->getBaseUrl()."/v3/merchants/create_checkout";
        $body = [
            'amount' => intval($params['amount']),
            'currency_code' => array_key_exists('currency_code', $params)?$params['currency_code']:null,
            'merchant_trans_id' => array_key_exists('merchant_trans_id', $params)?$params['merchant_trans_id']:null,
            'merchant_trans_data' => array_key_exists('merchant_trans_data', $params)?$params['merchant_trans_data']:null,
            'designation' => array_key_exists('designation', $params)?$params['designation']:null,
            'webhook_url' => array_key_exists('webhook_url', $params)?$params['webhook_url']:null,
            'return_url' => array_key_exists('return_url', $params)?$params['return_url']:null,
            'cancel_url' => array_key_exists('cancel_url', $params)?$params['cancel_url']:null,
            'customer_recipient_number' => array_key_exists('customer_recipient_number', $params)?$params['customer_recipient_number']:null,
            'customer_email' => array_key_exists('customer_email', $params)?$params['customer_email']:null,
            'customer_firstname' => array_key_exists('customer_firstname', $params)?$params['customer_firstname']:null,
            'customer_lastname' => array_key_exists('customer_lastname', $params)?$params['customer_lastname']:null,
        ];

        $response = $client->post($url, [
            "headers" => [
                'Authorization' => 'Bearer '.$this->getAccessToken(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            "json" => $body

        ]);

        $body = $response->getBody()->getContents();
        if($response->getStatusCode() == 200){
            $json = json_decode($body, true);

            if(array_key_exists('data', $json) && !empty( $json['data'])){
                $data = $json['data'];
                return new TransactionImpl($data);
            }else{
                $json =  json_decode($body, true);
                if(array_key_exists('message', $json) && !empty( $json['message'])){
                    $message  = $json['message'];
                }else{
                    $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
                }
                throw  new AdjeminPayException($message,$response->getStatusCode());
            }

        }else{

            $json =  json_decode($body, true);
            if(array_key_exists('message', $json) && !empty( $json['message'])){
                $message  = $json['message'];
            }else{
                $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
            }
            throw  new AdjeminPayException($message,$response->getStatusCode());
        }
    }

    public function completeCheckout($merchant_trans_id, $params): Transaction
    {

        //Validate parameters
        if(!array_key_exists('operator_code',$params) || empty($params['operator_code'])) throw new AdjeminPayArgumentException("Bad request,  operator_code is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);
        if(!array_key_exists('customer_recipient_number',$params) || empty($params['customer_recipient_number'])) throw new AdjeminPayArgumentException("Bad request,  customer_recipient_number is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);
        if( empty($merchant_trans_id)) throw new AdjeminPayArgumentException("Bad request,  merchant_trans_id is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);

        if(empty($this->getAccessToken())){
            $message = 'The requested service needs credentials, but the ones provided were invalid.';
            throw  new AdjeminPayAuthException($message,401);
        }

        $client = new Client();
        $url = $this->getBaseUrl()."/v3/merchants/complete_checkout/".$merchant_trans_id;
        $body = [
            'operator_code' => intval($params['operator_code']),
            'customer_recipient_number' => array_key_exists('customer_recipient_number', $params)?$params['customer_recipient_number']:null,
            'customer_email' => array_key_exists('customer_email', $params)?$params['customer_email']:null,
            'customer_firstname' => array_key_exists('customer_firstname', $params)?$params['customer_firstname']:null,
            'customer_lastname' => array_key_exists('customer_lastname', $params)?$params['customer_lastname']:null,
            'otp' => array_key_exists('otp', $params)?$params['otp']:null
        ];

        $response = $client->post($url, [
            "headers" => [
                'Authorization' => 'Bearer '.$this->getAccessToken(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            "json" => $body

        ]);

        $body = $response->getBody()->getContents();
        if($response->getStatusCode() == 200){
            $json = json_decode($body, true);

            if(array_key_exists('data', $json) && !empty( $json['data'])){
                $data = $json['data'];
                return new TransactionImpl($data);
            }else{
                $json =  json_decode($body, true);
                if(array_key_exists('message', $json) && !empty( $json['message'])){
                    $message  = $json['message'];
                }else{
                    $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
                }
                throw  new AdjeminPayException($message,$response->getStatusCode());
            }

        }else{

            $json =  json_decode($body, true);
            if(array_key_exists('message', $json) && !empty( $json['message'])){
                $message  = $json['message'];
            }else{
                $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
            }
            throw  new AdjeminPayException($message,$response->getStatusCode());
        }
    }

    public function createPayout($params): Transaction
    {

        //Validate parameters
        if(!array_key_exists('amount',$params) || empty($params['amount'])) throw new AdjeminPayArgumentException("Bad request,  amount is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);
        if(!array_key_exists('currency_code',$params) || empty($params['currency_code'])) throw new AdjeminPayArgumentException("Bad request,  currency_code is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);
        if(!array_key_exists('designation',$params) || empty($params['designation'])) throw new AdjeminPayArgumentException("Bad request,  designation is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);
        if(!array_key_exists('merchant_trans_id',$params) || empty($params['merchant_trans_id'])) throw new AdjeminPayArgumentException("Bad request,  merchant_trans_id is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);
        if(!array_key_exists('customer_recipient_number',$params) || empty($params['customer_recipient_number'])) throw new AdjeminPayArgumentException("Bad request, customer_recipient_number is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);

        if(empty($this->getAccessToken())){
            $message = 'The requested service needs credentials, but the ones provided were invalid.';
            throw  new AdjeminPayAuthException($message,401);
        }

        $client = new Client();
        $url = $this->getBaseUrl()."/v3/merchants/create_payout";
        $body = [
            'amount' => intval($params['amount']),
            'currency_code' => array_key_exists('currency_code', $params)?$params['currency_code']:null,
            'merchant_trans_id' => array_key_exists('merchant_trans_id', $params)?$params['merchant_trans_id']:null,
            'merchant_trans_data' => array_key_exists('merchant_trans_data', $params)?$params['merchant_trans_data']:null,
            'designation' => array_key_exists('designation', $params)?$params['designation']:null,
            'webhook_url' => array_key_exists('webhook_url', $params)?$params['webhook_url']:null,
            'return_url' => array_key_exists('return_url', $params)?$params['return_url']:null,
            'cancel_url' => array_key_exists('cancel_url', $params)?$params['cancel_url']:null,
            'customer_recipient_number' => array_key_exists('customer_recipient_number', $params)?$params['customer_recipient_number']:null,
            'customer_email' => array_key_exists('customer_email', $params)?$params['customer_email']:null,
            'customer_firstname' => array_key_exists('customer_firstname', $params)?$params['customer_firstname']:null,
            'customer_lastname' => array_key_exists('customer_lastname', $params)?$params['customer_lastname']:null,
        ];

        $response = $client->post($url, [
            "headers" => [
                'Authorization' => 'Bearer '.$this->getAccessToken(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            "json" => $body

        ]);

        $body = $response->getBody()->getContents();
        if($response->getStatusCode() == 200){
            $json = json_decode($body, true);

            if(array_key_exists('data', $json) && !empty( $json['data'])){
                $data = $json['data'];
                return new TransactionImpl($data);
            }else{
                $json =  json_decode($body, true);
                if(array_key_exists('message', $json) && !empty( $json['message'])){
                    $message  = $json['message'];
                }else{
                    $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
                }
                throw  new AdjeminPayException($message,$response->getStatusCode());
            }

        }else{

            $json =  json_decode($body, true);
            if(array_key_exists('message', $json) && !empty( $json['message'])){
                $message  = $json['message'];
            }else{
                $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
            }
            throw  new AdjeminPayException($message,$response->getStatusCode());
        }
    }

    public function getPaymentStatus($merchant_trans_id)
    {

        //Validate parameters
        if(empty($merchant_trans_id)) throw new AdjeminPayArgumentException("Bad request,  merchant_trans_id is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);

        if(empty($this->getAccessToken())){
            $message = 'The requested service needs credentials, but the ones provided were invalid.';
            throw  new AdjeminPayAuthException($message,401);
        }

        $client = new Client();
        $url = $this->getBaseUrl()."/v3/merchants/payment/$merchant_trans_id";

        $response = $client->get($url, [
            "headers" => [
                'Authorization' => 'Bearer '.$this->getAccessToken(),
                'Accept' => 'application/json'
            ]

        ]);

        $body = $response->getBody()->getContents();
        if($response->getStatusCode() == 200){
            $json = json_decode($body, true);

            if(array_key_exists('data', $json) && !empty( $json['data'])){
                $data = $json['data'];
                return new TransactionImpl($data);
            }else{
                $json =  json_decode($body, true);
                if(array_key_exists('message', $json) && !empty( $json['message'])){
                    $message  = $json['message'];
                }else{
                    $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
                }
                throw  new AdjeminPayException($message,$response->getStatusCode());
            }

        }else{

            $json =  json_decode($body, true);
            if(array_key_exists('message', $json) && !empty( $json['message'])){
                $message  = $json['message'];
            }else{
                $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
            }
            throw  new AdjeminPayException($message,$response->getStatusCode());
        }
    }

    public function getPaymentMethods($country_iso)
    {
        //Validate parameters
        if(empty($country_iso)) throw new AdjeminPayArgumentException("Bad request,  country_iso is required", StatusCode::codes[StatusCode::INVALID_PARAMS]);

        if(empty($this->getAccessToken())){
            $message = 'The requested service needs credentials, but the ones provided were invalid.';
            throw  new AdjeminPayAuthException($message,401);
        }

        $client = new Client();
        $url = $this->getBaseUrl()."/v3/operators/".strtoupper($country_iso);

        $response = $client->get($url, [
            "headers" => [
                'Authorization' => 'Bearer '.$this->getAccessToken(),
                'Accept' => 'application/json'
            ]
        ]);

        $body = $response->getBody()->getContents();
        if($response->getStatusCode() == 200){
            $json = json_decode($body, true);

            if(array_key_exists('data', $json) && !empty( $json['data'])){
                $data = $json['data'];
                if(!is_array($data)){
                     $data = (array)$data;
                }

                return $data;
            }else{
                $json =  json_decode($body, true);
                if(array_key_exists('message', $json) && !empty( $json['message'])){
                    $message  = $json['message'];
                }else{
                    $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
                }
                throw  new AdjeminPayException($message,$response->getStatusCode());
            }

        }else{

            $json =  json_decode($body, true);
            if(array_key_exists('message', $json) && !empty( $json['message'])){
                $message  = $json['message'];
            }else{
                $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
            }
            throw  new AdjeminPayException($message,$response->getStatusCode());
        }
    }

    public function getBalance()
    {

        if(empty($this->getAccessToken())){
            $message = 'The requested service needs credentials, but the ones provided were invalid.';
            throw  new AdjeminPayAuthException($message,401);
        }

        $client = new Client();
        $url = $this->getBaseUrl()."/v3/merchants/show";

        $response = $client->get($url, [
            "headers" => [
                'Authorization' => 'Bearer '.$this->getAccessToken(),
                'Accept' => 'application/json'
            ]
        ]);

        $body = $response->getBody()->getContents();
        if($response->getStatusCode() == 200){
            $json = json_decode($body, true);

            if(array_key_exists('data', $json) && !empty( $json['data'])){
                $data = $json['data'];
                if(!is_array($data)){
                    $data = (array)$data;
                }

                return $data;
            }else{
                $json =  json_decode($body, true);
                if(array_key_exists('message', $json) && !empty( $json['message'])){
                    $message  = $json['message'];
                }else{
                    $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
                }
                throw  new AdjeminPayException($message,$response->getStatusCode());
            }

        }else{

            $json =  json_decode($body, true);
            if(array_key_exists('message', $json) && !empty( $json['message'])){
                $message  = $json['message'];
            }else{
                $message  = StatusCode::messages[StatusCode::OPERATION_ERROR];
            }
            throw  new AdjeminPayException($message,$response->getStatusCode());
        }
    }

    public function getKnowIfWeCanCollectMoney()
    {
        // TODO: Implement getKnowIfWeCanCollectMoney() method.
    }
}