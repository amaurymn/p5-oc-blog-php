<?php

namespace App\Services;

class Validator
{
    use ValidatorTrait;

    private ?string $input = null;
    private array $values;
    private array $errors = [];
    private array $fieldNames;

    /**
     * Validator constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->values = $data;
    }

    /**
     * @param string $name
     * @param string|null $label
     * @return $this
     */
    public function validate(string $name, ?string $label = null)
    {
        $this->input = $name;

        if (!empty($label)) {
            $this->fieldNames[$name] = $label;
        }

        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function addError(string $message)
    {
        $message = str_replace('[FIELD]', $this->getFieldName($this->input), $message);
        $this->errors[$this->input][] = $message;

        return $this;
    }

    /**
     * @return array|false
     */
    public function hasErrors()
    {
        return $this->errors ? $this->errors : false;
    }

    /**
     * @param string $name
     * @return mixed|string
     */
    private function getFieldName(string $name)
    {
        if (isset($this->fieldNames[$name])) {
            $name = $this->fieldNames[$name];
        }

        return $name;
    }
}
