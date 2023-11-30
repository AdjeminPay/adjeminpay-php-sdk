<?php

namespace AdjeminPay;

class TransactionImpl implements Transaction
{

    public $data;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getReference(): ?string
    {
        return array_key_exists('reference', $this->data)?$this->data['reference']:null;
    }

    public function getOperatorCode(): ?string
    {
        return array_key_exists('operator_code', $this->data)?$this->data['operator_code']:null;
    }

    public function getAmount(): ?float
    {
        return array_key_exists('amount', $this->data)?$this->data['amount']:null;
    }

    public function getCurrencyCode(): ?string
    {
        return array_key_exists('currency_code', $this->data)?$this->data['currency_code']:null;
    }

    public function getRecipientNumber(): ?string
    {
        return array_key_exists('recipient_number', $this->data)?$this->data['recipient_number']:null;
    }

    public function getRecipientName(): ?string
    {
        return array_key_exists('recipient_name', $this->data)?$this->data['recipient_name']:null;
    }

    public function getRecipientEmail(): ?string
    {
        return array_key_exists('recipient_email', $this->data)?$this->data['recipient_email']:null;
    }

    public function getRecipientPhotoUrl(): ?string
    {
        return array_key_exists('recipient_photo_url', $this->data)?$this->data['recipient_photo_url']:null;
    }

    public function getDesignation(): ?string
    {
        return array_key_exists('designation', $this->data)?$this->data['designation']:null;
    }

    public function getStatus(): ?string
    {
        return array_key_exists('status', $this->data)?$this->data['status']:null;
    }

    public function getFees(): ?float
    {
        return array_key_exists('fees', $this->data)?$this->data['fees']:null;
    }

    public function getFailureReason(): ?string
    {
        return array_key_exists('failure_reason', $this->data)?$this->data['failure_reason']:null;
    }

    public function getMerchantTransID(): ?string
    {
        return array_key_exists('merchant_trans_id', $this->data)?$this->data['merchant_trans_id']:null;
    }

    public function getMerchantTransData(): ?string
    {
        return array_key_exists('merchant_trans_data', $this->data)?$this->data['merchant_trans_data']:null;
    }

    public function getWebhookUrl(): ?string
    {
        return array_key_exists('webhook_url', $this->data)?$this->data['webhook_url']:null;
    }

    public function getReturnUrl(): ?string
    {
        return array_key_exists('return_url', $this->data)?$this->data['return_url']:null;
    }

    public function getCancelUrl(): ?string
    {
        return array_key_exists('cancel_url', $this->data)?$this->data['cancel_url']:null;
    }

    public function isPayIn(): bool
    {
        return array_key_exists('is_payin', $this->data)?$this->data['is_payin']:false;
    }

    public function isWaiting(): bool
    {
        return array_key_exists('is_waiting', $this->data)?$this->data['is_waiting']:false;
    }

    public function isCompleted(): bool
    {
        return array_key_exists('is_completed', $this->data)?$this->data['is_completed']:false;
    }

    public function getServicePaymentUrl(): ?string
    {
        return array_key_exists('service_payment_url', $this->data)?$this->data['service_payment_url']:null;
    }

    public function getOperatorPaymentUrl(): ?string
    {
        return array_key_exists('operator_trans_url', $this->data)?$this->data['operator_trans_url']:null;
    }

    public function createdAt(): ?string
    {
        return array_key_exists('created_at', $this->data)?$this->data['created_at']:null;
    }

    public function updatedAt(): ?string
    {
        return array_key_exists('updated_at', $this->data)?$this->data['updated_at']:null;
    }
}