<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl;

use function file_get_contents;

final class FileProvider implements WsdlResourceProvider
{
    /** @var string */
    private $file;

    /** @throws Exception\ValueError */
    public function __construct(string $file)
    {
        if ($file === '') {
            throw new Exception\ValueError('File cannot be empty');
        }

        $this->file = $file;
    }

    public function provide(): string
    {
        try {
            $wsdl = ErrorHandler::run(function () {
                return file_get_contents($this->file);
            });
        } catch (Exception\Runtime $e) {
            throw Exception\File::fromThrowable($this->file, $e);
        }

        Utils::checkWsdl($wsdl, $this->file);

        return $wsdl;
    }

    public function resource(): string
    {
        return $this->file;
    }
}
