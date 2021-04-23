<?php

namespace Misery\Component\Common\Client;

class AuthenticatedAccount
{
    private $username;
    /** @var string|null */
    private $refreshToken;
    private $accessToken;

    public function __construct(
        string $username,
        string $accessToken = null,
        string $refreshToken = null
    ) {
        $this->username = $username;
        $this->refreshToken = $refreshToken;
        $this->accessToken = $accessToken;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function useToken(ApiClient $client): void
    {
        if ($this->accessToken) {
            $client->setHeaders([
                'Authorization: Bearer '. $this->accessToken,
            ]);
        }
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }
}