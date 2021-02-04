<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;
use App\Core\Validator;
use App\Exception\EmailException;
use App\Exception\TwigException;
use App\Services\Mailer;

class ContactController extends Controller
{
    /**
     * show contact page
     * @throws TwigException
     * @throws EmailException
     */
    public function executeShowContact(): void
    {
        $formCheck = new Validator($_POST);

        if ($this->isFormSubmit('submit') && $formCheck->contactFormValidation()) {
            $mailer = new Mailer();
            $mailer->sendMail($_POST);

            $this->redirectUrl('/contact');
        }

        $this->render('@public/contact.html.twig', [
            'post' => $_POST
        ]);
    }
}
