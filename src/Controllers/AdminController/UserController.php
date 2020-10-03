<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;

class UserController extends Controller
{
    public function executeShowProfile()
    {
        $this->render('@admin/profile.html.twig');
    }
}
