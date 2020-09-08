<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;


class HomeController extends Controller
{
    public function executeShowHome()
    {
        var_dump('allo?');
    }

    public function executeError404()
    {
        var_dump('404');
    }
}