<?php

declare(strict_types=1);

namespace Tourze\TLSCryptoRandom\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use Tourze\TLSCryptoRandom\Exception\CryptoException;
use Tourze\TLSCryptoRandom\Exception\RandomException;

/**
 * @covers \Tourze\TLSCryptoRandom\Exception\RandomException
 */
final class RandomExceptionTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $exception = new RandomException('Random error');
        
        $this->assertInstanceOf(RandomException::class, $exception);
        $this->assertInstanceOf(CryptoException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Random error', $exception->getMessage());
    }

    public function testCanBeCreatedWithCodeAndPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new RandomException('Random error', 456, $previous);
        
        $this->assertEquals('Random error', $exception->getMessage());
        $this->assertEquals(456, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testInheritanceHierarchy(): void
    {
        $exception = new RandomException('Test');
        
        // 验证继承层次结构
        $this->assertInstanceOf(CryptoException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}