# Hyperpay PTSP PHP Client

Lightweight PHP client to create Hyperpay PTSP payment links via the Hyperpay PTSP API using Basic Auth and Laravel’s HTTP client.

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

### Option 1: Install from GitHub (Recommended)

Install directly from the official GitHub repository:

```bash
composer require hyperpay/hyperpay-ptsp
```

Or specify the repository in your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/HyperpayOpenSource/hyperpay-ptsp-php-lib"
        }
    ],
    "require": {
        "hyperpay/hyperpay-ptsp": "^1.0"
    }
}
```

Then run:

```bash
composer install
```

### Option 2: Install from Local Path (Development)

For local development, you can use a path repository:

```bash
composer config repositories.hyperpay-ptsp '{"type": "path", "url": "packages/hyperpay-ptsp"}'
composer require hyperpay/hyperpay-ptsp:dev-main
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

### Using the Facade (Laravel - Recommended)

```php
<?php

use Hyperpay\HyperpayPtsp\Dto\CreatePaymentLinkRequest;
use Hyperpay\HyperpayPtsp\Facades\Hyperpay;
use Hyperpay\HyperpayPtsp\HyperpayPtspException;

$request = new CreatePaymentLinkRequest();
$request->customerName = 'Client Name';
$request->customerEmail = 'ibrahim.muhaisen@hyperpay.com';
$request->customerMobile = '00962787898819';
$request->lang = 'en';
$request->amount = 5656.00;
$request->currency = 'SAR';
$request->paymentOperation = 'pay'; // pay | authorize
$request->merchantReference = 'INV-' . time();
$request->tokenization = true;
$request->expirationDate = '2026-09-26 05:10:00';
$request->paymentMethods = ['VISA', 'MASTER'];
$request->sendEmail = true;
$request->sendSms = true;

try {
    $response = Hyperpay::createPaymentLink($request);
    echo "Payment Link URL: " . $response->paymentLinkUrl;
    echo "Payment Link ID: " . $response->paymentLinkId;
} catch (HyperpayPtspException $e) {
    echo 'Error: ' . $e->getMessage();
}
```

### Using the Service Directly

```php
<?php

use Hyperpay\HyperpayPtsp\Config;
use Hyperpay\HyperpayPtsp\Dto\CreatePaymentLinkRequest;
use Hyperpay\HyperpayPtsp\HyperpayPtspService;
use Hyperpay\HyperpayPtsp\HyperpayPtspException;

$config = Config::fromLaravelConfig(); // or Config::fromEnv()

$request = new CreatePaymentLinkRequest();
$request->customerName = 'Client Name';
$request->customerEmail = 'ibrahim.muhaisen@hyperpay.com';
$request->customerMobile = '00962787898819';
$request->amount = 5656.00;
$request->currency = 'SAR';
$request->paymentOperation = 'pay';
$request->merchantReference = 'INV-' . time();

$service = new HyperpayPtspService($config);

try {
    $response = $service->createPaymentLink($request);
    echo "Payment Link URL: " . $response->paymentLinkUrl;
} catch (HyperpayPtspException $e) {
    echo 'Error: ' . $e->getMessage();
}
```

## Status Lookup (merchantReference)

### Using the Facade (Laravel)

```php
use Hyperpay\HyperpayPtsp\Facades\Hyperpay;
use Hyperpay\HyperpayPtsp\HyperpayPtspException;

try {
    $status = Hyperpay::getStatusByMerchantReference('INV-123456');
    
    echo "Response Code: " . $status->responseCode;
    echo "Response Message: " . $status->responseMessage;
    echo "Hyperpay ID: " . $status->hyperpayId;
    echo "Amount: " . $status->amount;
    echo "Currency: " . $status->currency;
    echo "Customer Name: " . $status->customerName;
    echo "Payment Method: " . $status->paymentMethod;
} catch (HyperpayPtspException $e) {
    echo 'Error: ' . $e->getMessage();
}
```

### Using the Service Directly

```php
use Hyperpay\HyperpayPtsp\HyperpayPtspService;

$service = new HyperpayPtspService();

try {
    $status = $service->getStatusByMerchantReference('INV-123456');
    // $status is a PaymentStatusResponse DTO
} catch (HyperpayPtspException $e) {
    // handle error
}
```

## Payment Link Lookup by Token (x-api-key)

### Using the Facade (Laravel)

```php
use Hyperpay\HyperpayPtsp\Facades\Hyperpay;
use Hyperpay\HyperpayPtsp\HyperpayPtspException;

try {
    $paymentLink = Hyperpay::getPaymentLinkByToken(
        'c7e7f6957bf1bb6f6ba5ca1f93db1849',
        'hb_ZXv2o3GYeShnkXdibZokmzPO1pnrfXCtOP9CUBw6ajTSgPvjH0nDeT6H25He'
    );
    
    echo "Payment Link: " . $paymentLink->paymentLink;
    echo "Status: " . $paymentLink->status;
    echo "Amount: " . $paymentLink->amount;
} catch (HyperpayPtspException $e) {
    echo 'Error: ' . $e->getMessage();
}
```

