<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;
use App\Core\Validator;
use App\Entity\Admin;
use App\Entity\User;
use App\Exception\ConfigException;
use App\Exception\EntityNotFoundException;
use App\Exception\TwigException;
use App\Manager\AdminManager;
use App\Manager\UserManager;
use App\Services\FileUploader;
use App\Services\FlashBag;
use App\Services\InstallState;
use App\Services\Session;
use App\Services\UserAuth;
use ReflectionException;
use Symfony\Component\Yaml\Yaml;

class AccountController extends Controller
{
    const ROLE_ADMIN = 'admin';
    const ROLE_USER  = 'user';

    private UserManager $userManager;
    private FlashBag $flashBag;
    private UserAuth $userAuth;
    private Session $session;

    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        $this->userManager = new UserManager();
        $this->flashBag    = new FlashBag();
        $this->userAuth    = new UserAuth();
        $this->session     = new Session();
    }

    /**
     * @throws TwigException
     */
    public function executeShowLogin(): void
    {
        if ($this->isFormSubmit('login') && $this->userAuth->authenticateUser($_POST)) {
            $this->flashBag->set(FlashBag::INFO, "Vous êtes à présent connecté·e.");
            $this->redirectUrl('/');
        }

        $this->render('@public/login.html.twig');
    }

    /**
     * @throws ConfigException
     * @throws TwigException
     * @throws ReflectionException
     */
    public function executeShowRegister(): void
    {
        if ($this->isFormSubmit('register')) {
            $formCheck         = new Validator($_POST);
            $adminAlreadyExist = $this->userAuth->isAdminAlreadyExist();
            $installState      = new InstallState();

            if (
                $formCheck->registerValidation()
                && !$this->userAuth->isUserAlreadyRegistered($_POST)
                && !$this->userAuth->isUserNameAlreadyExist($_POST)
            ) {
                $user = new User();

                $role = (!$adminAlreadyExist) ? self::ROLE_ADMIN : self::ROLE_USER;
                $user->setRole($role);
                $user->hydrate($_POST);
                $user->setPassword($this->userAuth->setPassword($user->getPassword()));

                if (!$adminAlreadyExist) {
                    $_SESSION['installation'] = true;
                    $this->session->set('register_user', [$user]);
                    $installState->writeInstallStatus(true);
                    $this->redirectUrl('/registerAdmin');
                } else {
                    $this->userManager->create($user);
                    $this->flashBag->set(FlashBag::SUCCESS, "Utilisateur {$user->getUserName()} crée.");
                    $this->redirectUrl('/login');
                }
            }
        }
        $this->render('@public/register.html.twig', [
            'formpost' => $_POST
        ]);
    }

    /**
     * @throws EntityNotFoundException
     * @throws TwigException
     * @throws ReflectionException
     */
    public function executeShowRegisterAdmin(): void
    {
        (!$this->session->get('installation')) ? $this->redirectUrl('/register') : null;

        $sessionUser = $this->session->get('register_user')[0];

        if ($this->isFormSubmit('registertwo')) {
            $formCheck = new Validator($_POST);
            $pdf       = new FileUploader($_FILES, 'cvLink');
            $image     = new FileUploader($_FILES, 'image');

            if (
                $formCheck->registerValidationAdmin()
                && $pdf->checkFile(FileUploader::FILE_PDF)
                && $image->checkFile(FileUploader::FILE_IMG)
            ) {
                $adminManager = new AdminManager();
                $admin        = new Admin();
                $pdf->upload(FileUploader::TYPE_PROFILE, FileUploader::FILE_ADMIN_PATH);
                $image->upload(FileUploader::TYPE_PROFILE, FileUploader::FILE_ADMIN_PATH);

                $this->userManager->create($sessionUser);
                $user = $this->userManager->findOneBy(['email' => $sessionUser->getEmail()]);

                $admin->hydrate($_POST);
                $admin->setImage($image->getName());
                $admin->setCvLink($pdf->getName());
                $admin->setUserId($user->getId());

                $adminManager->create($admin);
                $this->session->clear('installation')->clear('register_user');
                $this->flashBag->set(FlashBag::SUCCESS, "Le compte admin a été crée.");
                $this->redirectUrl('/login');
            }
        }

        $this->render('@public/register-two.html.twig', [
            'formpost' => $_POST
        ]);
    }

    public function executeLogout(): void
    {
        (new Session())->clear();
    }
}
