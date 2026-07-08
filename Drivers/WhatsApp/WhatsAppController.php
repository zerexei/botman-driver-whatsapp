<?php

namespace Drivers\WhatsApp;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use Drivers\BaseController;
use Drivers\BotConversation;
use Drivers\Config;

/**
 * Webhook controller for the WhatsApp Cloud API.
 *
 * Handles both the one-time webhook verification handshake (GET with
 * hub.challenge) and live message events (POST with an `entry` array).
 *
 * @see https://developers.facebook.com/docs/whatsapp/cloud-api/webhooks
 */
class WhatsAppController extends BaseController
{
    /**
     * Respond to Meta's webhook verification handshake.
     *
     * Meta sends a GET request with hub.mode=subscribe, hub.challenge, and
     * hub.verify_token. We validate the token and echo back the challenge.
     */
    protected function handleVerification(Request $request): mixed
    {
        $challenge = $request->hub_challenge;

        if (!$challenge) {
            return null;
        }

        if ($request->hub_verify_token !== Config::get('WHATSAPP_VERIFY_TOKEN')) {
            return response()->json(['error' => 'Invalid verify token'], 403);
        }

        return $challenge;
    }

    protected function isRequestValid(): bool
    {
        return $this->isConfigured()
            && !empty(request('entry.0.id'))
            && !empty($this->getSenderId())
            && !empty($this->getRecipientId())
            && !empty($this->getMessageText());
    }

    protected function driverClass(): string
    {
        return WhatsAppDriver::class;
    }

    protected function botmanConfig(): array
    {
        return [
            'whatsApp' => [
                'token' => Config::get('WHATSAPP_ACCESS_TOKEN'),
            ],
        ];
    }

    protected function isConfigured(): bool
    {
        return !empty(Config::get('WHATSAPP_ACCESS_TOKEN'));
    }

    /** The user's phone number — the sender of the incoming message. */
    protected function getSenderId(): string
    {
        return (string) request('entry.0.changes.0.value.messages.0.from');
    }

    /** The business phone number ID — the bot's identity on WhatsApp. */
    protected function getRecipientId(): string
    {
        return (string) request('entry.0.changes.0.value.metadata.phone_number_id');
    }

    protected function getMessageText(): string
    {
        return (string) request('entry.0.changes.0.value.messages.0.text.body');
    }

    protected function registerHandlers(BotMan $botman): void
    {
        $botman->fallback(fn(BotMan $bot) => $bot->startConversation(new BotConversation));
    }
}
