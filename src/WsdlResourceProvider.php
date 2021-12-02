<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl;

interface WsdlResourceProvider extends WsdlProvider
{
    /**
     * Returns resource name for caching
     *
     * @throws Exception\Exception if any error occurs.
     */
    public function resource(): string;
}
