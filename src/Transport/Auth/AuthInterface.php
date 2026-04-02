<?php

namespace ANZ\BitUmc\SDK\Transport\Auth;

interface AuthInterface
{
    public function getLogin(): ?string;

    public function getPassword(): ?string;

    public function getApiKey(): ?string;
}
