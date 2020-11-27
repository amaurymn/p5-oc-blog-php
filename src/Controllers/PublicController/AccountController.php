<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;
use App\Exception\TwigException;
use App\Services\UserAuth;
use App\Services\Session;

class AccountController extends Controller
{
    /**
     * @throws TwigException
     */
    public function executeShowLogin(): void
    {
        if ($this->isFormSubmit('login')) {
            $auth = new UserAuth();

            if ($auth->authenticateUser($_POST)) {
                $this->redirectUrl('/dashboard');
            }
        }

        $this->render('@public/login.html.twig');
    }

    /**
     * @throws TwigException
     */
    public function executeShowRegister(): void
    {
        $this->render('@public/register.html.twig');
    }

    public function executeLogout(): void
    {
        (new Session())->clear();
    }
}
