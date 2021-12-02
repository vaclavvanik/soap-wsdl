<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl\Exception;

use LengthException;

use function sprintf;

final class EmptyContent extends LengthException implements Exception
{
    public static function fromEmpty(): self
    {
        return new self('WSDL has empty content');
    }

    public static function fromName(string $name): self
    {
        return new self(sprintf('WSDL (%s) has empty content', $name));
    }
}
