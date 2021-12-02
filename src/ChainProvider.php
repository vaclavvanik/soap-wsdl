<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl;

use Throwable;

use function array_reduce;
use function count;

final class ChainProvider implements WsdlProvider
{
    /** @var array<WsdlProvider> */
    private $providers;

    public function __construct(WsdlProvider ...$providers)
    {
        if (count($providers) === 0) {
            throw new Exception\ValueError('Chain cannot be empty');
        }

        $this->providers = $providers;
    }

    public function provide(): string
    {
        $wsdl = $this->wsdl();

        Utils::checkWsdl($wsdl);

        return $wsdl;
    }

    /** @throws Exception\Exception */
    private function wsdl(): string
    {
        try {
            /** @throws Throwable */
            $provide = static function (string $carry, WsdlProvider $provider): string {
                if ($carry !== '') {
                    return $carry;
                }

                try {
                    return $provider->provide();
                } catch (Exception\Exception $e) {
                    return '';
                }
            };

            return array_reduce($this->providers, $provide, '');
        } catch (Throwable $e) {
            throw Exception\Runtime::fromThrowable($e);
        }
    }
}