### Using the Service Directly

```php
use Hyperpay\HyperpayPtsp\HyperpayPtspService;

$service = new HyperpayPtspService();

try {
    $paymentLink = $service->getPaymentLinkByToken(
        paymentLinkToken: 'c7e7f6957bf1bb6f6ba5ca1f93db1849',
        apiKey: 'hb_ZXv2o3GYeShnkXdibZokmzPO1pnrfXCtOP9CUBw6ajTSgPvjH0nDeT6H25He'
    );
    // $paymentLink is a PaymentLinkResponse DTO
} catch (HyperpayPtspException $e) {
    // handle error
}
```

## Request DTO Fields (CreatePaymentLinkRequest)

| Field | Type | Required | Description |
| :--- | :--- | :---: | :--- |
| `customerName` | string | ✓ | Customer's full name |
| `customerEmail` | string | ✓ | Customer's email address |
| `customerMobile` | string\|null | | Customer's mobile number (international format) |
| `lang` | string | | Language code (`en` or `ar`, defaults to `en`) |
| `amount` | float | ✓ | Transaction amount (e.g., `250.00`) |
| `currency` | string | ✓ | ISO currency code (e.g., `SAR`, `USD`, `AED`) |
| `paymentOperation` | string | ✓ | `pay` (immediate) or `authorize` (two-step) |
| `merchantReference` | string | ✓ | Your unique transaction identifier |
| `tokenization` | bool\|null | | Enable card tokenization for recurring payments |
| `expirationDate` | string\|null | | Link expiration (format: `Y-m-d H:i:s`) |
| `paymentMethods` | array\|null | | Allowed payment methods (e.g., `['VISA', 'MASTER', 'MADA']`) |
| `sendEmail` | bool | | Send payment link via email (default: `false`) |
| `sendSms` | bool | | Send payment link via SMS (default: `false`) |

## Response DTO Fields

### CreatePaymentLinkResponse

- `paymentLinkUrl` - The payment link URL to share with customers
- `paymentLinkId` - Unique payment link identifier
- `responseCode` - Status code (`00001` = success)
- `responseMessage` - Human-readable status message
- `merchantReference` - Your transaction reference
- `amount` - Transaction amount
- `currency` - Currency code
- `customerName` - Customer's name
- `customerEmail` - Customer's email
- `customerMobile` - Customer's mobile
- And more...

### PaymentStatusResponse

- `responseCode` - Transaction status code
- `responseMessage` - Status message
- `hyperpayId` - Hyperpay transaction ID
- `paymentMethod` - Payment method used
- `merchantReference` - Your reference
- `amount` - Transaction amount
- `currency` - Currency code
- `customerName` - Customer's name
- `customerEmail` - Customer's email
- `customerMobile` - Customer's mobile
- `acquirerResponseCode` - Bank response code
- `cardToken` - Tokenized card (if tokenization enabled)
- `agreementId` - Agreement ID for recurring payments
- And more...

## Error Handling

The package throws `HyperpayPtspException` for all API and validation errors:

```php
use Hyperpay\HyperpayPtsp\HyperpayPtspException;

try {
    $response = Hyperpay::createPaymentLink($request);
} catch (HyperpayPtspException $e) {
    // Exception message includes:
    // - Response code (e.g., [10002])
    // - Response message
    // - Field-level validation errors (if any)
    echo $e->getMessage();
    // Example: "[10002] Duplicate Merchant Reference | The merchant reference has already been taken."
}
```

### Common Error Codes

| Code | Description |
| :--- | :--- |
| `00000` | Success |
| `10002` | Duplicate Merchant Reference |
| `10000` | Validation Error |
| `60000` | Transaction Declined |
| `60001` | Insufficient Funds |

For a complete list of error codes, see the [Integration Documentation](https://github.com/HyperpayOpenSource/hyperpay-ptsp-php-lib/blob/master/docs/integration.md).

## Features

✅ **Create Payment Links** - Generate secure payment URLs  
✅ **Get Payment Status** - Check transaction status by merchant reference  
✅ **Get Payment Link by Token** - Retrieve payment link details  
✅ **Environment Support** - Production and Sandbox environments  
✅ **Tokenization** - Support for recurring payments  
✅ **Laravel Integration** - Facade and Service Provider included  
✅ **Error Handling** - Comprehensive exception messages with validation details

## Links

- **GitHub Repository**: [https://github.com/HyperpayOpenSource/hyperpay-ptsp-php-lib](https://github.com/HyperpayOpenSource/hyperpay-ptsp-php-lib)
- **Integration Documentation**: Full API documentation available in the repository
- **Packagist**: [hyperpay/hyperpay-ptsp](https://packagist.org/packages/hyperpay/hyperpay-ptsp)

## License

MIT

## Support

For issues and support, please visit the [GitHub Issues](https://github.com/HyperpayOpenSource/hyperpay-ptsp-php-lib/issues) page.

