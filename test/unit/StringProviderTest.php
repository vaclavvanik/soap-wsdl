<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl;

use PHPUnit\Framework\TestCase;
use VaclavVanik\Soap\Wsdl\Exception\EmptyContent;
use VaclavVanik\Soap\Wsdl\StringProvider;

final class StringProviderTest extends TestCase
{
    public function testProvide(): void
    {
        $wsdl = '<root/>';

        $provider = new StringProvider($wsdl);

        $this->assertSame($wsdl, $provider->provide());
    }

    public function testThrowEmptyContentExceptionIfEmptyData(): void
    {
        $this->expectException(EmptyContent::class);
        $this->expectExceptionMessage('WSDL has empty content');

        (new StringProvider(''))->provide();
    }

    public function testResource(): void
    {
        $wsdl = '<root/>';

        $provider = new StringProvider($wsdl);

        $this->assertSame($wsdl, $provider->resource());
    }
}
