# AdjeminPay PHP SDK

[![Latest Stable Version](https://poser.pugx.org/adjeminpay/adjeminpay_php_sdk/v)](//packagist.org/packages/adjeminpay/adjeminpay_php_sdk) [![Total Downloads](https://poser.pugx.org/adjeminpay/adjeminpay-php-sdk/downloads)](//packagist.org/packages/adjeminpay/adjeminpay_php_sdk) [![Latest Unstable Version](https://poser.pugx.org/adjeminpay/adjeminpay_php_sdk/v/unstable)](//packagist.org/packages/adjeminpay/adjeminpay_php_sdk) [![License](https://poser.pugx.org/adjeminpay/adjeminpay_php_sdk/license)](//packagist.org/packages/adjeminpay/adjeminpay_php_sdk)

The AdjeminPay PHP SDK provides convenient access to the AdjeminPay API from
applications written in the PHP language. It includes a pre-defined set of
classes for API resources that initialize themselves dynamically from API
responses which makes it compatible with a wide range of versions of the AdjeminPay API

## Requirements

PHP 7.4.0 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require adjeminpay/adjeminpay_php_sdk
```

## Create a checkout
```php
<?php

use AdjeminPay\AdjeminPay;
use AdjeminPay\Transaction;

//Create AdjeminPay instance
$clientId = "CLIENT_ID"; //Client ID of an application created on  Merchant backoffice
$clientSecret  = "CLIENT_SECRET"; //Client Secret of an application created on  Merchant backoffice
$adjeminPay = new AdjeminPay($clientId, $clientSecret);

//Create a checkout
/** @var Transaction $transaction Transaction*/
$transaction = $adjeminPay->createCheckout([
  'amount' => 200, //required
  'currency_code' => 'XOF', //required
  'merchant_trans_id' => 'b72e51dc-7211-4e85-a937-5372c8769d36', //required You provide a merchant_trans_id
  'designation' => 'Paiement de facture', //required
  'customer_recipient_number' => '2250556888385', //required
  "customer_email" =>"angebagui@adjemin.com",
  "customer_firstname" =>"Ange",
  "customer_lastname" =>"Bagui",
  "webhook_url":"https://example.com/webhook_url",
  "return_url": "https://example.com/success",
  "cancel_url": "https://example.com/failure"
]);

//Complete the checkout
/** @var Transaction $transaction Transaction*/
$transaction = $adjeminPay->completeCheckout('b72e51dc-7211-4e85-a937-5372c8769d36',[
  'operator_code' => 'wave_ci', //Your get all payments Methods with $adjeminPay->getPaymentMethods('CI')
  'customer_recipient_number' => '2250556888385', //required
  "customer_email" =>"angebagui@adjemin.com",
  "customer_firstname" =>"Ange",
  "customer_lastname" =>"Bagui"
]);

var_dump($transaction);

```

## Payment Status
```php
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
$transaction = $adjeminPay->getPaymentStatus($merchant_transaction_id);

var_dump($transaction);

```

## Create a Payout
```php
<?php

use AdjeminPay\AdjeminPay;
use AdjeminPay\Transaction;

//Create AdjeminPay instance
$clientId = "CLIENT_ID"; //Client ID of an application created on  Merchant backoffice
$clientSecret  = "CLIENT_SECRET"; //Client Secret of an application created on  Merchant backoffice
$adjeminPay = new AdjeminPay($clientId, $clientSecret);

//Create a Payout
/** @var Transaction $transaction Transaction*/
$transaction = $adjeminPay->createPayout([
  'operator_code' => 'wave_ci',
  'amount' => 200000, //required
  'currency_code' => 'XOF', //required
  'merchant_trans_id' => 'b72e51dc-7211-4e85-a937-5372c8769d36', //required You provide a merchant_trans_id
  'designation' => 'Paiement de salaire', //required
  'customer_recipient_number' => '2250556888385', //required
  "customer_email" =>"angebagui@adjemin.com",
  "customer_firstname" =>"Ange",
  "customer_lastname" =>"Bagui",
  "webhook_url":"https://example.com/webhook_url"
]);


```


## Get Payment Methods by Country ISO Code
```php
<?php

use AdjeminPay\AdjeminPay;

//Create AdjeminPay instance
$clientId = "CLIENT_ID"; //Client ID of an application created on  Merchant backoffice
$clientSecret  = "CLIENT_SECRET"; //Client Secret of an application created on  Merchant backoffice
$adjeminPay = new AdjeminPay($clientId, $clientSecret);


//Get Payment Methods by Country CODE
$country_iso = 'CI';
$paymentMethods = $adjeminPay->getPaymentMethods($country_iso);

var_dump($paymentMethods);

```
