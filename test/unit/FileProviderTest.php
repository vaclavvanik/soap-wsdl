<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use VaclavVanik\Soap\Wsdl\Exception\EmptyContent;
use VaclavVanik\Soap\Wsdl\Exception\File;
use VaclavVanik\Soap\Wsdl\Exception\ValueError;
use VaclavVanik\Soap\Wsdl\FileProvider;

final class FileProviderTest extends TestCase
{
    /** @var vfsStreamDirectory */
    private $vfs;

    public function setUp(): void
    {
        $this->vfs = vfsStream::setup();
    }

    public function testProvide(): void
    {
        $file = 'my.wsdl';
        $content = '<root/>';

        vfsStream::newFile($file, 0600)
            ->withContent($content)
            ->at($this->vfs);

        $provider = new FileProvider($this->vfs->url() . '/' . $file);

        $this->assertSame($content, $provider->provide());
    }

    public function testThrowValueErrorExceptionIfFileNotSet(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('File cannot be empty');

        new FileProvider('');
    }

    public function testThrowRunTimeExceptionIfFileNotExists(): void
    {
        $this->expectException(File::class);

        $file = 'not-exists.wsdl';

        (new FileProvider($this->vfs->url() . '/' . $file))->provide();
    }

    public function testThrowEmptyContentExceptionIfEmptyData(): void
    {
        $this->expectException(EmptyContent::class);
        $this->expectExceptionMessageMatches('/WSDL \(.*\) has empty content/');

        $file = 'empty.wsdl';
        $content = '';

        vfsStream::newFile($file, 0600)
            ->withContent($content)
            ->at($this->vfs);

        (new FileProvider($this->vfs->url() . '/' . $file))->provide();
    }
}
