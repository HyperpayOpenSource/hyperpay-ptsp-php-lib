<?php

namespace HyperBill\HyperpayPtsp\Dto;

class PaymentStatusResponse
{
    public ?string $responseCode = null;
    public ?string $responseMessage = null;
    public ?string $hyperpayId = null;
    public ?string $paymentMethod = null;
    public ?string $merchantReference = null;
    public ?float $amount = null;
    public ?string $currency = null;
    public ?string $customerName = null;
    public ?string $customerEmail = null;
    public ?string $customerMobile = null;
    public ?string $paymentOperation = null;
    public ?string $acquirerResponseCode = null;
    public ?string $transactionDate = null;
    public ?string $rrn = null;
    public ?string $authorizationCode = null;
    public ?string $cardToken = null;
    public ?string $agreementId = null;

    public function __construct()
    {
    }

    public function setResponseCode(?string $responseCode): self
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    public function setResponseMessage(?string $responseMessage): self
    {
        $this->responseMessage = $responseMessage;
        return $this;
    }

    public function setHyperpayId(?string $hyperpayId): self
    {
        $this->hyperpayId = $hyperpayId;
        return $this;
    }

    public function setPaymentMethod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function setMerchantReference(?string $merchantReference): self
    {
        $this->merchantReference = $merchantReference;
        return $this;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function setCustomerName(?string $customerName): self
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function setCustomerEmail(?string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    public function setCustomerMobile(?string $customerMobile): self
    {
        $this->customerMobile = $customerMobile;
        return $this;
    }

    public function setPaymentOperation(?string $paymentOperation): self
    {
        $this->paymentOperation = $paymentOperation;
        return $this;
    }

    public function setAcquirerResponseCode(?string $acquirerResponseCode): self
    {
        $this->acquirerResponseCode = $acquirerResponseCode;
        return $this;
    }

    public function setTransactionDate(?string $transactionDate): self
    {
        $this->transactionDate = $transactionDate;
        return $this;
    }

    public function setRrn(?string $rrn): self
    {
        $this->rrn = $rrn;
        return $this;
    }

    public function setAuthorizationCode(?string $authorizationCode): self
    {
        $this->authorizationCode = $authorizationCode;
        return $this;
    }

    public function setCardToken(?string $cardToken): self
    {
        $this->cardToken = $cardToken;
        return $this;
    }

    public function setAgreementId(?string $agreementId): self
    {
        $this->agreementId = $agreementId;
        return $this;
    }

    public static function fromArray(array $data): self
    {
        $customer = $data['customer'] ?? [];
        
        return (new self())
            ->setResponseCode($data['responseCode'] ?? 'UNKNOWN')
            ->setResponseMessage($data['responseMessage'] ?? 'Unknown response')
            ->setMerchantReference($data['merchantReference'] ?? '')
            ->setAmount(isset($data['amount']) ? (float) $data['amount'] : 0.0)
            ->setCurrency($data['currency'] ?? '')
            ->setHyperpayId($data['hyperpayId'] ?? null)
            ->setPaymentMethod($data['paymentMethod'] ?? null)
            ->setCustomerName($customer['name'] ?? null)
            ->setCustomerEmail($customer['email'] ?? null)
            ->setCustomerMobile($customer['mobile'] ?? null)
            ->setPaymentOperation($data['paymentOperation'] ?? null)
            ->setAcquirerResponseCode($data['acquirerResponseCode'] ?? null)
            ->setTransactionDate($data['transactionDate'] ?? null)
            ->setRrn($data['rrn'] ?? null)
            ->setAuthorizationCode($data['authorizationCode'] ?? null)
            ->setCardToken($data['cardToken'] ?? null)
            ->setAgreementId($data['agreementId'] ?? null);
    }

    public function toArray(): array
    {
        return array_filter([
            'responseCode' => $this->responseCode,
            'responseMessage' => $this->responseMessage,
            'hyperpayId' => $this->hyperpayId,
            'paymentMethod' => $this->paymentMethod,
            'merchantReference' => $this->merchantReference,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'customerName' => $this->customerName,
            'customerEmail' => $this->customerEmail,
            'customerMobile' => $this->customerMobile,
            'paymentOperation' => $this->paymentOperation,
            'acquirerResponseCode' => $this->acquirerResponseCode,
            'transactionDate' => $this->transactionDate,
            'rrn' => $this->rrn,
            'authorizationCode' => $this->authorizationCode,
            'cardToken' => $this->cardToken,
            'agreementId' => $this->agreementId,
        ], function($value) { return $value !== null; });
    }

    public function isSuccessful(): bool
    {
        return $this->responseCode === '00000';
    }
}

