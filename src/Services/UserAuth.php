<?php

namespace App\Services;

use App\Manager\AdminManager;
use App\Manager\UserManager;
use ReflectionClass;
use ReflectionException;

class UserAuth
{
    private FlashBag $flash;
    private Session $session;

    public function __construct()
    {
        $this->flash   = new FlashBag();
        $this->session = new Session();
    }

    /**
     * Return if user exists and password is valid
     * @param array $post
     * @return bool
     * @throws ReflectionException
     */
    public function authenticateUser(array $post): bool
    {
        $user = (new UserManager())->getUserByMail($post['email']);

        if ($user && password_verify($post['password'], $user->getPassword())) {
            $admin = (new AdminManager())->getAdminByUser($user->getId());

            if ($admin) {
                $userArray = $this->dismountObject($user);

                foreach ($userArray as $key => $value)
                {
                    $setter = 'set' . ucfirst($key);
                    $admin->$setter($value);
                }

                $this->session->set('user', $admin);
            } else {
                $this->session->set('user', $user);
            }

            return true;
        }

        $this->flash->set(FlashBag::ERROR, 'Utilisateur ou mot de passe incorrect.');

        return false;
    }

    /**
     * Return if admin already exists
     * @return bool
     */
    public function isAdminAlreadyExist(): bool
    {
        return (new UserManager())->checkAdminAlreadyExist() ? true : false;
    }

    /**
     * Return if username already exists
     * @param array $post
     * @return bool
     */
    public function isUserNameAlreadyExist(array $post): bool
    {
        $userNameExist = (new UserManager())->checkUserNameAlreadyExist($post['userName']);

        if ($userNameExist) {
            $this->flash->set(FlashBag::ERROR, "Ce nom d'utilisateur est déjà utilisé.");

            return true;
        }

        return false;
    }

    /**
     * Return if email already exists in db
     * @param array $post
     * @return bool
     */
    public function isUserAlreadyRegistered(array $post): bool
    {
        $user = (new UserManager())->findBy(['email' => $post['email']]);

        if (!empty($user)) {
            $this->flash->set(FlashBag::ERROR, 'Cette adresse email est déjà utilisée.');

            return true;
        }

        return false;
    }

    /**
     * Set new password with ARGON2ID algo
     * @param string $password
     * @return string
     */
    public function setPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    /**
     * Make class properties accessible to get values
     * @param object $object
     * @return array
     * @throws ReflectionException
     */
    private function dismountObject(object $object): array
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
            $property->setAccessible(false);
        }
        return $array;
    }
}
