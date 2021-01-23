<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Exception\TwigException;
use App\Manager\CommentManager;
use App\Services\Session;

class DashboardController extends Controller
{
    /** @var Session */
    private Session $session;

    /**
     * DashboardController constructor.
     * @param $action
     * @param $params
     */
    public function __construct($action, $params)
    {
        parent::__construct($action, $params);

        $this->session = new Session();
        $this->session->redirectIfNotAuth();
    }

    /**
     * @throws TwigException
     */
    public function executeShowDashboard(): void
    {
        if (!$this->session->isAdmin()) {
            $this->session->redirectUrl('/dashboard/profil');
        }

        $manager = new CommentManager();
        $counter = $manager->getDashboardStats();

        $this->render('@admin/dashboard.html.twig', [
            'count_onl_articles' => $counter['art_online'],
            'count_com_total'    => $counter['com_total'],
            'count_com_pending'  => $counter['com_pending'],
            'count_reg_users'    => $counter['usr_registered'],
            'lastComments'       => $manager->getCommentAndAuthor(3)
        ]);
    }
}
