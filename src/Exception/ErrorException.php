<?php

declare(strict_types=1);

namespace Gitlab\Exception;

use ErrorException as PhpErrorException;

abstract class ErrorException extends PhpErrorException implements Exception
{
}
