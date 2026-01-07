<?php

namespace HyperBill\HyperpayPtsp\Dto;

class CreatePaymentLinkRequest
{
    public ?string $customerName = null;
    public ?string $customerEmail = null;
    public ?string $customerMobile = null;
    public ?string $lang = null;
    public ?float $amount = null;
    public ?string $currency = null;
    public ?string $paymentOperation = null;
    public ?string $merchantReference = null;
    public ?string $tokenization = 'no';
    public ?string $expirationDate = null;
    /** @var string[]|null */
    public ?array $paymentMethods = null;
    public bool $sendEmail = false;
    public bool $sendSms = false;

    public function __construct()
    {
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

    public function setLang(?string $lang): self
    {
        $this->lang = $lang;
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

    public function setPaymentOperation(?string $paymentOperation): self
    {
        $this->paymentOperation = $paymentOperation;
        return $this;
    }

    public function setMerchantReference(?string $merchantReference): self
    {
        $this->merchantReference = $merchantReference;
        return $this;
    }

    public function setTokenization(string $tokenization): self
    {
        $this->tokenization = $tokenization;
        return $this;
    }

    public function setExpirationDate(?string $expirationDate): self
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function setPaymentMethods(?array $paymentMethods): self
    {
        $this->paymentMethods = $paymentMethods;
        return $this;
    }

    public function setSendEmail(bool $sendEmail): self
    {
        $this->sendEmail = $sendEmail;
        return $this;
    }

    public function setSendSms(bool $sendSms): self
    {
        $this->sendSms = $sendSms;
        return $this;
    }

    public function toPayload(): array
    {
        $payload = [
            'customer' => [
                'name' => $this->customerName,
                'email' => $this->customerEmail,
                'mobile' => $this->customerMobile,
            ],
            'lang' => $this->lang,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'paymentOperation' => $this->paymentOperation,
            'merchantReference' => $this->merchantReference,
            'tokenization' => ($this->tokenization && !in_array($this->tokenization, ["0", "no"]))  ? "yes" : "no",
            'expirationDate' => $this->expirationDate,
            'paymentMethods' => $this->paymentMethods,
            'sendEmail' => $this->sendEmail,
            'sendSms' => $this->sendSms,
        ];

        // Remove nulls to keep payload compact
        return array_filter($payload, static function ($value) {
            return $value !== null;
        });
    }
}

