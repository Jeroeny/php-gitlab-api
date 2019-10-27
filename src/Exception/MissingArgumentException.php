<?php

declare(strict_types=1);

namespace Gitlab\Exception;

use InvalidArgumentException as PhpInvalidArgumentException;
use Throwable;
use function implode;
use function is_string;
use function sprintf;

final class MissingArgumentException extends PhpInvalidArgumentException
{
    /**
     * @param string|string[] $required
     */
    public function __construct($required, int $code = 0, ?Throwable $previous = null)
    {
        if (is_string($required)) {
            $required = [$required];
        }

        parent::__construct(sprintf('One or more of required ("%s") parameters is missing!', implode('", "', $required)), $code, $previous);
    }
}
