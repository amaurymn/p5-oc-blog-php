<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Exception\TwigException;
use App\Manager\CommentManager;
use App\Services\DashboardStats;
use App\Services\Session;

class DashboardController extends Controller
{
    /** @var Session */
    private Session $session;

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

        $stats    = new DashboardStats();
        $comments = new CommentManager();

        $count = $stats->getStats();

        $this->render('@admin/dashboard.html.twig', [
            'count_onl_articles' => $count['art_online'],
            'count_com_total'    => $count['com_total'],
            'count_com_pending'  => $count['com_pending'],
            'count_reg_users'    => $count['usr_registered'],
            'lastComments'       => $comments->getCommentAndAuthor(3)
        ]);
    }
}
