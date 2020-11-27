<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Exception\TwigException;
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

        $this->render('@admin/dashboard.html.twig');
    }
}
