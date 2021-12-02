<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use VaclavVanik\Soap\Wsdl\CacheFileProvider;
use VaclavVanik\Soap\Wsdl\Exception\EmptyContent;
use VaclavVanik\Soap\Wsdl\Exception\Exception;
use VaclavVanik\Soap\Wsdl\Exception\File;
use VaclavVanik\Soap\Wsdl\Exception\Runtime;
use VaclavVanik\Soap\Wsdl\Exception\ValueError;
use VaclavVanik\Soap\Wsdl\WsdlResourceProvider;

use function md5;

final class CacheFileProviderTest extends TestCase
{
    use WsdlProphecy;

    /** @var vfsStreamDirectory */
    private $vfs;

    /** @var string */
    private $wsdlUrl = 'https://example.com';

    /** @var string */
    private $wsdlContent = '<root/>';

    public function setUp(): void
    {
        $this->vfs = vfsStream::setup();
    }

    public function testThrowValueErrorExceptionOnNegativeTtl(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('Ttl must be greater than or equal to 0');

        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProvider()->reveal();

        new CacheFileProvider($delegated, '', -1);
    }

    public function testThrowValueErrorExceptionOnNegativeTime(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('Time must be greater than or equal to 0');

        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProvider()->reveal();

        new CacheFileProvider($delegated, '', 0, -1);
    }

    public function testDoNotCreateCacheFileWhenTtlIsZero(): void
    {
        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProviderWithWsdl($this->wsdlUrl, $this->wsdlContent)->reveal();

        $cacheFileProvider = new CacheFileProvider($delegated, $this->vfs->url());

        $this->assertSame($this->wsdlContent, $cacheFileProvider->provide());
        $this->assertCount(0, $this->vfs->getChildren());
    }

    public function testCreateCacheFile(): void
    {
        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProviderWithWsdl($this->wsdlUrl, $this->wsdlContent)->reveal();

        $cacheFile = 'example.com-' . md5($this->wsdlUrl) . '.wsdl';

        $cacheFileProvider = new CacheFileProvider($delegated, $this->vfs->url(), 10);

        $this->assertSame($this->wsdlContent, $cacheFileProvider->provide());
        $this->assertTrue($this->vfs->hasChild($cacheFile));
    }

    public function testCreateCacheFileThrowFileException(): void
    {
        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProviderWithWsdl($this->wsdlUrl, $this->wsdlContent)->reveal();

        $cacheFile = 'example.com-' . md5($this->wsdlUrl) . '.wsdl';

        vfsStream::newFile($cacheFile, 0100)
            ->withContent($this->wsdlContent)
            ->lastModified(0)
            ->at($this->vfs);

        $this->expectException(File::class);

        (new CacheFileProvider($delegated, $this->vfs->url(), 10))->provide();
    }

    public function testReadFromCache(): void
    {
        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProviderWithWsdl($this->wsdlUrl, $this->wsdlContent)->reveal();

        $cacheFile = 'example.com-' . md5($this->wsdlUrl) . '.wsdl';

        $fileModTime = 1000;

        vfsStream::newFile($cacheFile, 0600)
            ->withContent($this->wsdlContent)
            ->lastModified($fileModTime)
            ->at($this->vfs);

        $cacheFileProvider = new CacheFileProvider($delegated, $this->vfs->url(), 1000, 999 + $fileModTime);

        $this->assertSame($this->wsdlContent, $cacheFileProvider->provide());
        $this->assertTrue($this->vfs->hasChild($cacheFile));
        $this->assertSame($fileModTime, $this->vfs->getChild($cacheFile)->filemtime());
    }

    public function testReadFromCacheThrowFileException(): void
    {
        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProviderWithWsdl($this->wsdlUrl, $this->wsdlContent)->reveal();

        $cacheFile = 'example.com-' . md5($this->wsdlUrl) . '.wsdl';

        $fileModTime = 1000;

        vfsStream::newFile($cacheFile, 0100)
            ->withContent($this->wsdlContent)
            ->lastModified($fileModTime)
            ->at($this->vfs);

        $this->expectException(File::class);

        (new CacheFileProvider($delegated, $this->vfs->url(), 1000, 999 + $fileModTime))->provide();
    }

    public function testProvideRethrowExceptionOnDelegatedCall(): void
    {
        /** @var Exception $exception */
        $exception = $this->prophesize(Exception::class)->reveal();

        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProviderProvideThrowsException($exception, 'res')->reveal();

        $this->expectException(Exception::class);

        (new CacheFileProvider($delegated, $this->vfs->url()))->provide();
    }

    public function testProvideRethrowsWsdlExceptionOnDelegatedCall(): void
    {
        $exception = new Runtime('delegated-exception-message');

        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProviderResourceThrowsException($exception)->reveal();

        $this->expectException(Runtime::class);
        $this->expectExceptionMessage($exception->getMessage());

        (new CacheFileProvider($delegated, $this->vfs->url()))->provide();
    }

    public function testProvideThrowsRuntimeExceptionOnDelegatedCall(): void
    {
        $exception = new RuntimeException('delegated-exception-message');

        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProviderProvideThrowsException($exception, 'res')->reveal();

        $this->expectException(Runtime::class);
        $this->expectExceptionMessage($exception->getMessage());

        (new CacheFileProvider($delegated, $this->vfs->url()))->provide();
    }

    public function testProvideThrowsRuntimeExceptionOnDelegatedEmptyResource(): void
    {
        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProviderWithWsdl('', '')->reveal();

        $this->expectException(Runtime::class);
        $this->expectExceptionMessage('WSDL delegated resource is empty');

        (new CacheFileProvider($delegated, $this->vfs->url()))->provide();
    }

    public function testProvideThrowsEmptyContentExceptionOnDelegatedEmptyProvide(): void
    {
        /** @var WsdlResourceProvider $delegated */
        $delegated = $this->prophesizeWsdlResourceProviderWithWsdl('res', '')->reveal();

        $this->expectException(EmptyContent::class);
        $this->expectExceptionMessage('WSDL (res) has empty content');

        (new CacheFileProvider($delegated, $this->vfs->url()))->provide();
    }
}
