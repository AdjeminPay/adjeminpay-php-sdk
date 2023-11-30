<?php

namespace AdjeminPay;

interface Transaction
{
    //STATUS
    const  CREATED  = "CREATED";
    const  INITIATED  = "INITIATED";
    const  PENDING = "PENDING";
    const  SUCCESSFUL = "SUCCESSFUL";
    const  FAILED = "FAILED";

    public function  getReference():?string;
    public function  getOperatorCode():?string;
    public function  getAmount():?float;
    public function  getCurrencyCode():?string;
    public function  getRecipientNumber():?string;
    public function  getRecipientName():?string;
    public function  getRecipientEmail():?string;
    public function  getRecipientPhotoUrl():?string;
    public function  getDesignation():?string;
    public function  getStatus():?string;
    public function  getFees():?float;
    public function  getFailureReason():?string;
    public function  getMerchantTransID():?string;
    public function  getMerchantTransData():?string;
    public function  getWebhookUrl():?string;
    public function  getReturnUrl():?string;
    public function  getCancelUrl():?string;
    public function  isPayIn():bool;
    public function  isWaiting():bool;
    public function  isCompleted():bool;
    public function  getServicePaymentUrl():?string;
    public function  getOperatorPaymentUrl():?string;
    public function  createdAt():?string;
    public function  updatedAt():?string;

}