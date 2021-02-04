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
     * Set subkey for array in sessions $_SESSION['firstKey']['subKey']
     * @param string $key
     * @param string $subKey
     * @param string $value
     */
    public function setSubKey(string $key, string $subKey, string $value): void
    {
        $concat = 'set' . $subKey;
        $_SESSION[$key]->$concat($value);
    }

    /**
     * Set one value
     * @param string $key
     * @param $values
     */
    public function set(string $key, $values): void
    {
        $_SESSION[$key] = $values;
    }

    /**
     * Get one value
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Get all array
     * @return array|null
     */
    public function getAll(): ?array
    {
        return $_SESSION ?? null;
    }

    /**
     * Clear one key or all depending parameters
     * @param string|null $key
     * @return $this
     */
    public function clear(?string $key = null): Session
    {
        if ($key === null) {
            session_destroy();
            $this->redirectUrl('/');
        }
        if ($key !== null && array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }

        return $this;
    }

    /**
     * redirect url
     * @param string $url
     */
    public function redirectUrl(string $url = '/'): void
    {
        if (!empty($url)) {
            header('Location: ' . $url);
            exit;
        }
    }

    /**
     * redirect if the user don't have admin role
     * @return $this
     */
    public function redirectIfNotAdmin(): Session
    {
        if (!$this->isAdmin()) {
            $this->redirectUrl('/dashboard');
        }

        return $this;
    }

    /**
     * redirect if the user is not logged in
     * @return $this
     */
    public function redirectIfNotAuth(): Session
    {
        if (!$this->isAuth()) {
            $this->redirectUrl('/login');
        }

        return $this;
    }

    /**
     * return if the user is admin
     * @return bool
     */
    public function isAdmin(): bool
    {
      return $this->get('user') instanceof \App\Entity\Admin;
    }

    /**
     *
     * return if the user is logged in
     * @return bool
     */
    public function isAuth(): bool
    {
        return $this->get('user') !== null;
    }
}
