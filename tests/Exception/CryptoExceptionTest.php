<?php

declare(strict_types=1);

namespace Tourze\TLSCryptoRandom\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\TLSCryptoRandom\Exception\CryptoException;

/**
 * @internal
 */
#[CoversClass(CryptoException::class)]
final class CryptoExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeCreated(): void
    {
        // 创建匿名具体子类用于测试抽象基类
        $exception = new class('Test message') extends CryptoException {};

        $this->assertInstanceOf(CryptoException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testCanBeCreatedWithCodeAndPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        // 创建匿名具体子类用于测试抽象基类
        $exception = new class('Test message', 123, $previous) extends CryptoException {};

        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(123, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
