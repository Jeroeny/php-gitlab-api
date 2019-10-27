<?php

declare(strict_types=1);

namespace Gitlab\Api;

use Gitlab\Client;

interface Api
{
    public function __construct(Client $client);
}
