<?php

namespace tests;

use AdjeminPay\AdjeminPay;
use AdjeminPay\Transaction;
use PHPUnit\Framework\TestCase;

class AdjeminPayTest extends TestCase{

    protected AdjeminPay $adjeminPay;

    /**
     *
     * @throws \AdjeminPay\Exception\AdjeminPayAuthException
     */
    public function setUp(): void
    {
        //Create AdjeminPay instance
        $clientId = "CLIENT_ID"; //Client ID of an application created on  Merchant backoffice
        $clientSecret  = "CLIENT_SECRET"; //Client Secret of an application created on  Merchant backoffice
        $this->adjeminPay = new AdjeminPay($clientId, $clientSecret);
    }

    /**
     * @throws \AdjeminPay\Exception\AdjeminPayArgumentException
     * @throws \AdjeminPay\Exception\AdjeminPayException
     * @throws \AdjeminPay\Exception\AdjeminPayAuthException
     */
    public function  testGetPaymentStatus():void{

        //Get Checkout or Payout Status by merchant_transaction_id
        /** @var Transaction $transaction Transaction*/
        $merchant_transaction_id = '1b56c3fb-24f5-407e-8779-f239ed086e6e';
        $transaction = $this->adjeminPay->getPaymentStatus($merchant_transaction_id);
        $this->assertInstanceOf(Transaction::class, $transaction);
        var_dump($transaction);

    }

}