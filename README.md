# TLS-Crypto-Random

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-8892BF.svg?style=flat-square)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

A cryptographically secure random number generator library for TLS protocol implementation, providing secure random bytes and integers generation using PHP's built-in cryptographic functions.

## Features

- **Cryptographically Secure**: Uses PHP's `random_bytes()` and `random_int()` functions
- **Simple Interface**: Clean API with comprehensive error handling
- **Exception Safety**: Proper exception handling for all edge cases
- **Type Safety**: Full PHP 8.1+ type declarations
- **Performance**: Efficient random number generation with minimal overhead

## Installation

```bash
composer require tourze/tls-crypto-random
```

## Requirements

- PHP 8.1 or higher
- OpenSSL extension (for secure random number generation)

## Usage

### Basic Usage

```php
use Tourze\TLSCryptoRandom\CryptoRandom;

$random = new CryptoRandom();

// Generate random bytes
$randomBytes = $random->getRandomBytes(32); // 32 bytes
echo bin2hex($randomBytes); // Output: e.g., "a1b2c3d4e5f6..."

// Generate random integer
$randomInt = $random->getRandomInt(1, 100); // Integer between 1 and 100
echo $randomInt; // Output: e.g., 42
```

### Interface Implementation

```php
use Tourze\TLSCryptoRandom\Contract\RandomInterface;
use Tourze\TLSCryptoRandom\CryptoRandom;

function useRandomGenerator(RandomInterface $random): void
{
    // Generate a session ID
    $sessionId = bin2hex($random->getRandomBytes(16));
    
    // Generate a random port number
    $port = $random->getRandomInt(1024, 65535);
    
    echo "Session ID: {$sessionId}\n";
    echo "Port: {$port}\n";
}

$random = new CryptoRandom();
useRandomGenerator($random);
```

### Error Handling

```php
use Tourze\TLSCryptoRandom\CryptoRandom;
use Tourze\TLSCryptoRandom\Exception\RandomException;

$random = new CryptoRandom();

try {
    // Invalid length
    $random->getRandomBytes(0);
} catch (RandomException $e) {
    echo "Error: " . $e->getMessage(); // "随机字节长度必须大于0"
}

try {
    // Invalid range
    $random->getRandomInt(100, 1);
} catch (RandomException $e) {
    echo "Error: " . $e->getMessage(); // "最小值不能大于最大值"
}
```

## API Reference

### CryptoRandom Class

Implements `RandomInterface` and provides cryptographically secure random number generation.

#### Methods

##### `getRandomBytes(int $length): string`

Generates cryptographically secure random bytes.

- **Parameters:**
  - `$length` (int): Number of bytes to generate (must be > 0)
- **Returns:** string - Random bytes
- **Throws:** `RandomException` - If length is invalid or generation fails

##### `getRandomInt(int $min, int $max): int`

Generates cryptographically secure random integer within specified range.

- **Parameters:**
  - `$min` (int): Minimum value (inclusive)
  - `$max` (int): Maximum value (inclusive)
- **Returns:** int - Random integer
- **Throws:** `RandomException` - If range is invalid or generation fails

### RandomInterface

Interface for random number generators.

```php
interface RandomInterface
{
    public function getRandomBytes(int $length): string;
    public function getRandomInt(int $min, int $max): int;
}
```

### Exceptions

#### RandomException

Thrown when random number generation fails or invalid parameters are provided.

#### CryptoException

Base exception class for cryptographic operations.

## Security Considerations

- Uses PHP's cryptographically secure `random_bytes()` and `random_int()` functions
- Suitable for generating session tokens, salts, and other security-sensitive values
- No fallback to insecure random number generators
- Proper error handling prevents silent failures

## Performance

- Efficient direct usage of PHP's built-in functions
- No additional entropy collection overhead
- Suitable for high-performance applications
- Tested with large data generation (100KB+ in under 1 second)

## Testing

The package includes comprehensive tests covering:

- Basic functionality
- Edge cases and error conditions
- Performance characteristics
- Cryptographic quality (basic entropy testing)
- Concurrent usage scenarios

Run tests with:

```bash
./vendor/bin/phpunit packages/tls-crypto-random/tests
```

## License

MIT License - see LICENSE file for details.
