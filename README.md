
# ToolsBundle 📊

> A Symfony bundle compatible with PHP 8.2+ and symfony 6+

## 🚀 Installation

**Configure `composer.json`**  
   Add this repository to the `repositories` section of your `composer.json` file:

   ```json
   "repositories": [
       {
           "type": "vcs",
           "url": "https://github.com/tax16/ToolsBundle.git"
       }
   ]
   ```
**Add the bundle via Composer**  
   Run the following command in your terminal:

   ```bash
   composer require tax16/tools-bundle:^1.0
   ```

## ⚙️ Features

- **Retry**:
  - Retry logic with customizable delay
  - Configurable max attempts
  - Easy integration with Symfony or standalone PHP

## ⚙️ How It Works — Retry via Dynamic Proxy

This bundle uses [`ocramius/proxy-manager`](https://github.com/Ocramius/ProxyManager) to dynamically intercept method calls and apply **automatic retry logic** based on PHP attributes.

### 🧠 Behind the Scenes

A dedicated class, `RetryProxyFactory`, creates a **dynamic proxy** around any service. This proxy:

- Intercepts **public methods annotated** with the `#[Retry]` attribute
- Retries the execution when an exception is thrown
- Logs each failed attempt using `Psr\Log\LoggerInterface`
- Honors the parameters `attempts` (number of retries) and `delay` (delay between attempts in seconds)

This behavior is completely transparent to your application code.

### 🔁 Example: Using the `#[Retry]` Attribute

```php
use Tax16\ToolsBundle\Core\Domain\Retry\Attribut\Retry;

class ExternalApiClient
{
    #[Retry(attempts: 3, delay: 1)]
    public function fetchData(): void
    {
        // This method will be retried up to 3 times with a 1s delay on failure
    }
}
```

## 🤝 Contributing

> The application is designed in hexagonal architecture:

![Network design](doc/img/hexagonal.png)

> To contribute to the SystemCheckBundle, follow these steps:

1. **Clone the repository**:
   ```bash
   git clone https://github.com/tax16/ToolsBundle
   ```

2. **Install dependencies**:
   ```bash
   make install
   ```

3. **Run GrumPHP for code quality checks**:
   ```bash
   make grumphp
   ```

4. **Run tests**:
   ```bash
   make phpunit
   ```

Happy coding! 🎉
