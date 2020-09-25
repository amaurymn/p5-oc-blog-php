<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;

class AccountController extends Controller
{
    public function executeShowLogin()
    {
        $this->render('@public/login.html.twig');
    }

    public function executeShowRegister()
    {
        $this->render('@public/register.html.twig');
    }
}