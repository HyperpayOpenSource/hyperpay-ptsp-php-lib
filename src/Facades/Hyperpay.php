<?php

namespace HyperBill\HyperpayPtsp\Facades;

use HyperBill\HyperpayPtsp\Dto\CreatePaymentLinkRequest;
use HyperBill\HyperpayPtsp\Dto\CreatePaymentLinkResponse;
use HyperBill\HyperpayPtsp\Dto\PaymentLinkResponse;
use HyperBill\HyperpayPtsp\Dto\PaymentStatusResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static CreatePaymentLinkResponse createPaymentLink(CreatePaymentLinkRequest $request)
 * @method static PaymentLinkResponse getPaymentLinkByToken(string $paymentLinkToken, string $apiKey)
 * @method static PaymentStatusResponse getStatusByMerchantReference(string $merchantReference)
 *
 * @see \HyperBill\HyperpayPtsp\HyperpayPtspService
 */
class Hyperpay extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'hyperpay-ptsp';
    }
}

