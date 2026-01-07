<?php

namespace HyperBill\HyperpayPtsp;

class Config
{
    private const PRODUCTION_URL = 'https://ptsp.hyperpay.com';
    private const SANDBOX_URL = 'https://ptsp-stg.hyperpay.com';

    private string $baseUrl;
    private string $username;
    private string $password;
    private ?int $timeout;

    public function __construct(string $baseUrl, string $username, string $password, ?int $timeout = 15)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->username = $username;
        $this->password = $password;
        $this->timeout = $timeout;
    }

    /**
     * Build config from environment variables.
     *
     * Expected variables:
     * - PAYMENT_LINK_BASE_URL
     * - PAYMENT_LINK_BASIC_AUTH_USERNAME
     * - PAYMENT_LINK_BASIC_AUTH_PASSWORD
     * - PAYMENT_LINK_TIMEOUT (optional, seconds)
     */
    /**
     * Build config from Laravel config if available, otherwise env.
     */
    public static function fromLaravelConfig(): self
    {
        $environment = self::configValue('hyperpay-ptsp.environment') ?? 'sandbox';
        $baseUrl = self::getUrlForEnvironment($environment);

        $username = self::configValue('hyperpay-ptsp.basic_auth_username');
        $password = self::configValue('hyperpay-ptsp.basic_auth_password');
        $timeout = self::configValue('hyperpay-ptsp.timeout');

        if ($username === null || $password === null || empty($username) || empty($password)) {
            throw new \InvalidArgumentException(
                'Missing required config: PAYMENT_LINK_BASIC_AUTH_USERNAME, PAYMENT_LINK_BASIC_AUTH_PASSWORD'
            );
        }

        $timeoutInt = $timeout !== null ? (int) $timeout : 15;

        return new self($baseUrl, $username, $password, $timeoutInt);
    }

    /**
     * Get the API URL based on environment.
     */
    private static function getUrlForEnvironment(string $environment): string
    {
        return match(strtolower($environment)) {
            'sandbox', 'staging', 'stg' => self::SANDBOX_URL,
            'production', 'prod' => self::PRODUCTION_URL,
            default => self::PRODUCTION_URL,
        };
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function timeout(): ?int
    {
        return $this->timeout;
    }

    /**
     * Read an env value, compatible with Laravel's env() helper if available.
     */
    private static function envValue(string $key): ?string
    {
        if (function_exists('env')) {
            $value = env($key);
            if ($value !== null) {
                return is_string($value) ? $value : (string) $value;
            }
        }

        $value = getenv($key);
        if ($value === false) {
            return null;
        }

        return $value;
    }

    /**
     * Read a Laravel config value if available.
     */
    private static function configValue(string $key): ?string
    {
        if (function_exists('config')) {
            $value = config($key);
            if ($value !== null) {
                return is_string($value) ? $value : (string) $value;
            }
        }
        return null;
    }
}

