<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl;

final class StringProvider implements WsdlResourceProvider
{
    /** @var string */
    private $wsdl;

    public function __construct(string $wsdl)
    {
        $this->wsdl = $wsdl;
    }

    public function provide(): string
    {
        Utils::checkWsdl($this->wsdl);

        return $this->wsdl;
    }

    public function resource(): string
    {
        return $this->wsdl;
    }
}
