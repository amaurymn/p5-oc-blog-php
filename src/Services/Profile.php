<?php

namespace App\Services;

use App\Core\Validator;
use App\Entity\SocialNetwork;
use App\Exception\EntityNotFoundException;
use App\Exception\FileException;
use App\Manager\AdminManager;
use App\Manager\SocialNetworkManager;
use App\Manager\UserManager;
use ReflectionException;

class Profile
{
    private const DASHBOARD_PROFILE = '/dashboard/profil';
    private UserManager $userManager;
    private AdminManager $adminManager;
    private UserAuth $userAuth;
    private Session $session;
    private FlashBag $flashBag;
    private SocialNetworkManager $socialNetworkManager;

    public function __construct()
    {
        $this->userManager          = new UserManager();
        $this->adminManager         = new AdminManager();
        $this->socialNetworkManager = new SocialNetworkManager();
        $this->userAuth             = new UserAuth();
        $this->session              = new Session();
        $this->flashBag             = new FlashBag();
    }

    /**
     * update users infos
     * @param array $post
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    public function executeSaveInfos(array $post): void
    {
        $formCheck = new Validator($post);
        $user      = $this->userManager->findOneBy(['email' => $this->session->get('user')->getEmail()]);
        if ($formCheck->userInfoAdmValidation()) {

            if ($post['userName'] !== $user->getUserName() && !$this->userAuth->isUserNameAlreadyExist($post)) {
                $user->setUserName($post['userName']);
            }
            $user->hydrate($post);
            $this->userManager->update($user);

            $this->session->setSubKey('user', 'FirstName', $user->getFirstName());
            $this->session->setSubKey('user', 'LastName', $user->getLastName());
            $this->session->setSubKey('user', 'UserName', $user->getUserName());

            $this->flashBag->set(FlashBag::INFO, 'Infos mis à jour.');
            $this->session->redirectUrl(self::DASHBOARD_PROFILE);
        }
    }

    /**
     * update new email address
     * @param array $post
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    public function executeSaveEmail(array $post): void
    {
        $formCheck = new Validator($post);

        if ($formCheck->emailAdmValidation() && !$this->userAuth->isUserAlreadyRegistered($post)) {
            $user = $this->userManager->findOneBy(['email' => $this->session->get('user')->getEmail()]);

            $user->hydrate($post);
            $this->userManager->update($user);

            $this->session->setSubKey('user', 'email', $user->getEmail());

            $this->flashBag->set(FlashBag::INFO, 'Adresse email mise à jour.');
            $this->session->redirectUrl(self::DASHBOARD_PROFILE);
        }
    }

    /**
     * update password
     * @param array $post
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    public function executeSavePassword(array $post): void
    {
        $formCheck = new Validator($post);

        if ($formCheck->userPasswordAdmValidation()) {
            $user = $this->userManager->findOneBy(['email' => $this->session->get('user')->getEmail()]);

            $user->hydrate($post);
            $user->setPassword($this->userAuth->setPassword($user->getPassword()));

            $this->userManager->update($user);
            $this->flashBag->set(FlashBag::INFO, 'Mot de passe mis à jour');
            $this->session->redirectUrl(self::DASHBOARD_PROFILE);
        }
    }

    /**
     * update profile picture
     * @param array $file
     * @param array $post
     * @throws EntityNotFoundException
     * @throws FileException
     * @throws ReflectionException
     */
    public function executeSaveImageAdmin(array $file, array $post): void
    {
        $file      = new FileUploader($file);
        $formCheck = new Validator($post);
        $admin     = $this->adminManager->findOneBy(['user_id' => $this->session->get('user')->getUserId()]);

        if ($file->checkFile(FileUploader::FILE_IMG) && $formCheck->imgAltValidationAdmin()) {

            $file->deleteFile(FileUploader::TYPE_PROFILE, $admin->getImage());
            $file->upload(FileUploader::TYPE_PROFILE);

            $admin->setImage($file->getName());
            $admin->hydrate($post);

            $this->adminManager->update($admin);

            $this->session->setSubKey('user', 'AltImg', $admin->getAltImg());

            $this->flashBag->set(FlashBag::SUCCESS, "Image mise à jour..");
            $this->session->redirectUrl(self::DASHBOARD_PROFILE);
        }
    }

    /**
     * update short description
     * @param array $post
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    public function executeSaveInfosAdmin(array $post): void
    {
        $formCheck = new Validator($post);

        if ($formCheck->registerValidationAdmin()) {
            $admin = $this->adminManager->findOneBy(['user_id' => $this->session->get('user')->getUserId()]);

            $admin->hydrate($post);
            $this->adminManager->update($admin);

            $this->session->setSubKey('user', 'ShortDescription', $admin->getShortDescription());

            $this->flashBag->set(FlashBag::INFO, 'Infos Admin mis à jour.');
            $this->session->redirectUrl(self::DASHBOARD_PROFILE);
        }
    }

    /**
     * add new social network
     * @param array $post
     * @throws ReflectionException
     */
    public function executeSaveNetwork(array $post): void
    {
        $formCheck = new Validator($post);

        if ($formCheck->socialNetworkValidator()) {
            $socialNetwork = new SocialNetwork(['admin_id' => $this->session->get('user')->getId()]);

            $socialNetwork->hydrate($post);
            $this->socialNetworkManager->create($socialNetwork);

            $this->flashBag->set(FlashBag::SUCCESS, "Réseau social Ajouté.");
            $this->session->redirectUrl(self::DASHBOARD_PROFILE . '#sociaNetworks');
        }
    }

    /**
     * update CV
     * @param array $file
     * @throws EntityNotFoundException
     * @throws FileException
     * @throws ReflectionException
     */
    public function executeSaveCv(array $file): void
    {
        $file = new FileUploader($file);

        if ($file->checkFile(FileUploader::FILE_PDF)) {
            $admin = $this->adminManager->findOneBy(['user_id' => $this->session->get('user')->getUserId()]);

            $file->deleteFile(FileUploader::TYPE_PROFILE, $admin->getCvLink());
            $file->upload(FileUploader::TYPE_PROFILE);

            $admin->setCvLink($file->getName());
            $this->adminManager->update($admin);

            $this->flashBag->set(FlashBag::SUCCESS, "CV envoyé.");
            $this->session->redirectUrl(self::DASHBOARD_PROFILE);
        }
    }
}
