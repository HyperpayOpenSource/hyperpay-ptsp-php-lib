<?php

namespace HyperBill\HyperpayPtsp\Dto;

class PaymentLinkResponse
{
    public ?string $paymentLink = null;
    public ?string $paymentLinkToken = null;
    public ?string $merchantReference = null;
    public ?float $amount = null;
    public ?string $currency = null;
    public ?string $status = null;
    public ?string $customerName = null;
    public ?string $customerEmail = null;
    public ?string $customerMobile = null;
    public ?string $responseCode = null;
    public ?string $responseMessage = null;

    public function __construct()
    {
    }

    public function setPaymentLink(?string $paymentLink): self
    {
        $this->paymentLink = $paymentLink;
        return $this;
    }

    public function setPaymentLinkToken(?string $paymentLinkToken): self
    {
        $this->paymentLinkToken = $paymentLinkToken;
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

    public function setStatus(?string $status): self
    {
        $this->status = $status;
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

    public static function fromArray(array $data): self
    {
        $customer = $data['customer'] ?? [];
        
        return (new self())
            ->setPaymentLink($data['payment_link'] ?? $data['paymentLinkUrl'] ?? $data['payment_link_url'] ?? null)
            ->setPaymentLinkToken($data['paymentLinkToken'] ?? $data['payment_link_token'] ?? null)
            ->setMerchantReference($data['merchantReference'] ?? $data['merchant_reference'] ?? null)
            ->setAmount(isset($data['amount']) ? (float) $data['amount'] : null)
            ->setCurrency($data['currency'] ?? null)
            ->setStatus($data['status'] ?? null)
            ->setCustomerName($customer['name'] ?? null)
            ->setCustomerEmail($customer['email'] ?? null)
            ->setCustomerMobile($customer['mobile'] ?? null)
            ->setResponseCode($data['responseCode'] ?? null)
            ->setResponseMessage($data['responseMessage'] ?? null);
    }

    public function toArray(): array
    {
        return array_filter([
            'paymentLink' => $this->paymentLink,
            'paymentLinkToken' => $this->paymentLinkToken,
            'merchantReference' => $this->merchantReference,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'customerName' => $this->customerName,
            'customerEmail' => $this->customerEmail,
            'customerMobile' => $this->customerMobile,
            'responseCode' => $this->responseCode,
            'responseMessage' => $this->responseMessage,
        ], function($value) { return $value !== null; });
    }
}

