<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl;

interface WsdlProvider
{
    /**
     * Returns WSDL
     *
     * @throws Exception\EmptyContent if provided WSDL is empty.
     * @throws Exception\File if error occurs when accessing file.
     * @throws Exception\Exception if any other error occurs.
     */
    public function provide(): string;
}
