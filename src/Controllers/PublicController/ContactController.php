<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;

class ContactController extends Controller
{
    public function executeShowContact()
    {
        $this->render('@public/contact.html.twig');
    }
}