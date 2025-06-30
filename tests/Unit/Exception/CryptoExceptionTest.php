<?php

declare(strict_types=1);

namespace Tourze\TLSCryptoRandom\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use Tourze\TLSCryptoRandom\Exception\CryptoException;

/**
 * @covers \Tourze\TLSCryptoRandom\Exception\CryptoException
 */
final class CryptoExceptionTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $exception = new CryptoException('Test message');
        
        $this->assertInstanceOf(CryptoException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testCanBeCreatedWithCodeAndPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new CryptoException('Test message', 123, $previous);
        
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(123, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}