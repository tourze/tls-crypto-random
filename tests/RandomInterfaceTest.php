<?php

declare(strict_types=1);

namespace Tourze\TLSCryptoRandom\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\TLSCryptoRandom\Contract\RandomInterface;
use Tourze\TLSCryptoRandom\CryptoRandom;

class RandomInterfaceTest extends TestCase
{
    private RandomInterface $random;

    protected function setUp(): void
    {
        parent::setUp();
        $this->random = new CryptoRandom();
    }

    public function test_implementsRandomInterface(): void
    {
        $this->assertInstanceOf(RandomInterface::class, $this->random);
    }

    public function test_getRandomBytes_methodSignature(): void
    {
        $reflection = new \ReflectionClass($this->random);
        $method = $reflection->getMethod('getRandomBytes');
        
        // 检查方法是否是公共的
        $this->assertTrue($method->isPublic());
        
        // 检查参数数量
        $this->assertEquals(1, $method->getNumberOfParameters());
        
        // 检查参数类型
        $parameter = $method->getParameters()[0];
        $this->assertEquals('length', $parameter->getName());
        $this->assertTrue($parameter->hasType());
        $this->assertEquals('int', $parameter->getType()->getName());
        
        // 检查返回类型
        $this->assertTrue($method->hasReturnType());
        $this->assertEquals('string', $method->getReturnType()->getName());
    }

    public function test_getRandomInt_methodSignature(): void
    {
        $reflection = new \ReflectionClass($this->random);
        $method = $reflection->getMethod('getRandomInt');
        
        // 检查方法是否是公共的
        $this->assertTrue($method->isPublic());
        
        // 检查参数数量
        $this->assertEquals(2, $method->getNumberOfParameters());
        
        // 检查参数类型
        $parameters = $method->getParameters();
        $this->assertEquals('min', $parameters[0]->getName());
        $this->assertTrue($parameters[0]->hasType());
        $this->assertEquals('int', $parameters[0]->getType()->getName());
        
        $this->assertEquals('max', $parameters[1]->getName());
        $this->assertTrue($parameters[1]->hasType());
        $this->assertEquals('int', $parameters[1]->getType()->getName());
        
        // 检查返回类型
        $this->assertTrue($method->hasReturnType());
        $this->assertEquals('int', $method->getReturnType()->getName());
    }

    public function test_getRandomBytes_returnsString(): void
    {
        $result = $this->random->getRandomBytes(10);
        $this->assertIsString($result);
    }

    public function test_getRandomInt_returnsInt(): void
    {
        $result = $this->random->getRandomInt(1, 10);
        $this->assertIsInt($result);
    }

    public function test_getRandomBytes_contractCompliance(): void
    {
        // 测试接口契约要求：生成指定长度的随机字节
        $lengths = [1, 8, 16, 32, 64, 128, 256];
        
        foreach ($lengths as $length) {
            $bytes = $this->random->getRandomBytes($length);
            $this->assertEquals($length, strlen($bytes), "应该生成 $length 字节的随机数据");
            $this->assertIsString($bytes, "应该返回字符串类型");
        }
    }

    public function test_getRandomInt_contractCompliance(): void
    {
        // 测试接口契约要求：生成指定范围内的随机整数
        $ranges = [
            [0, 10],
            [1, 100],
            [-50, 50],
            [100, 200],
            [-100, -10]
        ];
        
        foreach ($ranges as [$min, $max]) {
            $result = $this->random->getRandomInt($min, $max);
            $this->assertIsInt($result, "应该返回整数类型");
            $this->assertGreaterThanOrEqual($min, $result, "结果应该大于等于最小值 $min");
            $this->assertLessThanOrEqual($max, $result, "结果应该小于等于最大值 $max");
        }
    }

    public function test_interface_methods_exist(): void
    {
        $interfaceReflection = new \ReflectionClass(RandomInterface::class);
        $interfaceMethods = $interfaceReflection->getMethods();
        
        $this->assertCount(2, $interfaceMethods, "RandomInterface 应该定义2个方法");
        
        $methodNames = array_map(fn($method) => $method->getName(), $interfaceMethods);
        $this->assertContains('getRandomBytes', $methodNames);
        $this->assertContains('getRandomInt', $methodNames);
    }

    public function test_interface_method_documentation(): void
    {
        $interfaceReflection = new \ReflectionClass(RandomInterface::class);
        
        // 检查 getRandomBytes 方法文档
        $getRandomBytesMethod = $interfaceReflection->getMethod('getRandomBytes');
        $docComment = $getRandomBytesMethod->getDocComment();
        $this->assertNotFalse($docComment, "getRandomBytes 方法应该有文档注释");
        $this->assertStringContainsString('生成指定长度的随机字节', $docComment);
        
        // 检查 getRandomInt 方法文档
        $getRandomIntMethod = $interfaceReflection->getMethod('getRandomInt');
        $docComment = $getRandomIntMethod->getDocComment();
        $this->assertNotFalse($docComment, "getRandomInt 方法应该有文档注释");
        $this->assertStringContainsString('生成指定范围内的随机整数', $docComment);
    }

    public function test_interface_is_usable_as_type_hint(): void
    {
        // 测试接口可以作为类型提示使用
        $this->assertInstanceOf(RandomInterface::class, $this->random);
        
        // 测试可以通过接口类型调用方法
        $randomInterface = $this->random;
        $bytes = $randomInterface->getRandomBytes(10);
        $int = $randomInterface->getRandomInt(1, 10);
        
        $this->assertIsString($bytes);
        $this->assertIsInt($int);
    }
} 