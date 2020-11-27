<?php

namespace App\Services;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @param array $values
     */
    public function set(array $values): void
    {
        $_SESSION = $values;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * @param string|null $key
     */
    public function clear(?string $key = null): void
    {
        if ($key === null) {
            session_destroy();
        }
        if ($key !== null && array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }
}


