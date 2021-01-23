<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;
use App\Exception\TwigException;
use App\Manager\ArticleManager;
use App\Manager\UserManager;

class HomeController extends Controller
{
    /**
     * @throws TwigException
     */
    public function executeShowHome(): void
    {
        $articleManager = new ArticleManager();
        $userManager    = new UserManager();

        $articles = $articleManager->findAll(['created_at' => 'DESC'], '3');
        $about    = $userManager->getAdminInfos();

        $this->render('@public/homepage.html.twig', [
            'about'    => $about,
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
