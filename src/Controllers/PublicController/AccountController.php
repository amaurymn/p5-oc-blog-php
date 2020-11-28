<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;
use App\Core\Validator;
use App\Entity\Admin;
use App\Entity\User;
use App\Exception\TwigException;
use App\Manager\UserManager;
use App\Services\FlashBag;
use App\Services\UserAuth;
use App\Services\Session;

class AccountController extends Controller
{
    private UserManager $manager;
    private FlashBag $flashBag;
    private UserAuth $userAuth;

    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        $this->manager  = new UserManager();
        $this->flashBag = new FlashBag();
        $this->userAuth = new UserAuth();
    }

    /**
     * @throws TwigException
     */
    public function executeShowLogin(): void
    {
        if ($this->isFormSubmit('login') && $this->userAuth->authenticateUser($_POST)) {
            $this->redirectUrl('/dashboard');
        }

        $this->render('@public/login.html.twig');
    }

    /**
     * @throws TwigException
     * @throws \ReflectionException
     */
    public function executeShowRegister(): void
    {
        if ($this->isFormSubmit('register')) {
            $formCheck = (new Validator($_POST));
            $hadAdmin  = $this->manager->findOneBy(['role' => 'admin']);

            if ($formCheck->registerValidation() && !$this->userAuth->isUserAlreadyRegistered($_POST)) {
                $user = new User();
                $hadAdmin ? $user->setRole('user') : $user->setRole('admin');

                $user->hydrate($_POST);
                $this->manager->create($user);

                $this->flashBag->set(FlashBag::SUCCESS, "Utilisateur crée.");
                $this->redirectUrl('/login');
            }
        }
        $this->render('@public/register.html.twig', [
            'formpost' => $_POST
        ]);
    }

    public function executeLogout(): void
    {
        (new Session())->clear();
    }
}
