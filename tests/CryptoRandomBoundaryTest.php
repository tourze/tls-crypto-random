<?php

declare(strict_types=1);

namespace Tourze\TLSCryptoRandom\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\TLSCryptoRandom\CryptoRandom;

/**
 * @internal
 */
#[CoversClass(CryptoRandom::class)]
final class CryptoRandomBoundaryTest extends TestCase
{
    private CryptoRandom $random;

    protected function setUp(): void
    {
        parent::setUp();

        $this->random = new CryptoRandom();
    }

    public function testGetRandomBytesWithMinimumLength(): void
    {
        $bytes = $this->random->getRandomBytes(1);
        $this->assertEquals(1, strlen($bytes));
    }

    public function testGetRandomBytesWithLargeLength(): void
    {
        $length = 1024 * 1024; // 1MB
        $bytes = $this->random->getRandomBytes($length);
        $this->assertEquals($length, strlen($bytes));
    }

    public function testGetRandomBytesWithVeryLargeLength(): void
    {
        $length = 1024 * 1024 * 10; // 10MB - 测试大内存分配
        $bytes = $this->random->getRandomBytes($length);
        $this->assertEquals($length, strlen($bytes));
    }

    public function testGetRandomIntWithMinMaxIntBoundaries(): void
    {
        $result = $this->random->getRandomInt(PHP_INT_MIN, PHP_INT_MAX);
        $this->assertGreaterThanOrEqual(PHP_INT_MIN, $result);
        $this->assertLessThanOrEqual(PHP_INT_MAX, $result);
    }

    public function testGetRandomIntWithMaxIntRange(): void
    {
        $result = $this->random->getRandomInt(PHP_INT_MAX - 100, PHP_INT_MAX);
        $this->assertGreaterThanOrEqual(PHP_INT_MAX - 100, $result);
        $this->assertLessThanOrEqual(PHP_INT_MAX, $result);
    }

    public function testGetRandomIntWithMinIntRange(): void
    {
        $result = $this->random->getRandomInt(PHP_INT_MIN, PHP_INT_MIN + 100);
        $this->assertGreaterThanOrEqual(PHP_INT_MIN, $result);
        $this->assertLessThanOrEqual(PHP_INT_MIN + 100, $result);
    }

    public function testGetRandomIntWithSameMinMax(): void
    {
        $value = 42;
        $result = $this->random->getRandomInt($value, $value);
        $this->assertEquals($value, $result);
    }

    public function testGetRandomIntWithZeroRange(): void
    {
        $result = $this->random->getRandomInt(0, 0);
        $this->assertEquals(0, $result);
    }

    public function testGetRandomIntWithNegativeRange(): void
    {
        $result = $this->random->getRandomInt(-100, -1);
        $this->assertGreaterThanOrEqual(-100, $result);
        $this->assertLessThanOrEqual(-1, $result);
        $this->assertLessThan(0, $result);
    }

    public function testGetRandomBytesUniquenessWithSameLength(): void
    {
        $length = 32;
        $bytes1 = $this->random->getRandomBytes($length);
        $bytes2 = $this->random->getRandomBytes($length);

        // 虽然理论上可能相同，但对于32字节的随机数据，概率极低
        $this->assertNotEquals($bytes1, $bytes2, '32字节随机数据应该几乎总是不同');
    }

    public function testGetRandomIntDistributionInSmallRange(): void
    {
        $min = 1;
        $max = 3;
        $iterations = 300;
        $counts = array_fill($min, $max - $min + 1, 0);

        for ($i = 0; $i < $iterations; ++$i) {
            $value = $this->random->getRandomInt($min, $max);
            ++$counts[$value];
        }

        // 检查每个值都至少出现过一次
        foreach ($counts as $value => $count) {
            $this->assertGreaterThan(0, $count, "值 {$value} 应该至少出现一次");
        }

        // 检查分布相对均匀（允许较大偏差）
        $expectedCount = $iterations / ($max - $min + 1);
        $tolerance = $expectedCount * 0.5; // 允许50%偏差

        foreach ($counts as $count) {
            $this->assertGreaterThan($expectedCount - $tolerance, $count);
            $this->assertLessThan($expectedCount + $tolerance, $count);
        }
    }

    public function testGetRandomBytesWithMaxPhpStringLength(): void
    {
        // 测试接近 PHP 字符串长度限制的情况
        // 在64位系统上，PHP字符串理论上可以达到 2^63-1 字节
        // 但实际受内存限制，我们测试一个合理的大值
        $length = 1024 * 1024 * 16; // 16MB
        $bytes = $this->random->getRandomBytes($length);
        $this->assertEquals($length, strlen($bytes));
    }
}
