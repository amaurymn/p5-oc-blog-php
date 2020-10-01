<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;
use App\Core\Manager;
use App\Entity\Article;

class HomeController extends Controller
{

    public function executeShowHome()
    {
        $manager = (new Manager())->getManagerFor(Article::class);

        $this->render('@public/homepage.html.twig');
    }

    public function executeShowError404()
    {
        $this->render('@public/404.html.twig');
    }

    public function executeShowMentions()
    {
        $this->render('@public/legal.html.twig');
    }

    public function executeShowRGPD()
    {
        $this->render('@public/rgpd.html.twig');
    }
}