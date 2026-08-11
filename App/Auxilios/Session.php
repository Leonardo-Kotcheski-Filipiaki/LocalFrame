<?php

namespace App\Auxilios;


/**
 * Session helper
 * 
 * @package App\Auxilios
 */
class Session
{
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function clear(): void
    {
        $_SESSION = [];
    }

    public static function oldFormData(): array
    {
        return $_SESSION['oldFormData'] ?? [];
    }

    public static function saveOldFormData(array $dados): void
    {
        $_SESSION['oldFormData'] = $dados;
    }
}