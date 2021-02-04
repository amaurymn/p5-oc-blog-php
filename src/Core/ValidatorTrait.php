<?php

namespace App\Core;

use App\Services\FlashBag;

trait ValidatorTrait
{

    /**
     * required field, return error if the field is empty
     * @param string|null $message
     * @return $this
     */
    public function required(?string $message = null)
    {
        $value = $this->values[$this->input];

        if (empty($value)) {
            $message      = empty($message) ? "[FIELD] ne doit pas être vide." : $message;
            $this->status = false;

            return $this->addError(FlashBag::ERROR, $message);
        }

        return $this;
    }

    /**
     * check if 2 fields are identical
     * @param string $string
     * @return $this
     */
    public function checkSame(string $string): self
    {
        $inputStr  = $this->values[$this->input];
        $repeatStr = $this->values[$string];

        if ($inputStr !== $repeatStr) {
            $this->status = false;

            return $this->addError(FlashBag::ERROR, "[FIELD] ne sont pas identiques.");
        }

        return $this;
    }

    /**
     * email validation, check if the email is valid
     * @param string|null $message
     * @return $this
     */
    public function email(?string $message = null)
    {
        $value = $this->values[$this->input];

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $message      = empty($message) ? "[FIELD] n'est pas valide." : $message;
            $this->status = false;

            return $this->addError(FlashBag::ERROR, $message);
        }

        return $this;
    }

    /**
     * url validation, check if url is valid
     * @param string|null $message
     * @return $this|Validator
     */
    public function url(?string $message = null)
    {
        $value = $this->values[$this->input];

        if (
            !filter_var($value, FILTER_VALIDATE_URL)
            || !in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true)
        ) {
            $message      = empty($message) ? "[FIELD] n'est pas valide." : $message;
            $this->status = false;

            return $this->addError(FlashBag::ERROR, $message);
        }

        return $this;
    }

    /**
     * length validation, check if the field is equal to X chars
     * @param int $length
     * @param string|null $message
     * @return $this
     */
    public function length(int $length = 255, ?string $message = null)
    {
        $value = $this->values[$this->input];

        if ((!empty($value) && mb_strlen($value) < $length) || (!empty($value) && mb_strlen($value) > $length)) {
            $message      = (empty($message)) ? "[FIELD] doit contenir {$length} caractères." : $message;
            $this->status = false;

            return $this->addError(FlashBag::ERROR, $message);
        }

        return $this;
    }

    /**
     * length validation, check if the field has minimum of X chars
     * @param int $length
     * @param string|null $message
     * @return $this
     */
    public function minLength(int $length = 255, ?string $message = null)
    {
        $value = $this->values[$this->input];

        if (mb_strlen($value) < $length) {
            $message      = (empty($message)) ? "[FIELD] doit contenir au minimum {$length} caractères." : $message;
            $this->status = false;

            return $this->addError(FlashBag::ERROR, $message);
        }

        return $this;
    }

    /**
     * length validation, check if the field has maximum of X chars
     * @param int $length
     * @param string|null $message
     * @return $this
     */
    public function maxLength(int $length = 255, ?string $message = null)
    {
        $value = $this->values[$this->input];

        if (mb_strlen($value) > $length) {
            $message      = (empty($message)) ? "[FIELD] doit contenir au maximum {$length} caractères." : $message;
            $this->status = false;

            return $this->addError(FlashBag::ERROR, $message);
        }

        return $this;
    }
}
