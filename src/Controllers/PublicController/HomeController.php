<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;

class HomeController extends Controller
{

    public function executeShowHome()
    {
        $this->render('public/homepage.twig');
    }

    public function executeError404()
    {
        $this->render('public/404.twig');
    }

    public function executeTest()
    {
        $this->render('public/test.twig', ['params' => $this->params['param']]);
    }
}