<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;

class DashboardController extends Controller
{

    public function executeShowDashboard()
    {
        $this->render('@admin/dashboard.html.twig');
    }
}