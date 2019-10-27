<?php

declare(strict_types=1);

namespace Gitlab\Exception;

use Http\Client\Exception as HttpException;

interface Exception extends HttpException
{
}
