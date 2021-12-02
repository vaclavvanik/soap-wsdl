<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl\Exception;

use RuntimeException;
use Throwable;

class Runtime extends RuntimeException implements Exception
{
    public static function fromThrowable(Throwable $e): self
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}
