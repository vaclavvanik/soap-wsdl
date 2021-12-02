<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use VaclavVanik\Soap\Wsdl\Exception\File;

final class FileTest extends TestCase
{
    public function testFromThrowable(): void
    {
        $throwable = new Exception('Error message');

        $exception = File::fromThrowable('My', $throwable);

        $this->assertSame('My', $exception->getFilename());
        $this->assertSame($throwable->getMessage(), $exception->getMessage());
        $this->assertSame($throwable->getCode(), $exception->getCode());
        $this->assertSame($throwable, $exception->getPrevious());
    }
}
