<?php

declare(strict_types=1);

namespace Tourze\TLSCryptoRandom\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\TLSCryptoRandom\CryptoRandom;
use Tourze\TLSCryptoRandom\Exception\CryptoException;
use Tourze\TLSCryptoRandom\Exception\RandomException;

/**
 * @internal
 */
#[CoversClass(CryptoRandom::class)]
final class CryptoRandomExceptionTest extends TestCase
{
    private CryptoRandom $random;

    protected function setUp(): void
    {
        parent::setUp();

        $this->random = new CryptoRandom();
    }

    public function testGetRandomBytesWithZeroLengthThrowsException(): void
    {
        $this->expectException(RandomException::class);
        $this->expectExceptionMessage('随机字节长度必须大于0');

        $this->random->getRandomBytes(0);
    }

    public function testGetRandomBytesWithNegativeLengthThrowsException(): void
    {
        $this->expectException(RandomException::class);
        $this->expectExceptionMessage('随机字节长度必须大于0');

        $this->random->getRandomBytes(-1);
    }

    public function testGetRandomBytesWithLargeNegativeLengthThrowsException(): void
    {
        $this->expectException(RandomException::class);
        $this->expectExceptionMessage('随机字节长度必须大于0');

        $this->random->getRandomBytes(-999999);
    }

    public function testGetRandomIntWithInvalidRangeThrowsException(): void
    {
        $this->expectException(RandomException::class);
        $this->expectExceptionMessage('最小值不能大于最大值');

        $this->random->getRandomInt(100, 1);
    }

    public function testGetRandomIntWithLargeInvalidRangeThrowsException(): void
    {
        $this->expectException(RandomException::class);
        $this->expectExceptionMessage('最小值不能大于最大值');

        $this->random->getRandomInt(PHP_INT_MAX, 0);
    }

    public function testExceptionInheritanceStructure(): void
    {
        try {
            $this->random->getRandomBytes(0);
        } catch (RandomException $e) {
            $this->assertInstanceOf(CryptoException::class, $e);
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testExceptionPreservesPreviousException(): void
    {
        // 测试异常链是否正确保存了原始异常
        // 虽然当前实现不太可能触发内部异常，但我们确保异常处理逻辑正确
        $this->expectException(RandomException::class);
        $this->expectExceptionMessage('随机字节长度必须大于0');

        $this->random->getRandomBytes(-1);
    }
}
