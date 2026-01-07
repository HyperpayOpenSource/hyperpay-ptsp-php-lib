# Hyperpay PTSP PHP Client

Lightweight PHP client to create Hyperpay PTSP payment links via the Hyperbill API using Basic Auth and Laravel’s HTTP client.

## Table of Contents
- Overview
- Requirements
- Installation
- Environment Variables
- Usage Example
- Status Lookup
- Payment Link Lookup by Token
- Request DTO Fields
- Error Handling
- Notes

## Overview
- Purpose: create Hyperpay PTSP payment links via `POST /v1/payment-link`.
- Auth: HTTP Basic Auth (credentials from environment).
- Transport: Laravel HTTP client (`illuminate/http`), no Guzzle.

## Requirements
- PHP 8.1+
- `illuminate/http` (installed via composer)
- Required env vars: `PAYMENT_LINK_BASIC_AUTH_USERNAME`, `PAYMENT_LINK_BASIC_AUTH_PASSWORD`
- Optional env vars: `PAYMENT_LINK_ENVIRONMENT` (defaults to 'production'), `PAYMENT_LINK_TIMEOUT` (defaults to 15s)

## Installation
From your project root (uses Laravel HTTP client, no Guzzle needed):
```bash
composer config repositories.hyperpay-ptsp '{"type": "path", "url": "packages/hyperpay-ptsp"}'
composer require hyperbill/hyperpay-ptsp:dev-main
```

## Environment Variables
Set these in your `.env` (Laravel) or system environment:
```
# Environment: 'production' (default) or 'sandbox'
PAYMENT_LINK_ENVIRONMENT=production
# Required
PAYMENT_LINK_BASIC_AUTH_USERNAME=your-basic-username
PAYMENT_LINK_BASIC_AUTH_PASSWORD=your-basic-password
# Optional: defaults to 15 seconds
PAYMENT_LINK_TIMEOUT=15
```

**Environment URLs (managed by the package):**
- **Production**: `https://ptsp.hyperpay.com` (default)
- **Sandbox**: `https://ptsp-stg.hyperpay.com`

To use sandbox, simply set `PAYMENT_LINK_ENVIRONMENT=sandbox`

## Publishable Config (Laravel)
- Register automatically via composer (service provider included).
- Publish the config to customize credentials/timeouts:
```bash
php artisan vendor:publish --tag=hyperpay-ptsp-config
```
- Config file path after publish: `config/hyperpay-ptsp.php`

## Usage Example
```php
<?php

use HyperBill\HyperpayPtsp\Config;
use HyperBill\HyperpayPtsp\Dto\HyperpayPtspRequest;
use HyperBill\HyperpayPtsp\HyperpayPtspClient;
use HyperBill\HyperpayPtsp\HyperpayPtspException;

require __DIR__ . '/vendor/autoload.php';

$config = Config::fromEnv(); // reads PAYMENT_LINK_* vars

$request = new HyperpayPtspRequest(
    customerName: 'Client Name',
    customerEmail: 'ibrahim.muhaisen@hyperpay.com',
    customerMobile: '00962787898819',
    lang: 'en',
    amount: 5656,
    currency: 'SAR',
    paymentOperation: 'pay',              // pay | authorize
    merchantReference: 'merchantInvoiceNumber',
    tokenization: 'yes',
    expirationDate: '2026-09-26 05:10:00',
    paymentMethods: ['VISA', 'MASTER'],
    sendEmail: true,
    getToken: true,
    sendSms: true
);

$client = new HyperpayPtspClient($config);

try {
    $paymentLinkUrl = $client->create($request);
    echo "Payment Link URL: " . $paymentLinkUrl;
} catch (HyperpayPtspException $e) {
    echo 'Error creating payment link: ' . $e->getMessage();
}
```

## Status Lookup (merchantReference)
```php
$client = new HyperpayPtspClient(Config::fromEnv());

try {
    $status = $client->statusByMerchantReference('your-merchant-reference');
    // $status is the decoded array response from /v1/status/merchantreference
} catch (HyperpayPtspException $e) {
    // handle error
}
```

## Payment Link Lookup by Token (x-api-key)
```php
$client = new HyperpayPtspClient(Config::fromEnv());

try {
    $details = $client->getPaymentLinkByToken(
        paymentLinkToken: 'your-payment-link-token',
        apiKey: 'your-x-api-key'
    );
    // $details is the decoded array response from /v1/payment-link/paymentlinktoken
} catch (HyperpayPtspException $e) {
    // handle error
}
```

## Request DTO Fields
- `customerName` (string, required)
- `customerEmail` (string, required)
- `customerMobile` (string|null)
- `lang` (string, e.g., `en` or `ar`)
- `amount` (float, required)
- `currency` (string, required)
- `paymentOperation` (`pay` | `authorize`, required)
- `merchantReference` (string, required)
- `tokenization` (`yes` | `no`, default `no`)
- `expirationDate` (string|null, format `Y-m-d H:i:s`)
- `paymentMethods` (array<string>|null)
- `sendEmail` (bool)
- `getToken` (bool)
- `sendSms` (bool)

## Error Handling
- `HyperpayPtspException` wraps HTTP and JSON errors.
- Laravel HTTP `->throw()` is used; HTTP errors are converted to exceptions.
- The client returns only the payment link URL; if it is missing, an exception is thrown.

## Notes
- Basic Auth credentials are read from environment via `Config::fromEnv()`.
- Payload mirrors the API contract; `merchantReference` maps internally to `merchant_invoice_number` on the API side.
- This package only creates Hyperpay PTSP payment links (`POST /v1/payment-link`).

