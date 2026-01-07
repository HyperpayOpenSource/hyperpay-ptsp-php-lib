<?php

namespace HyperBill\HyperpayPtsp;

use HyperBill\HyperpayPtsp\Config as HyperpayConfig;
use HyperBill\HyperpayPtsp\Dto\CreatePaymentLinkRequest;
use HyperBill\HyperpayPtsp\Dto\CreatePaymentLinkResponse;
use HyperBill\HyperpayPtsp\Dto\PaymentLinkResponse;
use HyperBill\HyperpayPtsp\Dto\PaymentStatusResponse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HyperpayPtspService
{
    private Config $config;

    public function __construct(?Config $config = null)
    {
        $this->config = $config ?? HyperpayConfig::fromLaravelConfig();
    }

    /**
     * Create a payment link and return the full response.
     *
     * @throws HyperpayPtspException
     */
    public function createPaymentLink(CreatePaymentLinkRequest $request): CreatePaymentLinkResponse
    {
        $response = $this->makeRequest(
            'POST',
            '/v1/payment-link',
            $request->toPayload(),
            'basic'
        );

        return CreatePaymentLinkResponse::fromArray($response);
    }

    /**
     * Retrieve payment link details by token.
     *
     * @throws HyperpayPtspException
     */
    public function getPaymentLinkByToken(string $paymentLinkToken, string $apiKey): PaymentLinkResponse
    {
        $response = $this->makeRequest(
            'GET',
            "/v1/payment-link/{$paymentLinkToken}",
            null,
            'apikey',
            $apiKey,
            false
        );

        return PaymentLinkResponse::fromArray($response);
    }

    /**
     * Retrieve payment status by merchant reference.
     *
     * @throws HyperpayPtspException
     */
    public function getStatusByMerchantReference(string $merchantReference): PaymentStatusResponse
    {
        $response = $this->makeRequest(
            'GET',
            "/v1/status/{$merchantReference}",
            null,
            'basic'
        );

        return PaymentStatusResponse::fromArray($response);
    }

    /**
     * Centralized API request handler.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE, etc.)
     * @param string $endpoint API endpoint path
     * @param array|null $data Request payload or query parameters
     * @param string $authType Authentication type: 'basic' or 'apikey'
     * @param string|null $apiKey API key for x-api-key header
     * @param bool $useBaseUrl Whether to use the configured base URL
     * @return array Decoded JSON response
     * @throws HyperpayPtspException
     */
    private function makeRequest(
        string $method,
        string $endpoint,
        ?array $data = null,
        string $authType = 'basic',
        ?string $apiKey = null,
        bool $useBaseUrl = true
    ): array {
        try {
            // Build the HTTP client
            $client = $this->buildHttpClient($authType, $apiKey, $useBaseUrl);

            // Build the full URL if not using base URL
            $url = $useBaseUrl ? $endpoint : rtrim($this->config->baseUrl(), '/') . $endpoint;

            // Make the request based on method
            $response = $this->executeRequest($client, strtoupper($method), $url, $data ?? []);

            $response->throw();

            return $this->decodeResponse($response);
        } catch (RequestException $e) {
            throw new HyperpayPtspException(
                $this->formatErrorMessage($e),
                $e->getCode(),
                $e
            );
        } catch (\Exception $e) {
            throw new HyperpayPtspException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute HTTP request based on method.
     *
     * @param PendingRequest $client
     * @param string $method
     * @param string $url
     * @param array $data
     * @return Response
     * @throws HyperpayPtspException
     */
    private function executeRequest(PendingRequest $client, string $method, string $url, array $data): Response
    {
        switch ($method) {
            case 'GET':
                return $client->get($url, $data);
            case 'POST':
                return $client->post($url, $data);
            case 'PUT':
                return $client->put($url, $data);
            case 'PATCH':
                return $client->patch($url, $data);
            case 'DELETE':
                return $client->delete($url, $data);
            default:
                throw new HyperpayPtspException("Unsupported HTTP method: {$method}");
        }
    }

    /**
     * Build and configure the HTTP client with appropriate authentication.
     *
     * @param string $authType Authentication type
     * @param string|null $apiKey API key if using apikey auth
     * @param bool $useBaseUrl Whether to set base URL
     * @return PendingRequest
     */
    private function buildHttpClient(string $authType, ?string $apiKey, bool $useBaseUrl): PendingRequest
    {
        $client = Http::timeout($this->config->timeout() ?? 15)
            ->acceptJson()
            ->asJson();

        if ($useBaseUrl) {
            $client = $client->baseUrl($this->config->baseUrl());
        }

        if ($authType === 'basic') {
            return $client->withBasicAuth($this->config->username(), $this->config->password());
        }

        if ($authType === 'apikey') {
            return $client->withHeaders(['x-api-key' => $apiKey ?? '']);
        }

        return $client;
    }

    /**
     * Decode and validate JSON response.
     *
     * @param Response $response
     * @return array
     * @throws HyperpayPtspException
     */
    private function decodeResponse(Response $response): array
    {
        $decoded = $response->json();

        if (!is_array($decoded)) {
            throw new HyperpayPtspException('Invalid JSON response received from API.');
        }

        return $decoded;
    }

    /**
     * Build a user-friendly error message from an HTTP exception payload.
     *
     * @param RequestException $e
     * @return string
     */
    private function formatErrorMessage(RequestException $e): string
    {
        $response = $e->response;

        if (!$response instanceof Response) {
            return $e->getMessage();
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            return $e->getMessage();
        }

        $message = $this->extractMainMessage($payload, $e);
        $message = $this->appendFieldErrors($message, $payload);
        $message = $this->prependResponseCode($message, $payload);

        return $message;
    }

    /**
     * Extract the main error message from payload.
     *
     * @param array $payload
     * @param RequestException $e
     * @return string
     */
    private function extractMainMessage(array $payload, RequestException $e): string
    {
        if (!empty($payload['responseMessage']) && is_string($payload['responseMessage'])) {
            return $payload['responseMessage'];
        }

        if (!empty($payload['message']) && is_string($payload['message'])) {
            return $payload['message'];
        }

        return $e->getMessage();
    }

    /**
     * Append field-specific validation errors to message.
     *
     * @param string $message
     * @param array $payload
     * @return string
     */
    private function appendFieldErrors(string $message, array $payload): string
    {
        if (empty($payload['errors']) || !is_array($payload['errors'])) {
            return $message;
        }

        $errorLines = [];

        foreach ($payload['errors'] as $fieldErrors) {
            if (!is_array($fieldErrors)) {
                continue;
            }

            foreach ($fieldErrors as $err) {
                if (is_string($err)) {
                    $errorLines[] = $err;
                }
            }
        }

        if ($errorLines) {
            return $message . ' | ' . implode(' | ', $errorLines);
        }

        return $message;
    }

    /**
     * Prepend response code to message if available.
     *
     * @param string $message
     * @param array $payload
     * @return string
     */
    private function prependResponseCode(string $message, array $payload): string
    {
        if (!empty($payload['responseCode']) && is_string($payload['responseCode'])) {
            return '[' . $payload['responseCode'] . '] ' . $message;
        }

        return $message;
    }
}
