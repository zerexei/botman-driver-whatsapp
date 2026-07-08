<?php

require __DIR__ . '/vendor/autoload.php';

// ── Load .env ─────────────────────────────────────────────────────────────────
// Simple .env loader for the standalone PHP playground.
// In a Laravel app this is handled by the framework; remove this block there.
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// ── Route request to the appropriate driver controller ────────────────────────
use Drivers\Messenger\MessengerController;
use Drivers\Viber\ViberController;
use Drivers\Web\WebController;
use Drivers\WhatsApp\WhatsAppController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

try {
    $request = Request::capture();

    $driver = $request->input('driver');

    $controllerClass = match ($driver) {
        'web'       => WebController::class,
        'messenger' => MessengerController::class,
        'whatsApp'  => WhatsAppController::class,
        'viber'     => ViberController::class,
        default     => null,
    };

    if ($controllerClass === null) {
        http_response_code(400);
        echo json_encode(['error' => "Unknown driver: '{$driver}'. Valid values: web, messenger, whatsApp, viber."]);
        exit;
    }

    $controller = new $controllerClass();
    $response = $controller($request);

    if ($response instanceof Response) {
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

    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
