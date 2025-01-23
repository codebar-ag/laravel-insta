<?php

namespace CodebarAg\LaravelInstagram\Authenticator;

use DateTimeImmutable;
use Saloon\Contracts\OAuthAuthenticator;
use Saloon\Http\PendingRequest;

class InstagramAuthenticator implements OAuthAuthenticator
{
    public function __construct(
        public string $accessToken,
        public ?string $refreshToken = null,
        public ?DateTimeImmutable $expiresAt = null,
    ) {}

    /**
     * Apply the authentication to the request.
     */
    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->query()->add('access_token', $this->getAccessToken());
    }

    /**
     * Check if the access token has expired.
     */
    public function hasExpired(): bool
    {
        if (is_null($this->expiresAt)) {
            return false;
        }

        return $this->expiresAt->getTimestamp() <= (new DateTimeImmutable)->getTimestamp();
    }

    /**
     * Check if the access token has not expired.
     */
    public function hasNotExpired(): bool
    {
        return ! $this->hasExpired();
    }

    /**
     * Get the access token
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Get the refresh token
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * Get the expires at DateTime instance
     */
    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * Check if the authenticator is refreshable
     */
    public function isRefreshable(): bool
    {
        return isset($this->refreshToken);
    }

    /**
     * Check if the authenticator is not refreshable
     */
    public function isNotRefreshable(): bool
    {
        return ! $this->isRefreshable();
    }

    /**
     * Serialize the access token.
     */
    public function serialize(): string
    {
        return serialize($this);
    }

    /**
     * Unserialize the access token.
     */
    public static function unserialize(string $string): static
    {
        return unserialize($string, ['allowed_classes' => true]);
    }
}
