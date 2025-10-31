# tls-crypto-random

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-8892BF.svg?style=flat-square)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

用于 TLS 协议实现的密码学安全随机数生成器库，使用 PHP 内置的密码学函数提供安全的随机字节和整数生成。

## 特性

- **密码学安全**: 使用 PHP 的 `random_bytes()` 和 `random_int()` 函数
- **简单接口**: 简洁的 API 并提供完整的错误处理
- **异常安全**: 对所有边界情况进行适当的异常处理
- **类型安全**: 完整的 PHP 8.1+ 类型声明
- **高性能**: 高效的随机数生成，最小化开销

## 安装

```bash
composer require tourze/tls-crypto-random
```

## 系统要求

- PHP 8.1 或更高版本
- OpenSSL 扩展（用于安全随机数生成）

## 使用方法

### 基本用法

```php
use Tourze\TLSCryptoRandom\CryptoRandom;

$random = new CryptoRandom();

// 生成随机字节
$randomBytes = $random->getRandomBytes(32); // 32 字节
echo bin2hex($randomBytes); // 输出: 例如 "a1b2c3d4e5f6..."

// 生成随机整数
$randomInt = $random->getRandomInt(1, 100); // 1到100之间的整数
echo $randomInt; // 输出: 例如 42
```

### 接口实现

```php
use Tourze\TLSCryptoRandom\Contract\RandomInterface;
use Tourze\TLSCryptoRandom\CryptoRandom;

function useRandomGenerator(RandomInterface $random): void
{
    // 生成会话ID
    $sessionId = bin2hex($random->getRandomBytes(16));
    
    // 生成随机端口号
    $port = $random->getRandomInt(1024, 65535);
    
    echo "会话ID: {$sessionId}\n";
    echo "端口: {$port}\n";
}

$random = new CryptoRandom();
useRandomGenerator($random);
```

### 错误处理

```php
use Tourze\TLSCryptoRandom\CryptoRandom;
use Tourze\TLSCryptoRandom\Exception\RandomException;

$random = new CryptoRandom();

try {
    // 无效长度
    $random->getRandomBytes(0);
} catch (RandomException $e) {
    echo "错误: " . $e->getMessage(); // "随机字节长度必须大于0"
}

try {
    // 无效范围
    $random->getRandomInt(100, 1);
} catch (RandomException $e) {
    echo "错误: " . $e->getMessage(); // "最小值不能大于最大值"
}
```

## API 参考

### CryptoRandom 类

实现 `RandomInterface` 接口，提供密码学安全的随机数生成。

#### 方法

##### `getRandomBytes(int $length): string`

生成密码学安全的随机字节。

- **参数:**
  - `$length` (int): 要生成的字节数（必须 > 0）
- **返回值:** string - 随机字节
- **抛出异常:** `RandomException` - 如果长度无效或生成失败

##### `getRandomInt(int $min, int $max): int`

在指定范围内生成密码学安全的随机整数。

- **参数:**
  - `$min` (int): 最小值（包含）
  - `$max` (int): 最大值（包含）
- **返回值:** int - 随机整数
- **抛出异常:** `RandomException` - 如果范围无效或生成失败

### RandomInterface 接口

随机数生成器接口。

```php
interface RandomInterface
{
    public function getRandomBytes(int $length): string;
    public function getRandomInt(int $min, int $max): int;
}
```

### 异常类

#### RandomException

当随机数生成失败或提供无效参数时抛出。

#### CryptoException

密码学操作的基础异常类。

## 安全考虑

- 使用 PHP 的密码学安全函数 `random_bytes()` 和 `random_int()`
- 适用于生成会话令牌、盐值和其他安全敏感值
- 不会回退到不安全的随机数生成器
- 适当的错误处理防止静默失败

## 性能

- 高效直接使用 PHP 内置函数
- 无额外的熵收集开销
- 适用于高性能应用程序
- 经过大数据生成测试（100KB+ 在1秒内完成）

## 测试

包含完整的测试覆盖：

- 基本功能测试
- 边界情况和错误条件
- 性能特性
- 密码学质量（基本熵测试）
- 并发使用场景

运行测试：

```bash
./vendor/bin/phpunit packages/tls-crypto-random/tests
```

## 许可证

MIT 许可证 - 详见 LICENSE 文件。
