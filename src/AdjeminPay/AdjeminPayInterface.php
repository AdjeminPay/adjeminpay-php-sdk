<?php

namespace AdjeminPay;

interface AdjeminPayInterface
{

    public function getBaseUrl();
    public function getClientId();
    public function getClientSecret();
    public function obtainAccessToken();
    public function getAccessToken();
    public function createCheckout($params):Transaction;
    public function completeCheckout($merchant_trans_id, $params):Transaction;
    public function createPayout($params):Transaction;
    public function getPaymentStatus($merchant_trans_id);
    public function getPaymentMethods($country_iso);
    public function getBalance();
    public function getKnowIfWeCanCollectMoney();

}