<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl\Exception;

use RuntimeException;
use Throwable;

final class File extends RuntimeException implements Exception
{
    /** @var string */
    private $filename;

    private function __construct(
        string $filename,
        string $message,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->filename = $filename;

        parent::__construct($message, $code, $previous);
    }

    public static function fromThrowable(string $filename, Throwable $e): self
    {
        return new self($filename, $e->getMessage(), $e->getCode(), $e);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
