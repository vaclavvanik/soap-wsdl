<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl;

interface WsdlResourceProvider extends WsdlProvider
{
    /** @throws Exception\Exception */
    public function resource(): string;
}
