<?php

declare(strict_types=1);

namespace Presta\SonataBookmarksBundle\Exception;

final class UnexpectedTypeException extends \InvalidArgumentException
{
    public function __construct(mixed $value, string $expectedType)
    {
        $givenType = \get_debug_type($value);

        parent::__construct("Expected argument of type \"$expectedType\", \"$givenType\" given.");
    }
}
