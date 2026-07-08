<?php

namespace Drivers\Web;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\Drivers\Web\WebDriver;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Http\Request;

/**
 * Controller for the local web playground (botman/driver-web).
 *
 * This controller powers the frosted-glass chat UI served at index.php.
 * It does not require authentication because it is only meant to be run
 * on a local PHP development server, never exposed to the public internet.
 *
 * To add your own bot logic, register additional hears() callbacks below
 * or swap the fallback for a Conversation class.
 */
class WebController
{
    public function __invoke(Request $request): void
    {
        DriverManager::loadDriver(WebDriver::class);

        $botman = BotManFactory::create([]);

        // ── Demo responses ────────────────────────────────────────────────

        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply("Hey there 👋 How's your day going?");
        });

        $botman->hears('what is your name', function (BotMan $bot) {
            $bot->reply("I'm your friendly chat assistant 🤖");
        });

        $botman->hears('what time is it', function (BotMan $bot) {
            $bot->reply('⏰ The current time is ' . date('h:i A'));
        });

        $botman->hears('help', function (BotMan $bot) {
            $bot->reply('🧭 Sure! You can say things like "hello", "what time is it", or "tell me a joke."');
        });

        $botman->hears('tell me a joke', function (BotMan $bot) {
            $bot->reply("😂 Why don't programmers like nature? Too many bugs!");
        });

        $botman->fallback(function (BotMan $bot) {
            $bot->reply("😅 Sorry, I didn't quite get that. Could you try rephrasing?");
        });

        $botman->listen();
    }
}
