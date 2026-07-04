# BotMan Drivers 🤖

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![BotMan](https://img.shields.io/badge/BotMan-Driver-brightgreen)](https://github.com/botman/botman)

A collection of custom and modernized integration drivers connecting [BotMan](https://github.com/botman/botman) to multiple messaging platforms. This project enables seamless hookups with minimum overhead.

---

## 🚀 Features

- 🤖 **Plug-and-play** local client and server playground
- 🔌 **Ready-to-use** platform drivers: **Messenger**, **WhatsApp**, **Viber**, and generic template base
- 🎨 **Frosted-Glass Client Interface** to test conversations locally
- 🔒 **Secure Response & Validation Logic** protecting webhook verifications and inputs

---

## 📦 Installation

To use these drivers in your BotMan application:

1. Clone or download the repository to your project path.
2. In your main `composer.json`, map the PSR-4 namespace autoload configuration:
   ```json
   "autoload": {
       "psr-4": {
           "Drivers\\": "Drivers/"
       }
   }
   ```
3. Run `composer install` (or `composer update`) to generate the autoload definitions.

---

## 🛠️ Usage & Integration

### Running the Web Client Playground

To spin up the playground web environment:
```bash
# Start the built-in PHP development server in the project root
php -S localhost:8000
```
Then navigate to `http://localhost:8000` in your web browser. You'll see the premium frosted-glass chat UI where you can interact with the bot!

### Registering Drivers in Code

Register any driver in your service provider or bootstrap entry file before instantiating BotMan:

```php
use BotMan\BotMan\Drivers\DriverManager;
use Drivers\WhatsApp\WhatsAppDriver;
use Drivers\Messenger\MessengerDriver;
use Drivers\Viber\ViberDriver;

// Load the desired drivers
DriverManager::loadDriver(WhatsAppDriver::class);
DriverManager::loadDriver(MessengerDriver::class);
DriverManager::loadDriver(ViberDriver::class);
```

---

## 💬 Currently Supported Drivers

- [x] **Web** (Built-in via `botman/driver-web`)
- [x] **Messenger**
- [x] **WhatsApp** (Cloud API version)
- [x] **Viber**

---

## 🤝 Contributing

Contributions are welcome! Please review the [CONTRIBUTING.md](CONTRIBUTING.md) guide to get started.

## 🔒 Security Vulnerabilities

If you discover a security vulnerability, please contact:
📧 **Angelo Arcillas** — angeloarcillas64@gmail.com
