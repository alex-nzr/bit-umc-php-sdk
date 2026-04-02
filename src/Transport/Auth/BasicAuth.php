<?php

namespace ANZ\BitUmc\SDK\Transport\Auth;

final class BasicAuth implements AuthInterface
{
    public function __construct(
        private readonly string $login,
        private readonly string $password,
        private readonly ?string $apiKey = null,
    ) {
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }
}
