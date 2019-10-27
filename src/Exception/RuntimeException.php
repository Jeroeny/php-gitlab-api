<?php

declare(strict_types=1);

namespace Gitlab\Exception;

use RuntimeException as PhpRuntimeException;

final class RuntimeException extends PhpRuntimeException implements Exception
{
}
