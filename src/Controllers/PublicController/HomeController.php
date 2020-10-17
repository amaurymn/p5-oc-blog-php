<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;
use App\Manager\ArticleManager;

class HomeController extends Controller
{
    public function executeShowHome()
    {
        $manager = new ArticleManager();

        $articles = $manager->findBy([], ['created_at' => 'DESC'], '3');

        $this->render('@public/homepage.html.twig', [
            'articles' => $articles
        ]);
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
