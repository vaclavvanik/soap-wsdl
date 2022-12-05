<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl;

use PHPUnit\Framework\TestCase;
use Throwable;
use VaclavVanik\Soap\Wsdl\ChainProvider;
use VaclavVanik\Soap\Wsdl\Exception\EmptyContent;
use VaclavVanik\Soap\Wsdl\Exception\Exception;
use VaclavVanik\Soap\Wsdl\Exception\Runtime;
use VaclavVanik\Soap\Wsdl\Exception\ValueError;
use VaclavVanik\Soap\Wsdl\WsdlProvider;

final class ChainProviderTest extends TestCase
{
    use WsdlProphecy;

    public function testEmptyChainThrowValueErrorException(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('Chain cannot be empty');

        new ChainProvider();
    }

    /** @return iterable<string, array{string, array<WsdlProvider>}> */
    public function provideProvide(): iterable
    {
        yield 'first' => [
            '<root/>',
            [
                $this->prophesizeWsdlProviderProvide('<root/>')->reveal(),
                $this->prophesizeWsdlProviderProvide('<root2/>')->reveal(),
            ],
        ];

        yield 'second' => [
            '<root/>',
            [
                $this->prophesizeWsdlProviderProvide('')->reveal(),
                $this->prophesizeWsdlProviderProvide('<root/>')->reveal(),
            ],
        ];
    }

    /**
     * @param array<int, WsdlProvider> $providers
     *
     * @dataProvider provideProvide
     */
    public function testProvide(string $wsdl, array $providers): void
    {
        $this->assertSame($wsdl, (new ChainProvider(...$providers))->provide());
    }

    public function testProvideSkipException(): void
    {
        /** @var Throwable $exception */
        $exception = $this->prophesize(Exception::class)->reveal();

        $wsdl = '<root/>';

        $providers = [
            $this->prophesizeWsdlProviderProvideThrowsException($exception)->reveal(),
            $this->prophesizeWsdlProviderProvide($wsdl)->reveal(),
        ];

        $this->assertSame($wsdl, (new ChainProvider(...$providers))->provide());
    }

    public function testProvideRethrowException(): void
    {
        /** @var Throwable $exception */
        $exception = $this->prophesize(Exception::class)->reveal();

        $this->expectException(Exception::class);

        /** @var WsdlProvider $wsdlProvider */
        $wsdlProvider = $this->prophesizeWsdlProviderProvideThrowsException($exception)->reveal();

        (new ChainProvider($wsdlProvider))->provide();
    }

    public function testProvideThrowRuntimeException(): void
    {
        /** @var Throwable $exception */
        $exception = $this->prophesize(Throwable::class)->reveal();

        $this->expectException(Runtime::class);

        /** @var WsdlProvider $wsdlProvider */
        $wsdlProvider = $this->prophesizeWsdlProviderProvideThrowsException($exception)->reveal();

        (new ChainProvider($wsdlProvider))->provide();
    }

    public function testProvideThrowEmptyContentExceptionIfEmptyData(): void
    {
        $this->expectException(EmptyContent::class);
        $this->expectExceptionMessage('WSDL has empty content');

        /** @var WsdlProvider $wsdlProvider */
        $wsdlProvider = $this->prophesizeWsdlProviderProvide('')->reveal();

        (new ChainProvider($wsdlProvider))->provide();
    }
}
