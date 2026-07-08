<?php

namespace Drivers\Messenger;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Drivers\DriverManager;
use Drivers\BaseController;
use Drivers\BotConversation;
use Drivers\Config;

/**
 * Webhook controller for the Facebook Messenger platform.
 *
 * Webhook events arrive as POST requests containing an `entry` array.
 * Each entry holds a `messaging` array with sender, recipient, and message.
 *
 * @see https://developers.facebook.com/docs/messenger-platform/webhooks
 */
class MessengerController extends BaseController
{
    protected function driverClass(): string
    {
        return MessengerDriver::class;
    }

    protected function botmanConfig(): array
    {
        return [
            'messenger' => [
                'token' => Config::get('MESSENGER_ACCESS_TOKEN'),
            ],
        ];
    }

    protected function isConfigured(): bool
    {
        return !empty(Config::get('MESSENGER_ACCESS_TOKEN'));
    }

    protected function getSenderId(): string
    {
        return (string) request('entry.0.messaging.0.sender.id');
    }

    protected function getRecipientId(): string
    {
        return (string) request('entry.0.messaging.0.recipient.id');
    }

    protected function isPostback(): bool
    {
        return (bool) request('entry.0.messaging.0.postback');
    }

    protected function getMessageText(): string
    {
        if ($this->isPostback()) {
            return (string) request('entry.0.messaging.0.postback.payload');
        }

        return (string) request('entry.0.messaging.0.message.text');
    }

    protected function registerHandlers(BotMan $botman): void
    {
        $botman->fallback(fn(BotMan $bot) => $bot->startConversation(new BotConversation));
    }
}
