<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;
use App\Exception\TwigException;
use App\Manager\ArticleManager;

class HomeController extends Controller
{
    public function executeShowHome(): void
    {
        $manager = new ArticleManager();

        $articles = $manager->findAll(['created_at' => 'DESC'], '3');

        $this->render('@public/homepage.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @throws TwigException
     */
    public function executeShowError404(): void
    {
        http_response_code(404);
        $this->render('@public/404.html.twig', ['exceptionMsg' => $this->params['exceptionMsg']]);
    }

    /**
     * @throws TwigException
     */
    public function executeShowError(): void
    {
        http_response_code(500);
        $this->render('@public/error.html.twig', ['exceptionMsg' => $this->params['exceptionMsg']]);
    }

    /**
     * @throws TwigException
     */
    public function executeShowMentions(): void
    {
        $this->render('@public/legal.html.twig');
    }

    /**
     * @throws TwigException
     */
    public function executeShowRGPD(): void
    {
        $this->render('@public/rgpd.html.twig');
    }
}
