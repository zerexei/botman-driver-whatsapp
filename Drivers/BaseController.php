<?php

namespace Drivers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Http\Request;

/**
 * Abstract base for all webhook-based BotMan driver controllers.
 *
 * Handles the common boot sequence:
 *   1. Optional webhook verification handshake (override handleVerification)
 *   2. Request validation guard (isConfigured + sender/recipient + message)
 *   3. Driver registration and BotMan factory creation
 *   4. Bot logic registration (override registerHandlers)
 *   5. BotMan listen loop
 *
 * Concrete controllers only need to implement the small set of abstract
 * methods that are specific to their platform.
 *
 * NOTE: The Template driver scaffold in Drivers/Template/ intentionally does
 * not extend this class so that it remains a self-contained copy-paste
 * starting point for new drivers.
 */
abstract class BaseController
{
    /**
     * Handle the incoming webhook request.
     *
     * Called by server.php for every inbound request.
     */
    final public function __invoke(Request $request): mixed
    {
        try {
            // Allow drivers to handle platform-specific verification handshakes
            // (e.g. WhatsApp hub.challenge, Viber set_webhook confirmation).
            $verification = $this->handleVerification($request);
            if ($verification !== null) {
                return $verification;
            }

            if (!$this->isRequestValid()) {
                return response()->json();
            }

            DriverManager::loadDriver($this->driverClass());
            $botman = BotManFactory::create($this->botmanConfig(), new LaravelCache());

            $this->registerHandlers($botman);

            $botman->listen();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Handle platform-specific webhook verification.
     *
     * Return a non-null value to short-circuit the normal listen flow
     * (e.g. echo back a challenge token). Return null to continue.
     */
    protected function handleVerification(Request $request): mixed
    {
        return null;
    }

    /**
     * Determine whether the incoming request is valid enough to process.
     *
     * Subclasses may override this to add platform-specific checks
     * (e.g. WhatsApp requires a conversation/entry ID as well).
     */
    protected function isRequestValid(): bool
    {
        return $this->isConfigured()
            && !empty($this->getSenderId())
            && !empty($this->getRecipientId())
            && !empty($this->getMessageText());
    }

    // ──────────────────────────────────────────────────────────────────────
    // Abstract contract — implement in each concrete controller
    // ──────────────────────────────────────────────────────────────────────

    /** Fully-qualified class name of the BotMan driver to register. */
    abstract protected function driverClass(): string;

    /** BotMan configuration array passed to BotManFactory::create(). */
    abstract protected function botmanConfig(): array;

    /** True when all required credentials/config are present. */
    abstract protected function isConfigured(): bool;

    /** ID of the user who sent the incoming message. */
    abstract protected function getSenderId(): string;

    /** ID of the bot/business account that received the message. */
    abstract protected function getRecipientId(): string;

    /** Plain text of the incoming message. */
    abstract protected function getMessageText(): string;

    /**
     * Register BotMan hears/fallback handlers before listen() is called.
     *
     * Override to add your conversation logic, e.g.:
     *   $botman->fallback(fn(BotMan $bot) => $bot->startConversation(new MyConversation));
     */
    abstract protected function registerHandlers(BotMan $botman): void;
}
