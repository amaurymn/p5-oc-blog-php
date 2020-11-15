<?php

namespace App\Services;

class FlashBag
{
    const        SESSION_KEY = 'flash';
    public const INFO        = 'info';
    public const WARNING     = 'warning';
    public const ERROR       = 'error';
    public const SUCCESS     = 'success';

    private $flash;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION[self::SESSION_KEY])) {
            $this->flash = $_SESSION[self::SESSION_KEY];
        }
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        $flashMsg = $this->flash;

        $this->clear();

        return $flashMsg;
    }

    /**
     * @param string|null $type
     * @return $this
     */
    public function clear(?string $type = null)
    {
        if ($type === null) {
            unset($this->flash);
        }

        if ($type !== null && array_key_exists($type, $this->flash)) {
            unset($this->flash[$type]);
        }

        $this->saveToSession();

        return $this;
    }

    private function saveToSession(): void
    {
        $_SESSION[self::SESSION_KEY] = $this->flash;
    }

    /**
     * @param string $type
     * @return mixed|null
     */
    public function get(string $type)
    {
        if (!isset($this->flash[$type])) {
            return null;
        }

        $flashMsg = $this->flash[$type];

        $this->clear($type);

        return $flashMsg;
    }

    /**
     * @param string $type
     * @param string $message
     * @return $this
     */
    public function set(string $type, string $message)
    {
        $this->flash[$type][] = $message;

        $this->saveToSession();

        return $this;
    }
}
