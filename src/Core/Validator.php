<?php

namespace App\Core;

use App\Services\FlashBag;

class Validator
{
    use ValidatorTrait;

    private ?string $input = null;
    private array $values;
    private array $fieldNames;
    private FlashBag $flashBag;
    private $status = true;

    /**
     * Validator constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->values   = $data;
        $this->flashBag = new FlashBag();
    }

    /**
     * @param string $name
     * @param string|null $label
     * @return $this
     */
    public function check(string $name, ?string $label = null)
    {
        $this->input = $name;

        if (!empty($label)) {
            $this->fieldNames[$name] = $label;
        }

        return $this;
    }

    /**
     * @param string $type
     * @param string $message
     * @return $this
     */
    public function addError(string $type, string $message)
    {
        $message = str_replace('[FIELD]', $this->getFieldName($this->input), $message);
        $this->flashBag->set($type, $message);

        return $this;
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

    /**
     * Validation methods
     */

    /**
     * @return array|false
     */
    public function articleValidation()
    {
        $this->check('title', 'Le titre')->required()->maxLength(255);
        $this->check('textHeader', 'Le chapô')->required();
        $this->check('content', 'Le contenu')->required();
        $this->check('imageAlt', 'Le texte de l\'image')->required()->maxLength(100);

        return $this->status;
    }
}
