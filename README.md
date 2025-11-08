# RainYun PHP SDK

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue.svg)](https://php.net)

用于在 PHP 平台上轻松管理 RainYun.com 服务的 SDK。

## 特性

- ✅ 符合 PSR-4 自动加载标准
- ✅ 基于 PSR-7/17/18 HTTP 接口
- ✅ 流畅的链式调用接口
- ✅ 完整的类型提示支持
- ✅ 灵活的 HTTP 客户端支持（Guzzle、Symfony HttpClient 等）

## 系统要求

- PHP >= 7.4
- PSR-7 HTTP 消息实现
- PSR-17 HTTP 工厂实现
- PSR-18 HTTP 客户端实现

## 安装

使用 Composer 安装：

```bash
composer require skyhhjmk/rainyun-php-sdk
```

### 安装 HTTP 客户端实现

本 SDK 需要一个 PSR-18 兼容的 HTTP 客户端。推荐使用 Guzzle：

```bash
composer require guzzlehttp/guzzle:^7.0
composer require guzzlehttp/psr7:^2.6
```

或使用 Symfony HTTP Client：

```bash
composer require symfony/http-client
composer require nyholm/psr7
```

## 快速开始

### 基本用法

```php
<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use RainYun\Client;

// 初始化 HTTP 客户端和工厂
$http = new GuzzleClient();
$factory = new HttpFactory();

// 创建 RainYun 客户端
$client = new Client($http, $factory, $factory);

// 获取系统状态
$status = $client->pub()->status()->get();
echo "Status: " . $status->status . "\n";
```

### 使用 API 密钥进行认证

```php
// 从环境变量获取 API 密钥（推荐）
$apiKey = getenv('RAINYUN_API_KEY');

// 创建带认证的客户端
$client = new Client(
    $http,
    $factory,
    $factory,
    'https://api.v2.rainyun.com',
    $apiKey
);

// 获取用户信息
$userInfo = $client->user()->info()->get();

if ($userInfo->isSuccess()) {
    echo "User ID: " . $userInfo->data->ID . "\n";
    echo "User Name: " . $userInfo->data->Name . "\n";
    echo "Email: " . $userInfo->data->Email . "\n";
} else {
    echo "Error: " . $userInfo->getCode() . "\n";
}
```

## 支持的 API

### 公共 API（无需认证）

#### 系统状态
```php
$status = $client->pub()->status()->get();
```

#### 应用配置
```php
$config = $client->pub()->appConfig()->get();
```

#### 新闻/公告
```php
$news = $client->pub()->news()->get();

if ($news->isSuccess()) {
    foreach ($news->data as $item) {
        echo $item->Title . "\n";
        echo $item->TimeStamp . "\n";
        echo $item->URL . "\n\n";
    }
    
    // 按类型筛选
    $updates = $news->getByType('更新动态');
    $activities = $news->getByType('最新活动');
}
```

### 用户 API（需要认证）

#### 用户信息
```php
$userInfo = $client->user()->info()->get();

if ($userInfo->isSuccess()) {
    $user = $userInfo->getData();
    // 访问用户数据
    echo $user->Name;
}
```

### 产品 API（需要认证）

#### RCS（云服务器）
```php
// 获取所有 RCS 实例
$result = $client->product()->rcs()->get();

if ($result->isSuccess()) {
    echo "Total: " . $result->getTotalRecords() . "\n";
    
    // 遍历所有实例
    foreach ($result->getRecords() as $instance) {
        echo "ID: " . $instance->ID . "\n";
        echo "HostName: " . $instance->HostName . "\n";
        echo "Status: " . $instance->Status . "\n";
        echo "IPv4: " . $instance->MainIPv4 . "\n";
        echo "Region: " . $instance->Node->Region . "\n";
        echo "CPU: " . $instance->CPU . " cores\n";
        echo "Memory: " . $instance->Memory . " MB\n";
        echo "Disk: " . $instance->Disk . " GB\n";
    }
    
    // 按状态过滤
    $running = $result->getByStatus('running');
    echo "Running instances: " . count($running) . "\n";
    
    // 按区域过滤
    $hkInstances = $result->getByRegion('cn-hk4');
    echo "HK instances: " . count($hkInstances) . "\n";
    
    // 按 ID 获取
    $instance = $result->getById(220195);
    if ($instance) {
        echo "Found: " . $instance->HostName . "\n";
    }
}

// 使用选项参数
use RainYun\Endpoints\Product\RcsGetOptions;

$options = RcsGetOptions::make()
    ->isRgpu(false)
    ->options(['filter' => 'value']);

$result = $client->product()->rcs()->get($options);
```

## 高级用法

### 使用选项参数

```php
use RainYun\Endpoints\Pub\PubGetOptions;

$options = PubGetOptions::make()
    ->page(1)
    ->perPage(10)
    ->filter('status', 'active');

$result = $client->pub()->news()->get($options);
```

### 响应处理

所有 API 响应都返回 `Collection` 对象：

```php
$result = $client->pub()->news()->get();

// 检查请求是否成功
if ($result->isSuccess()) {
    // 使用魔术属性访问
    echo $result->code;
    echo $result->data;
    
    // 转换为数组
    $array = $result->toArray();
    
    // 转换为 JSON
    $json = (string) $result;
    
    // 迭代数据
    foreach ($result->data as $item) {
        // 处理每个项目
    }
    
    // 获取数量
    $count = $result->count();
}
```

### 自定义 HTTP 客户端

你可以配置自己的 HTTP 客户端：

```php
$http = new GuzzleClient([
    'timeout' => 10,
    'verify' => true, // 生产环境请启用 SSL 验证
    'headers' => [
        'User-Agent' => 'MyApp/1.0',
    ],
]);

$client = new Client($http, $factory, $factory);
```

## 环境变量配置

推荐使用 `.env` 文件管理敏感信息：

```env
RAINYUN_API_KEY=your_api_key_here
RAINYUN_BASE_URL=https://api.v2.rainyun.com
```

然后在代码中使用：

```php
// 使用 vlucas/phpdotenv 或类似库
$apiKey = $_ENV['RAINYUN_API_KEY'];
$baseUrl = $_ENV['RAINYUN_BASE_URL'];

$client = new Client($http, $factory, $factory, $baseUrl, $apiKey);
```

## 错误处理

```php
try {
    $result = $client->user()->info()->get();
    
    if (!$result->isSuccess()) {
        // 处理 API 错误
        $errorCode = $result->getCode();
        $errorMessage = $result->message ?? 'Unknown error';
        throw new RuntimeException("API Error: {$errorMessage} (Code: {$errorCode})");
    }
    
} catch (\Psr\Http\Client\ClientExceptionInterface $e) {
    // 处理 HTTP 客户端异常
    echo "HTTP Error: " . $e->getMessage();
} catch (\Exception $e) {
    // 处理其他异常
    echo "Error: " . $e->getMessage();
}
```

## 示例

更多示例请查看 `examples/` 目录：

- `examples/test_news.php` - 获取新闻公告
- `examples/test_user.php` - 用户信息获取
- `examples/test_rcs.php` - 获取 RCS 云服务器列表

运行示例：

```bash
php examples/test_news.php

# 需要 API 密钥
php examples/test_rcs.php
```

**注意**: 请先配置好 API 密钥后再运行需要认证的示例。

## 贡献

欢迎贡献！请遵循以下步骤：

1. Fork 本仓库
2. 创建特性分支 (`git checkout -b feature/amazing-feature`)
3. 提交更改 (`git commit -m 'Add some amazing feature'`)
4. 推送到分支 (`git push origin feature/amazing-feature`)
5. 开启 Pull Request

### 开发规范

- 遵循 PSR-12 编码标准
- 添加适当的 PHPDoc 注释
- 确保向后兼容性
- 更新相关文档

## 许可证

本项目采用 MIT 许可证 - 详见 [LICENSE](LICENSE) 文件。

## 作者

- **skyhhjmk** - [biliwind.com](https://www.biliwind.com)

## 支持

- 📧 Email: admin@biliwind.com
- 🐛 Issues: [GitHub Issues](https://github.com/skyhhjmk/rainyun-php-sdk/issues)

## 相关链接

- [RainYun 官网](https://www.rainyun.com)
- [RainYun API 文档](https://api.v2.rainyun.com/docs)

---

**注意**: 本 SDK 为非官方实现,使用前请确保了解 RainYun 的服务条款。
