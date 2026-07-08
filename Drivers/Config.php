<?php

namespace Drivers;

/**
 * Minimal configuration helper for the standalone PHP playground.
 *
 * Reads values from environment variables (set by the system, a CI runner,
 * or the .env loader in server.php). In a Laravel application you would
 * replace calls to Config::get() with config() or env() instead.
 */
final class Config
{
    /**
     * Retrieve an environment variable value.
     *
     * @param  string  $key      The environment variable name (e.g. 'MESSENGER_ACCESS_TOKEN').
     * @param  mixed   $default  Returned when the variable is not set.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);

        return $value !== false ? $value : $default;
    }

    /**
     * Retrieve a required environment variable.
     *
     * Throws a RuntimeException with a clear message when the variable is
     * missing, preventing silent failures at runtime.
     *
     * @throws \RuntimeException
     */
    public static function require(string $key): string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            throw new \RuntimeException(
                "Required environment variable '{$key}' is not set. " .
                "Add it to your .env file (see .env.example)."
            );
        }

        return $value;
    }
}
