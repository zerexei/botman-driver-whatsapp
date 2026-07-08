<div align="center">

# Zerexei BotMan Drivers

**Custom BotMan 2.x drivers for Messenger, WhatsApp Cloud API, and Viber**

<img src="https://img.shields.io/packagist/v/zerexei/botman-drivers.svg?style=flat-square" alt="Latest Version on Packagist" />
<img src="https://img.shields.io/packagist/dt/zerexei/botman-drivers.svg?style=flat-square" alt="Total Downloads" />
<img src="https://img.shields.io/badge/php-%3E%3D%208.2-777bb4.svg?style=flat-square" alt="PHP 8.2+" />
<img src="https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square" alt="MIT License" />
<img src="https://img.shields.io/badge/BotMan-2.x-00b96b?style=flat-square&logoColor=white" alt="BotMan 2.x">
<img src="https://img.shields.io/badge/PSR--4-compliant-brightgreen.svg?style=flat-square" alt="PSR-4 Compliant" />

</div>

---

## Overview

A collection of custom [BotMan](https://botman.io) drivers that connect your PHP chatbot to multiple messaging platforms. Each driver follows the BotMan `HttpDriver` contract and comes with typed message template builders.

Also includes a **local web playground** (frosted-glass UI + PHP dev server) so you can develop and test your bot without a tunnel or real webhook.

```
Drivers/
├── Messenger/          # Facebook Messenger Platform
│   └── MessageTemplates/  # ButtonTemplate, GenericTemplate, QuickReply, Image, Text
├── WhatsApp/           # WhatsApp Cloud API
│   └── MessageTemplates/  # ButtonTemplate (interactive list), Image, Text
├── Viber/              # Viber Bot API
│   └── MessageTemplates/  # ButtonTemplate, GenericTemplate, Image, Text
├── Web/                # Local playground (botman/driver-web)
├── Template/           # Scaffold — copy this to build your own driver
├── BaseController.php  # Abstract webhook controller base
├── BotConversation.php # Example conversation class
└── Config.php          # Thin getenv() wrapper
```

---

## Getting Started

### 1. Clone and install

```bash
git clone https://github.com/zerexei/botman-drivers.git
cd botman-drivers
composer install
```

### 2. Configure credentials

```bash
cp .env.example .env
```

Open `.env` and fill in the tokens for the platforms you use:

| Variable | Platform | Where to get it |
|---|---|---|
| `MESSENGER_ACCESS_TOKEN` | Messenger | Meta App → Messenger → Access Tokens |
| `WHATSAPP_ACCESS_TOKEN` | WhatsApp | Meta App → WhatsApp → API Setup |
| `WHATSAPP_VERIFY_TOKEN` | WhatsApp | A secret string you register in the Meta webhook dashboard |
| `VIBER_ACCESS_TOKEN` | Viber | [partners.viber.com](https://partners.viber.com/) |
| `VIBER_SENDER_NAME` | Viber | Display name shown to users (e.g. `Support Bot`) |
| `VIBER_SENDER_AVATAR` | Viber | Public URL of your bot's avatar (optional) |

### 3. Run the local playground

```bash
php -S localhost:8000
```

Open `http://localhost:8000`. The frosted-glass chat UI talks to the Web driver. No credentials or tunnel needed.

---

## Integration

### Embed in an existing PHP project

Copy the `Drivers/` folder into your project root and add the PSR-4 mapping to your `composer.json`:

```json
"autoload": {
    "psr-4": {
        "Drivers\\": "Drivers/"
    }
}
```

```bash
composer dump-autoload
```

### Register drivers

Call `DriverManager::loadDriver()` before creating the BotMan instance:

```php
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use Drivers\Messenger\MessengerDriver;
use Drivers\WhatsApp\WhatsAppDriver;
use Drivers\Viber\ViberDriver;

DriverManager::loadDriver(MessengerDriver::class);
DriverManager::loadDriver(WhatsAppDriver::class);
DriverManager::loadDriver(ViberDriver::class);

$botman = BotManFactory::create($config, new LaravelCache());
```

### Customize a controller

Each controller exposes a `registerHandlers(BotMan $bot)` method for your bot logic. Extend the provided controller instead of editing it directly so you keep upstream bug fixes:

```php
namespace App\Bot;

use BotMan\BotMan\BotMan;
use Drivers\WhatsApp\WhatsAppController as BaseController;
use App\Bot\Conversations\SupportConversation;

class WhatsAppController extends BaseController
{
    protected function registerHandlers(BotMan $botman): void
    {
        $botman->hears('hello', fn($bot) => $bot->reply('Hi! How can I help?'));
        $botman->hears('support', fn($bot) => $bot->startConversation(new SupportConversation));
        $botman->fallback(fn($bot) => $bot->reply("Sorry, I didn't understand that."));
    }
}
```

Then register your controller in `server.php`:

```php
'whatsApp' => App\Bot\WhatsAppController::class,
```

---

## Message Templates

Typed builder classes for platform-specific message formats — no raw arrays needed.

### Messenger

<details>
<summary>Button Template</summary>

```php
use Drivers\Messenger\MessageTemplates\ButtonTemplate;
use Drivers\Messenger\MessageTemplates\Button;

$bot->reply(
    ButtonTemplate::create('What would you like to do?')
        ->addButton(Button::create('Visit Docs', 'web_url',  'https://botman.io'))
        ->addButton(Button::create('Get Help',   'postback', 'HELP_PAYLOAD'))
);
```

</details>

<details>
<summary>Quick Replies</summary>

```php
use Drivers\Messenger\MessageTemplates\QuickReplyTemplate;
use Drivers\Messenger\MessageTemplates\QuickReplyButton;

$bot->reply(
    QuickReplyTemplate::create('Are you sure?')
        ->addButton(QuickReplyButton::create('Yes ✅', 'text', 'YES'))
        ->addButton(QuickReplyButton::create('No ❌',  'text', 'NO'))
);
```

</details>

<details>
<summary>Generic (Carousel) Template</summary>

```php
use Drivers\Messenger\MessageTemplates\GenericTemplate;
use Drivers\Messenger\MessageTemplates\Element;
use Drivers\Messenger\MessageTemplates\Button;

$bot->reply(
    GenericTemplate::create()
        ->addElement(
            Element::create('Product Name', 'Short description', 'https://example.com/img.jpg')
                ->addButton(Button::create('View', 'web_url', 'https://example.com'))
                ->addButton(Button::create('Buy',  'postback', 'BUY_123'))
        )
);
```

</details>

<details>
<summary>Image</summary>

```php
use Drivers\Messenger\MessageTemplates\ImageTemplate;

$bot->reply(ImageTemplate::create('https://example.com/photo.jpg'));
```

</details>

---

### WhatsApp

<details>
<summary>Text</summary>

```php
use Drivers\WhatsApp\MessageTemplates\TextTemplate;

$bot->reply(TextTemplate::create('Hello from WhatsApp! 👋'));
```

</details>

<details>
<summary>Image</summary>

```php
use Drivers\WhatsApp\MessageTemplates\ImageTemplate;

$bot->reply(ImageTemplate::create('https://example.com/photo.jpg'));
```

</details>

<details>
<summary>Interactive List (Button Template)</summary>

```php
use Drivers\WhatsApp\MessageTemplates\ButtonTemplate;
use Drivers\WhatsApp\MessageTemplates\Button;

// Second argument sets the list button label (default: 'Options')
$bot->reply(
    ButtonTemplate::create('What do you need help with?', 'Choose')
        ->addButton(Button::create('1', 'Technical Support'))
        ->addButton(Button::create('2', 'Billing'))
        ->addButton(Button::create('3', 'General Inquiry'))
);
```

</details>

---

### Viber

<details>
<summary>Text</summary>

```php
use Drivers\Viber\MessageTemplates\TextTemplate;

$bot->reply(TextTemplate::create('Hello from Viber! 👋'));
```

</details>

<details>
<summary>Image</summary>

```php
use Drivers\Viber\MessageTemplates\ImageTemplate;

$bot->reply(ImageTemplate::create('https://example.com/photo.jpg'));
```

</details>

<details>
<summary>Button Keyboard</summary>

```php
use Drivers\Viber\MessageTemplates\ButtonTemplate;
use Drivers\Viber\MessageTemplates\Button;

$bot->reply(
    ButtonTemplate::create('Pick an action:')
        ->addButton(Button::create('Say Hello'))
        ->addButton(Button::create('Visit Site', 'open-url', 'https://example.com'))
);
```

</details>

<details>
<summary>Rich Media (Carousel)</summary>

```php
use Drivers\Viber\MessageTemplates\GenericTemplate;
use Drivers\Viber\MessageTemplates\Element;
use Drivers\Viber\MessageTemplates\Button;

$bot->reply(
    GenericTemplate::create()
        ->addElement(
            Element::create('Card Title', 'Card subtitle', 'https://example.com/img.jpg')
                ->addButton(Button::create('Open', 'open-url', 'https://example.com'))
        )
);
```

</details>

---

## Building a New Driver

Copy `Drivers/Template/` and rename the files and namespace to match your platform:

```bash
cp -r Drivers/Template Drivers/MyPlatform
```

The scaffold includes three files:

| File | What to implement |
|---|---|
| `TemplateDriver.php` | `buildPayload`, `matchesRequest`, `getMessages`, `buildServicePayload`, `sendPayload`, `isConfigured` |
| `TemplateController.php` | Self-contained webhook entry point (intentionally does not extend `BaseController`) |
| `MessageTemplates/TextTemplate.php` | Starting point for message builders |

---

## Supported Platforms

| Platform | Driver | API Reference |
|---|---|---|
| 🌐 Web (playground) | `botman/driver-web` | — |
| 💬 Messenger | `Drivers\Messenger\MessengerDriver` | [Messenger Platform](https://developers.facebook.com/docs/messenger-platform) |
| 📱 WhatsApp | `Drivers\WhatsApp\WhatsAppDriver` | [WhatsApp Cloud API](https://developers.facebook.com/docs/whatsapp/cloud-api) |
| 📲 Viber | `Drivers\Viber\ViberDriver` | [Viber Bot API](https://developers.viber.com/docs/api/rest-bot-api) |

---

## Contributing

Contributions are welcome. See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## Security

To report a security vulnerability, contact:
📧 **Angelo Arcillas** — angeloarcillas64@gmail.com
