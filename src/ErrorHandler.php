<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl;

use ErrorException;
use Throwable;

use function error_reporting;
use function restore_error_handler;
use function set_error_handler;

/** @internal */
abstract class ErrorHandler
{
    /**
     * @return mixed
     *
     * @throws Exception\Runtime
     */
    public static function run(callable $callback)
    {
        /** @throws ErrorException */
        $errorHandler = static function (int $no, string $str, string $file, int $line): bool {
            if (! (error_reporting() & $no)) {
                return false;
            }

            throw new ErrorException($str, 0, $no, $file, $line);
        };

        try {
            set_error_handler($errorHandler);

            return $callback();
        } catch (Throwable $e) {
            throw Exception\Runtime::fromThrowable($e);
        } finally {
            restore_error_handler();
        }
    }
}
