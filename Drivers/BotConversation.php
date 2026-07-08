<?php

namespace Drivers;

use BotMan\BotMan\Messages\Conversations\Conversation as BotmanConversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use Throwable;

class BotConversation extends BotmanConversation
{
    public function stopsConversation(IncomingMessage $message): bool
    {
        return in_array($message->getText(), ['get_started', 'get started', 'GET_STARTED']);
    }

    public function run()
    {
        try {
            // your flow here ...
            $this->handleAsk("What is your name?");
        } catch (\Throwable $th) {
            $message = sprintf(
                "[%s] %s in %s:%d\nStack trace:\n%s\n\n",
                date('Y-m-d H:i:s'),
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
                $th->getTraceAsString()
            );

            $logsDir = dirname(__DIR__) . '/logs';
            if (!is_dir($logsDir)) {
                mkdir($logsDir, 0755, true);
            }
            file_put_contents($logsDir . '/error.log', $message, FILE_APPEND);
        }
    }

    public function handleAsk(string|Question $message)
    {
        $this->ask(
            $message,
            fn(Answer $answer) => $this->followUp($answer),
        );
    }

    public function followUp(Answer $answer)
    {
        // your logic here ...

        $message = 'You said: ' . ($answer->getValue() ?: $answer->getText());
        $this->say($message);

        $this->handleAsk("Guess a number from 1 to 10:");
    }
}
