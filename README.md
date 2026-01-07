<p align="center">
  <img src=".github/assets/bahleel-banner.jpg" alt="Bahleel - PHP Web Scraping Framework" width="100%">
</p>

<p align="center">
  <a href="https://github.com/bahleel/bahleel/actions"><img src="https://github.com/bahleel/bahleel/workflows/Tests/badge.svg" alt="Tests"></a>
  <a href="https://github.com/bahleel/bahleel/actions"><img src="https://github.com/bahleel/bahleel/workflows/Code%20Style/badge.svg" alt="Code Style"></a>
  <a href="https://github.com/bahleel/bahleel/releases"><img src="https://img.shields.io/github/v/release/bahleel/bahleel" alt="Latest Version"></a>
  <a href="LICENSE"><img src="https://img.shields.io/github/license/bahleel/bahleel" alt="License"></a>
</p>

✅ Bahleel adalah **Framework PHP untuk menambang data dari internet**. Ingat tambang, ingat Bahleel.

> *Strike while the data is rich!* Built with [RoachPHP](https://roach-php.dev/) + [Laravel Zero](https://laravel-zero.com/)

---

## 💎 What Can You Mine?

✅ E-commerce products & prices  
✅ News articles & content  
✅ Real estate listings  
✅ Job postings  
✅ Social media data  
✅ Market research insights  
✅ **Anything on the web!**

## ⛏️ Mining Equipment (Features)

- 🎯 **Interactive Spider Generator** - Forge your scrapers with an intuitive wizard
- 💾 **Auto SQLite Storage** - All mined data stored safely in your vault
- 🔄 **Duplicate Detection** - Smart filtering keeps only the purest ore
- 📊 **Export Formats** - Ship your findings as CSV, JSON, or custom formats
- 🔌 **Middleware Support** - Proxy tunnels, JavaScript excavators, and more
- 📝 **Logging & Statistics** - Track every dig with detailed reports
- 🎨 **Template Generator** - Create custom tools for your mining operation
- 🧪 **27 Tests** - Every tool tested for reliability

## 🏭 Setting Up Your Mining Operation

### Requirements

- PHP 8.2 or higher
- Composer
- SQLite (sudah termasuk dalam PHP)

```bash
git clone https://github.com/bahleel/bahleel.git
cd bahleel
composer install
php bahleel migrate
```

## ⚡ Quick Start - Your First Excavation

### 1. Create Your First Spider (Mining Tool)

```bash
php bahleel make:spider
```

Interactive wizard akan memandu Anda:
- Spider name (your mining tool)
- Start URLs (where to dig)
- Concurrency & delay settings
- Middleware options (proxy, JavaScript, etc.)
- Field extraction (what to mine)

### 2. Start the Excavation

```bash
php bahleel run:spider MySpider
```

### 3. Lihat Data

```bash
php bahleel data:show MySpider
```

### 4. Export Data

```bash
php bahleel export:csv MySpider
```

## 🎯 Command Reference

### Mining Tools Management

```bash
# Create new spider (mining tool)
php bahleel make:spider

# List all available spiders
php bahleel spider:list

# Run spider to start mining
php bahleel run:spider {name}
```

```bash
# View mined data
php bahleel data:show {spider} --limit=10

# Clear data for a specific spider
php bahleel data:clear {spider}
```
```bash
# Ship findings as CSV
php bahleel export:csv {spider} --output=path/to/file.csv

# Create custom exporter
php bahleel make:exporter
```

### Tool Generators
### Generators

```bash
# Generate spider
php bahleel make:spider

# Generate middleware
php bahleel make:middleware

# Generate item processor
php bahleel make:processor

# Generate exporter
php bahleel make:exporter
```

## 📁 Project Structure

```
bahleel/
├── app/
│   ├── Commands/          # CLI commands (control center)
│   ├── Models/            # Database models (data vault)
│   ├── Services/          # Business logic (processing plant)
│   ├── ItemProcessors/    # Data processors (refineries)
│   └── Exporters/         # Export tools (shipping dept)
├── spiders/               # Your mining tools
├── middlewares/           # Request/response handlers
├── processors/            # Custom data processors
├── exporters/             # Custom exporters
├── config/
│   ├── bahleel.php        # Main configuration
│   └── database.php       # Data vault settings
├── database/
│   ├── migrations/        # Database schema
│   └── database.sqlite    # Your data vault
└── storage/
    └── exports/           # Exported findings
```

## ⚙️ Configuration

Edit `.env` untuk konfigurasi:

```env
# Auto save scraped data
BAHLEEL_AUTO_SAVE=true
BAHLEEL_DUPLICATE_FILTER=true

# Default spider settings
BAHLEEL_CONCURRENCY=2
BAHLEEL_REQUEST_DELAY=1

# Proxy settings
PROXY_ENABLED=false
PROXY_URL=http://proxy-server:port

# JavaScript execution (requires Puppeteer)
JS_ENABLED=false
CHROME_PATH=/path/to/chrome
NODE_PATH=/path/to/node
```

## 🔧 Advanced Usage

### Custom Middleware

Generate custom middleware untuk modify requests/responses:

```bash
php bahleel make:middleware MyMiddleware --type=request
```

### Custom Item Processor

Generate custom processor untuk data transformation:

```bash
php bahleel make:processor DataCleaner
```

### Custom Exporter

Generate custom exporter untuk format khusus:

```bash
php bahleel make:exporter HtmlExporter
```

## 📊 Example Spider

```php
<?php

namespace Spiders;

use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class ExampleSpider extends BasicSpider
{
    public array $startUrls = [
        'https://example.com',
    ];

    public array $itemProcessors = [
        \App\ItemProcessors\SqliteStorageProcessor::class,
    ];

    public function parse(Response $response): \Generator
    {
        $title = $response->filter('h1')->text();
        $content = $response->filter('.content')->text();

        yield $this->item([
            'title' => $title,
            'content' => $content,
            'url' => $response->getRequest()->getUri(),
        ]);
    }
}
```

## 🔍 Debugging Your Excavation

Enable verbose output to see every detail:

```bash
php bahleel run:spider MySpider -v
```

### Proxy Tunnels

Use proxies to access restricted areas:

```php
public array $downloaderMiddleware = [
    [\RoachPHP\Downloader\Middleware\ProxyMiddleware::class, [
        'proxy' => [
            'example.com' => 'http://proxy-server:port'
        ]
    ]],
];
```

### Custom Cookies

Untuk menggunakan custom cookies atau cookie file:

**Option 1: Set cookies langsung di spider**

```php
use RoachPHP\Http\Request;

protected function initialRequests(): iterable
{
    yield new Request(
        'GET',
        'https://example.com',
        options: [
            'cookies' => [
                'session_id' => 'your-session-value',
                'auth_token' => 'your-auth-token'
            ]
        ]
    );
}
```

**Option 2: Load cookies dari file**

```php
protected function initialRequests(): iterable
{
    $cookies = json_decode(file_get_contents('cookies.json'), true);
    
    yield new Request(
        'GET',
        'https://example.com',
        options: ['cookies' => $cookies]
    );
}
```

**Option 3: Custom Cookie Middleware**

Buat middleware untuk apply cookies ke semua requests:

```bash
php bahleel make:middleware CookieMiddleware --type=request
```

Kemudian edit middleware:

```php
public function handleRequest(Request $request): Request
{
    $cookies = [
        'session_id' => 'value',
        'user_token' => 'value'
    ];
    
    return $request->withOptions([
        'cookies' => $cookies
    ]);
}
```

Add ke spider:

```php
public array $downloaderMiddleware = [
    \Middlewares\CookieMiddleware::class,
];
```

### JavaScript Excavators

For sites requiring JavaScript execution:

```bash
npm install -g puppeteer
composer require spatie/browsershot
```

Tambah di spider:

```php
public array $downloaderMiddleware = [
    \RoachPHP\Downloader\Middleware\ExecuteJavascriptMiddleware::class,
];
```

## 🧪 Testing

Bahleel includes a comprehensive test suite using Pest PHP.

### Run All Tests

```bash
php bahleel test
```

### Run Specific Test Suites

```bash
# Run only Unit tests
php bahleel test --unit

# Run only Feature tests
php bahleel test --feature

# Filter tests by name
php bahleel test --filter=SpiderManager
```

### Test Coverage

- **Unit Tests**: Services, Models, Exporters, Template Generators
- **Feature Tests**: Command execution and integration

### Writing Tests

Create a new test:

```bash
php bahleel make:test MyFeatureTest
```

Example test:

```php
test('spider manager can check if spider exists', function () {
    $manager = new SpiderManager();
    expect($manager->exists('MySpider'))->toBeFalse();
});
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

MIT License

## 🙏 Credits

Bahleel is built on top of these amazing projects:

- **[RoachPHP](https://roach-php.dev/)** - The powerful PHP web scraping library
  - [Documentation](https://roach-php.dev/docs/installation)
  - [Spiders Guide](https://roach-php.dev/docs/spiders)
  - [Middleware Reference](https://roach-php.dev/docs/downloader-middleware)
  
- **[Laravel Zero](https://laravel-zero.com/)** - The micro-framework for console applications
  - [Documentation](https://laravel-zero.com/docs/introduction)
  - [Database Guide](https://laravel-zero.com/docs/database)
  - [Testing](https://laravel-zero.com/docs/testing)

Inspired by Python's [Scrapy](https://scrapy.org/)

## 📚 Further Reading

- [RoachPHP Item Pipeline](https://roach-php.dev/docs/item-pipeline) - Learn about data processing
- [RoachPHP Extensions](https://roach-php.dev/docs/extensions) - Extend spider functionality
- [Laravel Collections](https://laravel.com/docs/collections) - Powerful data manipulation
- [Laravel Eloquent](https://laravel.com/docs/eloquent) - Database ORM used in Bahleel

## 📮 Support

For issues and questions, please use the [GitHub issue tracker](https://github.com/bahleel/bahleel/issues).

---

Made with ❤️ by Bahleel Team

⛏️ **Happy Mining!** Strike while the data is rich.

Made with ❤️ by Bahleel Team - *Data Miners Since 2026*