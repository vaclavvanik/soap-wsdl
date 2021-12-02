<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl\Exception;

use PHPUnit\Framework\TestCase;
use VaclavVanik\Soap\Wsdl\Exception\EmptyContent;

final class EmptyContentTest extends TestCase
{
    public function testFromEmpty(): void
    {
        $exception = EmptyContent::fromEmpty();

        $this->assertSame('WSDL has empty content', $exception->getMessage());
    }

    public function testFromName(): void
    {
        $exception = EmptyContent::fromName('My');

        $this->assertSame('WSDL (My) has empty content', $exception->getMessage());
    }
}
