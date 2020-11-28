<?php

namespace App\Services;

use App\Exception\EntityNotFoundException;
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
            $this->session->set($user);

            return true;
        }

        $this->flash->set(FlashBag::ERROR, 'Utilisateur ou mot de passe incorrect.');

        return false;
    }

    /**
     * @param array $post
     * @return bool
     * @throws EntityNotFoundException
     */
    public function isUserAlreadyRegistered(array $post): bool
    {
        $user = (new UserManager())->findOneBy(['email' => $post['email']]);

        if ($user) {
            $this->flash->set(FlashBag::ERROR, 'Cette adresse email est déjà utilisée.');

            return true;
        }

        return false;
    }
}
