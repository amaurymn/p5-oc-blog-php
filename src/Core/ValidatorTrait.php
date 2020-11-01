<?php

namespace App\Core;

trait ValidatorTrait
{

    /**
     * @param string|null $message
     * @return $this
     */
    public function required(?string $message = null)
    {
        $value = $this->values[$this->input];

        if (empty($value)) {
            $message = empty($message) ? "[FIELD] ne doit pas être vide." : $message;
            return $this->addError($message);
        }

        return $this;
    }

    /**
     * @param string|null $message
     * @return $this
     */
    public function email(?string $message = null)
    {
        $value = $this->values[$this->input];

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $message = empty($message) ? "[FIELD] doit être valide." : $message;
            $this->addError($message);
        }

        return $this;
    }

    /**
     * @param int $length
     * @param string|null $message
     * @return $this
     */
    public function length(int $length = 255, ?string $message = null)
    {
        $value = $this->values[$this->input];

        if ((!empty($value) && strlen($value) < $length) || (!empty($value) && strlen($value) > $length)) {
            $message = (empty($message)) ? "[FIELD] doit contenir {$length} caractères." : $message;
            $this->addError($message);
        }

        return $this;
    }

    /**
     * @param int $length
     * @param string|null $message
     * @return $this
     */
    public function minLength(int $length = 255, ?string $message = null)
    {
        $value = $this->values[$this->input];

        if (strlen($value) < $length) {
            $message = (empty($message)) ? "[FIELD] doit contenir au minimum {$length} caractères." : $message;
            $this->addError($message);
        }

        return $this;
    }

    /**
     * @param int $length
     * @param string|null $message
     * @return $this
     */
    public function maxLength(int $length = 255, ?string $message = null)
    {
        $value = $this->values[$this->input];

        if (strlen($value) > $length) {
            $message = (empty($message)) ? "[FIELD] doit contenir au maximum {$length} caractères." : $message;
            $this->addError($message);
        }

        return $this;
    }
}
