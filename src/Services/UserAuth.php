<?php

namespace App\Services;

use App\Manager\UserManager;

class UserAuth
{

    private $flash;
    private $session;

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
        $user = (new UserManager())->findOneBy(['email' => $post['email']]);

        if ($user && password_verify($post['password'], $user['password'])) {
            $this->session->set([
                'auth'     => true,
                'id'       => $user['id'],
                'role'     => $user['role']
            ]);

            return true;
        }

        $this->flash->set(FlashBag::ERROR, 'Utilisateur ou mot de passe incorrect.');
        return false;
    }
}
