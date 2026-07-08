<?php

namespace Drivers\Viber;

use BotMan\BotMan\BotMan;
use Drivers\BaseController;
use Drivers\BotConversation;
use Drivers\Config;

/**
 * Webhook controller for the Viber Bot API.
 *
 * Viber sends all events (messages, subscriptions, unsubscribes) as POST
 * requests to your registered webhook URL. The URL path segment after the
 * token acts as the bot's identity (getSenderId).
 *
 * @see https://developers.viber.com/docs/api/rest-bot-api
 */
class ViberController extends BaseController
{
    protected function driverClass(): string
    {
        return ViberDriver::class;
    }

    protected function botmanConfig(): array
    {
        return [
            'viber' => [
                'token'  => Config::get('VIBER_ACCESS_TOKEN'),
                'name'   => Config::get('VIBER_SENDER_NAME', 'Bot'),
                'avatar' => Config::get('VIBER_SENDER_AVATAR', ''),
            ],
        ];
    }

    protected function isConfigured(): bool
    {
        return !empty(Config::get('VIBER_ACCESS_TOKEN'));
    }

    /** Derived from the webhook URL path — Viber uses this as the bot token. */
    protected function getSenderId(): string
    {
        $parts = explode('/', request()->getPathInfo());
        return (string) end($parts);
    }

    protected function getRecipientId(): string
    {
        return (string) request('sender.id');
    }

    protected function getMessageText(): string
    {
        return (string) request('message.text');
    }

    protected function registerHandlers(BotMan $botman): void
    {
        $botman->fallback(fn(BotMan $bot) => $bot->startConversation(new BotConversation));
    }
}
