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

        $path = $this->vfs->url() . '/' . $file;

        $provider = new FileProvider($path);

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
        $path = $this->vfs->url() . '/' . $file;

        (new FileProvider($path))->provide();
    }

    public function testThrowEmptyContentExceptionIfEmptyData(): void
    {
        $file = 'empty.wsdl';
        $content = '';

        vfsStream::newFile($file, 0600)
            ->withContent($content)
            ->at($this->vfs);

        $path = $this->vfs->url() . '/' . $file;

        $this->expectException(EmptyContent::class);
        $this->expectExceptionMessage('WSDL (' . $path . ') has empty content');

        (new FileProvider($path))->provide();
    }

    public function testResource(): void
    {
        $file = 'my.wsdl';
        $content = '<root/>';

        vfsStream::newFile($file, 0600)
            ->withContent($content)
            ->at($this->vfs);

        $path = $this->vfs->url() . '/' . $file;

        $provider = new FileProvider($path);

        $this->assertSame($path, $provider->resource());
    }
}
