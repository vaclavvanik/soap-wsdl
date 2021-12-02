<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ProphecyInterface;
use Throwable;
use VaclavVanik\Soap\Wsdl\WsdlProvider;
use VaclavVanik\Soap\Wsdl\WsdlResourceProvider;

trait WsdlProphecy
{
    use ProphecyTrait;

    private function prophesizeWsdlProvider(): ProphecyInterface
    {
        return $this->prophesize(WsdlProvider::class);
    }

    private function prophesizeWsdlProviderProvide(string $wsdl): ProphecyInterface
    {
        /** @var ProphecyInterface|WsdlProvider $provider */
        $provider = $this->prophesizeWsdlProvider();
        $provider->provide()->willReturn($wsdl);

        return $provider;
    }

    private function prophesizeWsdlProviderProvideThrowsException(Throwable $e): ProphecyInterface
    {
        /** @var ProphecyInterface|WsdlProvider $provider */
        $provider = $this->prophesizeWsdlProvider();
        $provider->provide()->willThrow($e);

        return $provider;
    }

    private function prophesizeWsdlResourceProvider(): ProphecyInterface
    {
        return $this->prophesize(WsdlResourceProvider::class);
    }

    private function prophesizeWsdlResourceProviderProvideThrowsException(
        Throwable $e,
        string $resource = ''
    ): ProphecyInterface {
        /** @var ProphecyInterface|WsdlResourceProvider $provider */
        $provider = $this->prophesizeWsdlResourceProvider();
        $provider->resource()->willReturn($resource);
        $provider->provide()->willThrow($e);

        return $provider;
    }

    private function prophesizeWsdlResourceProviderResourceThrowsException(Throwable $e): ProphecyInterface
    {
        /** @var ProphecyInterface|WsdlResourceProvider $provider */
        $provider = $this->prophesizeWsdlResourceProvider();
        $provider->resource()->willThrow($e);

        return $provider;
    }

    private function prophesizeWsdlResourceProviderWithWsdl(string $resource, string $wsdl): ProphecyInterface
    {
        /** @var ProphecyInterface|WsdlResourceProvider $provider */
        $provider = $this->prophesizeWsdlResourceProvider();
        $provider->resource()->willReturn($resource);
        $provider->provide()->willReturn($wsdl);

        return $provider;
    }
}
