<?php

namespace HyperBill\HyperpayPtsp\Dto;

class CreatePaymentLinkResponse
{
    public ?string $paymentLink = null;
    public ?string $paymentLinkToken = null;
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
        $paymentLink = $data['payment_link'] ?? $data['paymentLinkUrl'] ?? $data['payment_link_url'] ?? null;

        if (!$paymentLink || !is_string($paymentLink)) {
            throw new \InvalidArgumentException('paymentLinkUrl not found in API response.');
        }

        return (new self())
            ->setPaymentLink($paymentLink)
            ->setPaymentLinkToken($data['paymentLinkToken'] ?? $data['payment_link_token'] ?? null)
            ->setResponseCode($data['responseCode'] ?? null)
            ->setResponseMessage($data['responseMessage'] ?? null);
    }

    public function toArray(): array
    {
        return [
            'paymentLink' => $this->paymentLink,
            'paymentLinkToken' => $this->paymentLinkToken,
            'responseCode' => $this->responseCode,
            'responseMessage' => $this->responseMessage,
        ];
    }
}

