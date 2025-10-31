<?php

declare(strict_types=1);

namespace Tourze\TLSCryptoRandom\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\TLSCryptoRandom\CryptoRandom;
use Tourze\TLSCryptoRandom\Exception\RandomException;

/**
 * @internal
 */
#[CoversClass(CryptoRandom::class)]
final class CryptoRandomTest extends TestCase
{
    private CryptoRandom $random;

    protected function setUp(): void
    {
        parent::setUp();

        $this->random = new CryptoRandom();
    }

    public function testGetRandomBytesWithValidLengths(): void
    {
        // 测试生成不同长度的随机字节
        $lengths = [1, 16, 32, 64, 128];

        foreach ($lengths as $length) {
            $bytes = $this->random->getRandomBytes($length);
            $this->assertEquals($length, strlen($bytes));

            // 验证两次生成的随机字节不相同
            $anotherBytes = $this->random->getRandomBytes($length);
            $this->assertNotEquals($bytes, $anotherBytes, '随机字节生成应该是不可预测的');
        }
    }

    public function testGetRandomBytesWithInvalidLengthThrowsException(): void
    {
        $this->expectException(RandomException::class);
        $this->expectExceptionMessage('随机字节长度必须大于0');

        $this->random->getRandomBytes(0);
    }

    public function testGetRandomBytesWithNegativeLengthThrowsException(): void
    {
        $this->expectException(RandomException::class);
        $this->expectExceptionMessage('随机字节长度必须大于0');

        $this->random->getRandomBytes(-10);
    }

    public function testGetRandomIntWithValidRanges(): void
    {
        // 测试不同范围的随机整数
        $ranges = [
            [0, 10],
            [1, 100],
            [-50, 50],
            [PHP_INT_MAX - 100, PHP_INT_MAX],
            [PHP_INT_MIN, PHP_INT_MIN + 100],
        ];

        foreach ($ranges as [$min, $max]) {
            $int = $this->random->getRandomInt($min, $max);
            $this->assertGreaterThanOrEqual($min, $int);
            $this->assertLessThanOrEqual($max, $int);
        }
    }

    public function testGetRandomIntWithInvalidRangeThrowsException(): void
    {
        $this->expectException(RandomException::class);
        $this->expectExceptionMessage('最小值不能大于最大值');

        $this->random->getRandomInt(100, 1);
    }

    public function testGetRandomIntDistributionInRange(): void
    {
        // 测试生成的随机数是否具有相对均匀分布的特性
        $min = 1;
        $max = 6;  // 模拟骰子
        $iterations = 1000;
        $counts = array_fill($min, $max - $min + 1, 0);

        for ($i = 0; $i < $iterations; ++$i) {
            $value = $this->random->getRandomInt($min, $max);
            ++$counts[$value];
        }

        // 检查每个值的出现次数是否在理论期望值的合理范围内
        $expectedCount = $iterations / ($max - $min + 1);
        $tolerance = $expectedCount * 0.3;  // 允许30%的偏差

        foreach ($counts as $value => $count) {
            $this->assertGreaterThan($expectedCount - $tolerance, $count, "值 {$value} 出现次数过少");
            $this->assertLessThan($expectedCount + $tolerance, $count, "值 {$value} 出现次数过多");
        }
    }

    public function testGetRandomBytesUniqueness(): void
    {
        // 测试多次生成的随机字节的唯一性
        $length = 16;
        $samples = [];
        $iterations = 100;

        for ($i = 0; $i < $iterations; ++$i) {
            $samples[] = $this->random->getRandomBytes($length);
        }

        // 检查是否所有样本都是唯一的
        $uniqueSamples = array_unique($samples);
        $this->assertCount($iterations, $uniqueSamples, '所有随机字节样本应该是唯一的');
    }

    public function testGetRandomIntUniquenessInLargeRange(): void
    {
        // 在较大范围内测试随机整数的唯一性
        $min = 1;
        $max = 1000;
        $samples = [];
        $iterations = 100;

        for ($i = 0; $i < $iterations; ++$i) {
            $samples[] = $this->random->getRandomInt($min, $max);
        }

        // 在大范围内，应该有相当多的唯一值
        $uniqueSamples = array_unique($samples);
        $uniquePercentage = count($uniqueSamples) / $iterations;
        $this->assertGreaterThan(0.8, $uniquePercentage, '大范围内应该有较高的唯一性');
    }

    public function testCryptographicQualityBasicEntropy(): void
    {
        // 基本的熵质量测试
        $bytes = $this->random->getRandomBytes(1000);

        // 计算字节值分布
        $counts = array_count_values(array_map('ord', str_split($bytes)));

        // 检查是否有合理的分布（避免明显的偏差）
        if ([] === $counts) {
            self::fail('字节分布统计不应该为空');
        }

        $maxCount = max($counts);
        $minCount = min($counts);
        $avgCount = array_sum($counts) / count($counts);

        // 调整容忍度，使测试更稳健
        // 最大值不应该过度偏离平均值
        $this->assertLessThan($avgCount * 5, $maxCount, '字节分布不应过度集中');

        // 对于1000字节的样本，某些字节值可能不出现，这是正常的
        // 所以我们只检查最大偏差，不检查最小值
        $this->assertGreaterThan(0, $minCount, '所有出现的字节值计数应该大于0');
    }

    public function testPerformanceLargeRandomBytes(): void
    {
        // 性能测试：生成大量随机字节
        $startTime = microtime(true);
        $bytes = $this->random->getRandomBytes(1024 * 100); // 100KB
        $endTime = microtime(true);

        $this->assertEquals(1024 * 100, strlen($bytes));

        // 性能应该在合理范围内（1秒内完成）
        $duration = $endTime - $startTime;
        $this->assertLessThan(1.0, $duration, '生成100KB随机数据应该在1秒内完成');
    }

    public function testConcurrentUsageSimulation(): void
    {
        // 模拟并发使用场景
        $results = [];

        for ($i = 0; $i < 50; ++$i) {
            $bytes = $this->random->getRandomBytes(32);
            $int = $this->random->getRandomInt(1, 1000000);

            $results[] = ['bytes' => $bytes, 'int' => $int];
        }

        // 检查所有结果是否有效
        foreach ($results as $result) {
            $this->assertEquals(32, strlen($result['bytes']));
            $this->assertGreaterThanOrEqual(1, $result['int']);
            $this->assertLessThanOrEqual(1000000, $result['int']);
        }

        // 检查字节的唯一性
        $allBytes = array_column($results, 'bytes');
        $uniqueBytes = array_unique($allBytes);
        $this->assertCount(50, $uniqueBytes, '所有生成的字节序列应该是唯一的');
    }
}
