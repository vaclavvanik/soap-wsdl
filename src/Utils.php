<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl;

use function base64_encode;

abstract class Utils
{
    /** @throws Exception\EmptyContent */
    public static function checkWsdl(string $wsdl, ?string $name = null): void
    {
        if ($wsdl === '') {
            if ($name) {
                throw Exception\EmptyContent::fromName($name);
            }

            throw Exception\EmptyContent::fromEmpty();
        }
    }

    /** @throws Exception\EmptyContent */
    public static function toDataUrl(string $wsdl): string
    {
        self::checkWsdl($wsdl);

        return 'data://text/plain;base64,' . base64_encode($wsdl);
    }
}
