<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Exception\EntityNotFoundException;
use App\Exception\FileException;
use App\Exception\TwigException;
use App\Manager\SocialNetworkManager;
use App\Services\FlashBag;
use App\Services\Profile;
use App\Services\Session;
use ReflectionException;

class UserController extends Controller
{
    private Session $session;
    private FlashBag $flashBag;
    private SocialNetworkManager $socialNetworkManager;
    private Profile $profileService;

    public function __construct($action, $params)
    {
        parent::__construct($action, $params);

        $this->session              = new Session();
        $this->socialNetworkManager = new SocialNetworkManager();
        $this->profileService       = new Profile();
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
            $this->profileService->executeSaveInfos($_POST);
        }
        if ($this->isFormSubmit('saveEmail')) {
            $this->profileService->executeSaveEmail($_POST);
        }
        if ($this->isFormSubmit('savePassword')) {
            $this->profileService->executeSavePassword($_POST);
        }
        if ($this->isFormSubmit('saveInfosAdmin')) {
            $this->profileService->executeSaveInfosAdmin($_POST);
        }
        if ($this->isFormSubmit('saveImageAdmin')) {
            $this->profileService->executeSaveImageAdmin($_FILES, $_POST);
        }
        if ($this->isFormSubmit('saveNetwork')) {
            $this->profileService->executeSaveNetwork($_POST);
        }
        if ($this->isFormSubmit('saveInfosCv')) {
            $this->profileService->executeSaveCv($_FILES);
        }

        $this->render('@admin/profile.html.twig', [
            'user'          => $user,
            'socialNetwork' => $socialNetwork
        ]);
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
}

