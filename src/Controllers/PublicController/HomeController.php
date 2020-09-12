<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;

class HomeController extends Controller
{

    public function executeShowHome()
    {
        $this->render('@public/homepage.html.twig');
    }

    public function executeError404()
    {
        $this->render('@public/404.html.twig');
    }
}