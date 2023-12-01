<?php

use AdjeminPay\AdjeminPay;
use AdjeminPay\Transaction;

//Create AdjeminPay instance
$clientId = "CLIENT_ID"; //Client ID of an application created on  Merchant backoffice
$clientSecret  = "CLIENT_SECRET"; //Client Secret of an application created on  Merchant backoffice
$adjeminPay = new AdjeminPay($clientId, $clientSecret);


//Get Checkout or Payout Status by merchant_transaction_id
/** @var Transaction $transaction Transaction*/
$merchant_transaction_id = 'b72e51dc-7211-4e85-a937-5372c8769d36';
try {
    $transaction = $adjeminPay->getPaymentStatus($merchant_transaction_id);
} catch (Exception $e) {
    echo  "Error: ".$e->getMessage();
}

var_dump($transaction);


