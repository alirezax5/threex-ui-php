<?php

declare(strict_types=1);

namespace ThreeXUI;

use ThreeXUI\Endpoints\Inbounds;
use ThreeXUI\Endpoints\Clients;
use ThreeXUI\Endpoints\ClientGroups;
use ThreeXUI\Endpoints\Server;
use ThreeXUI\Endpoints\Nodes;
use ThreeXUI\Endpoints\Settings;
use ThreeXUI\Endpoints\XrayConfig;
use ThreeXUI\Endpoints\CustomGeo;
use ThreeXUI\Endpoints\Subscriptions;
use ThreeXUI\Exceptions\ThreeXUIException;
use ThreeXUI\Helpers\RetryHandler;

/**
 * 3X-UI Panel API Client
 *
 * Main entry point for interacting with a 3X-UI panel via its REST API.
 * Supports both session-based (cookie) and token-based (Bearer) authentication.
 *
 * @method Inbounds      inbounds()
 * @method Clients       clients()
 * @method ClientGroups  clientGroups()
 * @method Server        server()
 * @method Nodes         nodes()
 * @method Settings      settings()
 * @method XrayConfig    xrayConfig()
 * @method CustomGeo     customGeo()
 * @method Subscriptions subscriptions()
 */
class ThreeXUI
{
    private Config $config;
    private HttpClient $http;
    private Authentication $auth;

    private Inbounds $inbounds;
    private Clients $clients;
    private ClientGroups $clientGroups;
    private Server $server;
    private Nodes $nodes;
    private Settings $settings;
    private XrayConfig $xrayConfig;
    private CustomGeo $customGeo;
    private Subscriptions $subscriptions;

    /**
     * Create a new 3X-UI client instance.
     *
     * @param string $panelUrl The base URL of the 3X-UI panel (e.g. https://panel.example.com:54321)
     */
    public function __construct(string $panelUrl)
    {
        $this->config = new Config($panelUrl);
        $this->http = new HttpClient($this->config);
        $this->auth = new Authentication($this->http, $this->config);

        $this->inbounds = new Inbounds($this->http);
        $this->clients = new Clients($this->http);
        $this->clientGroups = new ClientGroups($this->http);
        $this->server = new Server($this->http);
        $this->nodes = new Nodes($this->http);
        $this->settings = new Settings($this->http);
        $this->xrayConfig = new XrayConfig($this->http);
        $this->customGeo = new CustomGeo($this->http);
        $this->subscriptions = new Subscriptions($this->http);
    }

    /**
     * Authenticate using an API token from the panel settings.
     */
    public function withApiToken(string $token): self
    {
        $this->config->useApiToken($token);
        $this->http->setBearerToken($token);

        return $this;
    }

    /**
     * Authenticate using username and password (session-based login).
     *
     * @throws Exceptions\AuthenticationException
     */
    public function login(string $username, string $password): array
    {
        $this->config->useCredentials($username, $password);

        return $this->auth->login();
    }

    /**
     * Log out and clear the session.
     */
    public function logout(): array
    {
        return $this->auth->logout();
    }

    /**
     * Check if two-factor authentication is enabled on the panel.
     */
    public function isTwoFactorEnabled(): bool
    {
        return $this->auth->isTwoFactorEnabled();
    }

    /**
     * Set custom request timeout in seconds (default: 30).
     */
    public function setTimeout(int $seconds): self
    {
        $this->config->setTimeout($seconds);
        $this->http->setTimeout($seconds);

        return $this;
    }

    /**
     * Disable SSL certificate verification (use only in development).
     */
    public function withoutSslVerification(): self
    {
        $this->config->disableSslVerification();

        return $this;
    }

    /**
     * Enable automatic retry for transient failures (connection errors, 5xx, 429).
     *
     * @param int   $maxAttempts Maximum attempts (default: 3)
     * @param int   $baseDelayMs Base delay between retries in ms (default: 500)
     * @param float $multiplier  Exponential backoff multiplier (default: 2.0)
     * @param int   $maxDelayMs  Maximum delay cap in ms (default: 10000)
     */
    public function withRetry(
        int $maxAttempts = 3,
        int $baseDelayMs = 500,
        float $multiplier = 2.0,
        int $maxDelayMs = 10000
    ): self {
        $retry = new RetryHandler($maxAttempts, $baseDelayMs, $multiplier, $maxDelayMs);
        $this->http->setRetryHandler($retry);

        return $this;
    }

    /**
     * Access Inbound management endpoints.
     */
    public function inbounds(): Inbounds
    {
        return $this->inbounds;
    }

    /**
     * Access Client management endpoints.
     */
    public function clients(): Clients
    {
        return $this->clients;
    }

    /**
     * Access Client Group management endpoints.
     */
    public function clientGroups(): ClientGroups
    {
        return $this->clientGroups;
    }

    /**
     * Access Server management and monitoring endpoints.
     */
    public function server(): Server
    {
        return $this->server;
    }

    /**
     * Access Node management endpoints.
     */
    public function nodes(): Nodes
    {
        return $this->nodes;
    }

    /**
     * Access Panel settings endpoints.
     */
    public function settings(): Settings
    {
        return $this->settings;
    }

    /**
     * Access Xray configuration endpoints.
     */
    public function xrayConfig(): XrayConfig
    {
        return $this->xrayConfig;
    }

    /**
     * Access Custom Geo source endpoints.
     */
    public function customGeo(): CustomGeo
    {
        return $this->customGeo;
    }

    /**
     * Access subscription link endpoints.
     */
    public function subscriptions(): Subscriptions
    {
        return $this->subscriptions;
    }

    /**
     * Get the underlying HTTP client for custom requests.
     */
    public function getHttpClient(): HttpClient
    {
        return $this->http;
    }

    /**
     * Get the configuration object.
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
