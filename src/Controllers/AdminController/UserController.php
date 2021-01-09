<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Core\Validator;
use App\Entity\SocialNetwork;
use App\Exception\EntityNotFoundException;
use App\Exception\FileException;
use App\Exception\TwigException;
use App\Manager\AdminManager;
use App\Manager\SocialNetworkManager;
use App\Manager\UserManager;
use App\Services\FileUploader;
use App\Services\FlashBag;
use App\Services\Session;
use App\Services\UserAuth;
use ReflectionException;

class UserController extends Controller
{
    private UserManager $userManager;
    private AdminManager $adminManager;
    private UserAuth $userAuth;
    private Session $session;
    private FlashBag $flashBag;
    private SocialNetworkManager $socialNetworkManager;

    /**
     * UserController constructor.
     * @param $action
     * @param $params
     */
    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        $this->userManager          = new UserManager();
        $this->adminManager         = new AdminManager();
        $this->socialNetworkManager = new SocialNetworkManager();
        $this->userAuth             = new UserAuth();
        $this->session              = new Session();
        $this->flashBag             = new FlashBag();
    }

    /**
     * @throws EntityNotFoundException
     * @throws FileException
     * @throws ReflectionException
     * @throws TwigException
     */
    public function executeShowProfile(): void
    {
        $user          = $this->session->get('user');
        $socialNetwork = $this->socialNetworkManager->findAll();

        if ($this->isFormSubmit('saveInfos')) {
            $this->executeSaveInfos($_POST);
        }
        if ($this->isFormSubmit('saveEmail')) {
            $this->executeSaveEmail($_POST);
        }
        if ($this->isFormSubmit('savePassword')) {
            $this->executeSavePassword($_POST);
        }
        if ($this->isFormSubmit('saveInfosAdmin')) {
            $this->executeSaveInfosAdmin($_POST);
        }
        if ($this->isFormSubmit('saveImageAdmin')) {
            $this->executeSaveImageAdmin($_FILES);
        }
        if ($this->isFormSubmit('saveNetwork')) {
            $this->executeSaveNetwork($_POST);
        }
        if ($this->isFormSubmit('saveInfosCv')) {
            $this->executeSaveCv($_FILES);
        }

        $this->render('@admin/profile.html.twig', [
            'user'          => $user,
            'socialNetwork' => $socialNetwork
        ]);
    }

    /**
     * @param array $post
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    private function executeSaveInfos(array $post): void
    {
        $formCheck = (new Validator($post));
        $user      = $this->userManager->findOneBy(['email' => $this->session->get('user')['email']]);
        if ($formCheck->userInfoAdmValidation()) {

            if ($post['userName'] !== $user->getUserName() && !$this->userAuth->isUserNameAlreadyExist($post)) {
                $user->setUserName($post['userName']);
            }
            $user->hydrate($post);
            $this->userManager->update($user);

            $this->session->setSubKey('user', 'first_name', $user->getFirstName());
            $this->session->setSubKey('user', 'last_name', $user->getLastName());
            $this->session->setSubKey('user', 'user_name', $user->getUserName());

            $this->flashBag->set(FlashBag::INFO, 'Infos mis à jour.');
            $this->session->redirectUrl('/dashboard/profil');
        }
    }

    /**
     * @param array $post
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    private function executeSaveEmail(array $post): void
    {
        $formCheck = (new Validator($post));

        if ($formCheck->emailAdmValidation() && !$this->userAuth->isUserAlreadyRegistered($post)) {
            $user = $this->userManager->findOneBy(['email' => $this->session->get('user')['email']]);

            $user->hydrate($post);
            $this->userManager->update($user);

            $this->session->setSubKey('user', 'email', $user->getEmail());

            $this->flashBag->set(FlashBag::INFO, 'Adresse email mise à jour.');
            $this->session->redirectUrl('/dashboard/profil');
        }
    }

    /**
     * @param array $post
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    private function executeSavePassword(array $post): void
    {
        $formCheck = (new Validator($post));

        if ($formCheck->userPasswordAdmValidation()) {
            $user = $this->userManager->findOneBy(['email' => $this->session->get('user')['email']]);

            $user->hydrate($post);
            $user->setPassword($this->userAuth->setPassword($user->getPassword()));

            $this->userManager->update($user);
            $this->flashBag->set(FlashBag::INFO, 'Mot de passe mis à jour');
            $this->session->redirectUrl('/dashboard/profil');
        }
    }

    /**
     * @param array $file
     * @throws EntityNotFoundException
     * @throws FileException
     * @throws ReflectionException
     */
    private function executeSaveImageAdmin(array $file): void
    {
        $file  = new FileUploader($file);
        $admin = $this->adminManager->findOneBy(['user_id' => $this->session->get('user')['id']]);

        if ($file->checkFile(FileUploader::FILE_IMG)) {
            $file->deleteFile(FileUploader::TYPE_PROFILE, $admin->getImage());
            $file->upload(FileUploader::TYPE_PROFILE, FileUploader::FILE_ADMIN_PATH);

            $admin->setImage($file->getName());
            $this->adminManager->update($admin);

            $this->flashBag->set(FlashBag::SUCCESS, "Image mise à jour..");
            $this->redirectUrl('/dashboard/profil');
        }
    }

    /**
     * @param array $post
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    private function executeSaveInfosAdmin(array $post): void
    {
        $formCheck = new Validator($post);

        if ($formCheck->registerValidationAdmin()) {
            $admin = $this->adminManager->findOneBy(['user_id' => $this->session->get('user')['id']]);

            $admin->hydrate($post);
            $this->adminManager->update($admin);

            $this->session->setSubKey('user', 'alt_img', $admin->getAltImg());
            $this->session->setSubKey('user', 'short_description', $admin->getShortDescription());

            $this->flashBag->set(FlashBag::INFO, 'Infos Admin mis à jour.');
            $this->session->redirectUrl('/dashboard/profil');
        }
    }

    /**
     * @param array $post
     * @throws ReflectionException
     */
    private function executeSaveNetwork(array $post): void
    {
        $formCheck = new Validator($post);

        if ($formCheck->socialNetworkValidator()) {
            $socialNetwork = new SocialNetwork(['admin_id' => $this->session->get('user')['admin_id']]);

            $socialNetwork->hydrate($post);
            $this->socialNetworkManager->create($socialNetwork);

            $this->flashBag->set(FlashBag::SUCCESS, "Réseau social Ajouté.");
            $this->redirectUrl('/dashboard/profil#sociaNetworks');
        }
    }

    /**
     * @throws EntityNotFoundException
     */
    public function executeDeleteNetwork(): void
    {
        $network = $this->socialNetworkManager->findOneBy(['id' => $this->params['networkId']]);
        $this->socialNetworkManager->delete($network);

        $this->flashBag->set(FlashBag::SUCCESS, "Réseau social supprimé.");
        $this->redirectUrl('/dashboard/profil#sociaNetworks');
    }

    /**
     * @param array $file
     * @throws EntityNotFoundException
     * @throws FileException
     * @throws ReflectionException
     */
    private function executeSaveCv(array $file): void
    {
        $file = new FileUploader($file);

        if ($file->checkFile(FileUploader::FILE_PDF)) {
            $admin = $this->adminManager->findOneBy(['user_id' => $this->session->get('user')['id']]);

            $file->deleteFile(FileUploader::TYPE_PROFILE, $admin->getCvLink());
            $file->upload(FileUploader::TYPE_PROFILE, FileUploader::FILE_ADMIN_PATH);

            $admin->setCvLink($file->getName());
            $this->adminManager->update($admin);

            $this->flashBag->set(FlashBag::SUCCESS, "CV envoyé.");
            $this->redirectUrl('/dashboard/profil');
        }

    }

}

