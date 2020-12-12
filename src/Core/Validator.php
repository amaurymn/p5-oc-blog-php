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

    public function registerValidation()
    {
        $this->check('firstName', 'Le nom')->required()->maxLength(50);
        $this->check('lastName', 'Le prénom')->required()->maxLength(50);
        $this->check('userName', 'Le nom d\'utilisateur')->required()->maxLength(50);
        $this->check('email', 'L\'email')->required()->email()->maxLength(255);
        $this->check('password', 'Le mot de passe')->required()->minLength(8)->maxLength(255);
        $this->check('rpassword', 'La confirmation du mot de passe')->required();
        $this->check('password', 'Les mots de passe')->checkSame('rpassword');

        return $this->status;
    }

    public function registerValidationAdmin()
    {
        $this->check('image', "L'URL de l'image")->required()->maxLength(255);
        $this->check('altImg', "La description de l'image")->required()->maxLength(255);
        $this->check('shortDescription', "La description")->required()->maxLength(500);

        return $this->status;
    }

    public function commentValidation()
    {
        $this->check('content', 'Le message')->required()->maxLength(255);

        return $this->status;
    }

    public function userInfoAdmValidation()
    {
        $this->check('firstName', 'Le nom')->required()->maxLength(50);
        $this->check('lastName', 'Le prénom')->required()->maxLength(50);
        $this->check('userName', 'Le nom d\'utilisateur')->required()->maxLength(50);
        $this->check('email', 'L\'email')->required()->email()->maxLength(255);

        return $this->status;
    }

    public function userPasswordAdmValidation()
    {
        $this->check('password', 'Le mot de passe')->required()->minLength(8)->maxLength(255);
        $this->check('rpassword', 'La confirmation du mot de passe')->required();
        $this->check('password', 'Les mots de passe')->checkSame('rpassword');

        return $this->status;
    }

    public function socialNetworkValidator()
    {
        $this->check('name', "Le nom")->required()->maxLength(50);
        $this->check('url', "L'URL")->required()->maxLength(255);
        $this->check('icon', "L'icone")->required()->maxLength(50);

        return $this->status;
    }
}
