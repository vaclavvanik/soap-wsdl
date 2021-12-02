<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl;

use PHPUnit\Framework\TestCase;
use VaclavVanik\Soap\Wsdl\Exception\EmptyContent;
use VaclavVanik\Soap\Wsdl\Utils;

final class UtilsTest extends TestCase
{
    public function testToDataUrl(): void
    {
        $wsdl = '<root/>';
        $dataUrl = 'data://text/plain;base64,PHJvb3QvPg==';

        $this->assertSame($dataUrl, Utils::toDataUrl($wsdl));
    }

    public function testThrowEmptyContentExceptionIfEmptyWsdl(): void
    {
        $this->expectException(EmptyContent::class);
        $this->expectExceptionMessage('WSDL has empty content');

        Utils::toDataUrl('');
    }
}
