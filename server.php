<?php

require __DIR__ . '/vendor/autoload.php';

use Drivers\Messenger\MessengerController;
use Illuminate\Http\Request;
use Drivers\Web\WebController;
use Drivers\Viber\ViberController;
use Drivers\WhatsApp\WhatsAppController;

try {
    // Create a Request instance
    $request = Request::capture();

    $driver = $request->input('driver');

    $controller = match ($driver) {
        'web' => WebController::class,
        'messenger' => MessengerController::class,
        'whatsApp' => WhatsAppController::class,
        'viber' => ViberController::class,
    };

    $instance = new $controller();

    $response = $instance($request);

    if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
        $response->send();
    } elseif (is_string($response) || is_numeric($response)) {
        echo $response;
    }
} catch (\Throwable $th) {
    $message = sprintf(
        "[%s] %s in %s:%d\nStack trace:\n%s\n\n",
        date('Y-m-d H:i:s'),
        $th->getMessage(),
        $th->getFile(),
        $th->getLine(),
        $th->getTraceAsString()
    );

    $logsDir = __DIR__ . '/logs';
    if (!is_dir($logsDir)) {
        mkdir($logsDir, 0755, true);
    }
    file_put_contents($logsDir . '/error.log', $message, FILE_APPEND);
}
