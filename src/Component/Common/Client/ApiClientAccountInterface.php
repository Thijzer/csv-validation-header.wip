<?php

namespace Misery\Component\Common\Client;

interface ApiClientAccountInterface
{
    public function authorize(ApiClient $client): AuthenticatedAccount;
    public function refresh(ApiClient $client, AuthenticatedAccount $account): AuthenticatedAccount;
}