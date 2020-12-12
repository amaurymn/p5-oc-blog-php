<?php

namespace App\Services;

use App\Manager\UserManager;

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
     * @param array $post
     * @return bool
     */
    public function authenticateUser(array $post): bool
    {
        $user = (new UserManager())->getUser($post['email']);

        if ($user && password_verify($post['password'], $user['password'])) {
            $this->session->set('user', $user);

            return true;
        }

        $this->flash->set(FlashBag::ERROR, 'Utilisateur ou mot de passe incorrect.');

        return false;
    }

    /**
     * @return bool
     */
    public function isAdminAlreadyExist(): bool
    {
        return (new UserManager())->checkAdminAlreadyExist() ? true : false;
    }

    /**
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
     * @param array $post
     * @return bool
     */
    public function isUserAlreadyRegistered(array $post): bool
    {
        $user = (new UserManager())->getUser($post['email']);

        if ($user) {
            $this->flash->set(FlashBag::ERROR, 'Cette adresse email est déjà utilisée.');

            return true;
        }

        return false;
    }
}
