<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl;

use Throwable;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function md5;
use function preg_match;
use function time;

use const DIRECTORY_SEPARATOR;

final class CacheFileProvider implements WsdlProvider
{
    /** @var WsdlResourceProvider */
    private $delegated;

    /** @var string */
    private $directory;

    /** @var int */
    private $ttl;

    /** @var int */
    private $time;

    public function __construct(
        WsdlResourceProvider $delegated,
        string $directory = '',
        int $ttl = 0,
        int $time = 0
    ) {
        if ($ttl < 0) {
            throw new Exception\ValueError('Ttl must be greater than or equal to 0');
        }

        if ($time < 0) {
            throw new Exception\ValueError('Time must be greater than or equal to 0');
        }

        $this->delegated = $delegated;
        $this->directory = $directory;
        $this->ttl = $ttl;
        $this->time = $time ?: time();
    }

    public function provide(): string
    {
        $resource = $this->delegated->resource();

        if ($resource === '') {
            throw new Exception\Runtime('WSDL delegated resource is empty');
        }

        $file = $this->filename($this->directory, $resource);

        try {
            if ($this->ttl > 0 && $this->isCached($file, $this->ttl, $this->time)) {
                $wsdl = ErrorHandler::run(static function () use ($file) {
                    return file_get_contents($file);
                });

                Utils::checkWsdl($wsdl, $file);

                return $wsdl;
            }
        } catch (Exception\Runtime $e) {
            throw Exception\File::fromThrowable($file, $e);
        }

        try {
            $wsdl = $this->delegated->provide();
        } catch (Exception\Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw Exception\Runtime::fromThrowable($e);
        }

        Utils::checkWsdl($wsdl, $resource);

        try {
            if ($this->ttl > 0) {
                ErrorHandler::run(static function () use ($file, $wsdl) {
                    return file_put_contents($file, $wsdl);
                });
            }
        } catch (Exception\Runtime $e) {
            throw Exception\File::fromThrowable($file, $e);
        }

        return $wsdl;
    }

    private function isCached(string $file, int $ttl, int $time): bool
    {
        return ErrorHandler::run(static function () use ($file, $time, $ttl) {
            return file_exists($file) && (filemtime($file) > $time - $ttl);
        });
    }

    private function filename(string $directory, string $resource): string
    {
        $host = '';

        $m = [];

        if (preg_match('~https?://([\w.]+)/?~', $resource, $m) > 0) {
            $host = $m[1];
        }

        return $directory . DIRECTORY_SEPARATOR . $host . '-' . md5($resource) . '.wsdl';
    }
}
