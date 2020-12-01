<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;
use App\Core\Validator;
use App\Entity\Admin;
use App\Entity\User;
use App\Exception\TwigException;
use App\Manager\AdminManager;
use App\Manager\UserManager;
use App\Services\FlashBag;
use App\Services\Session;
use App\Services\UserAuth;

class AccountController extends Controller
{
    const ROLE_ADMIN = 'admin';
    const ROLE_USER  = 'user';

    private UserManager $manager;
    private FlashBag $flashBag;
    private UserAuth $userAuth;
    private Session $session;

    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        $this->manager  = new UserManager();
        $this->flashBag = new FlashBag();
        $this->userAuth = new UserAuth();
        $this->session  = new Session();
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
            $formCheck         = (new Validator($_POST));
            $adminAlreadyExist = $this->userAuth->isAdminAlreadyExist();

            if ($formCheck->registerValidation() && !$this->userAuth->isUserAlreadyRegistered($_POST)) {
                $user = new User();

                $role = (!$adminAlreadyExist) ? self::ROLE_ADMIN : self::ROLE_USER;
                $user->setRole($role);
                $user->hydrate($_POST);

                $this->manager->create($user);

                if (!$adminAlreadyExist) {
                    $this->createAdmin($user);
                }

                $this->flashBag->set(FlashBag::SUCCESS, "Utilisateur crée.");
                $this->redirectUrl('/login');
            }
        }
        $this->render('@public/register.html.twig', [
            'formpost' => $_POST
        ]);
    }

    /**
     * @param User $user
     * @throws \ReflectionException
     */
    public function createAdmin(User $user): void
    {
        $adminManager = new AdminManager();
        $admin        = new Admin();

        $getUser = $this->manager->findBy(['email' => $user->getEmail()]);
        $admin->setUserId($getUser['id']);
        $adminManager->create($admin);

        $this->flashBag->set(FlashBag::SUCCESS, "Admin crée.");
        $this->redirectUrl('/login');
    }

    public function executeLogout(): void
    {
        (new Session())->clear();
    }
}
