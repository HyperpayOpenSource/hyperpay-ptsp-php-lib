<?php

use HyperBill\HyperpayPtsp\Config;
use HyperBill\HyperpayPtsp\Dto\HyperpayPtspRequest;
use HyperBill\HyperpayPtsp\HyperpayPtspClient;
use HyperBill\HyperpayPtsp\HyperpayPtspException;

require __DIR__ . '/../../vendor/autoload.php';

// Ensure PAYMENT_LINK_* vars are set in your environment or .env
$config = Config::fromEnv();

$request = new HyperpayPtspRequest(
    customerName: 'Client Name',
    customerEmail: 'ibrahim.muhaisen@hyperpay.com',
    customerMobile: '00962787898819',
    lang: 'en',
    amount: 5656,
    currency: 'SAR',
    paymentOperation: 'pay', // pay | authorize
    merchantReference: 'merchantInvoiceNumber',
    tokenization: 'yes',
    expirationDate: '2026-09-26 05:10:00',
    paymentMethods: ['VISA', 'MASTER'],
    sendEmail: true,
    sendSms: true
);

$client = new HyperpayPtspClient($config);

try {
    $paymentLinkUrl = $client->create($request);
    echo "Payment Link URL: " . $paymentLinkUrl . PHP_EOL;
} catch (HyperpayPtspException $e) {
    echo 'Error creating payment link: ' . $e->getMessage() . PHP_EOL;
    if ($e->getPrevious()) {
        echo 'Details: ' . $e->getPrevious()->getMessage() . PHP_EOL;
    }
}
