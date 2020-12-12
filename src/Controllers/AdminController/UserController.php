<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Core\Validator;
use App\Entity\SocialNetwork;
use App\Exception\EntityNotFoundException;
use App\Exception\TwigException;
use App\Manager\AdminManager;
use App\Manager\SocialNetworkManager;
use App\Manager\UserManager;
use App\Services\FlashBag;
use App\Services\Session;
use App\Services\UserAuth;

class UserController extends Controller
{
    private UserManager $userManager;
    private AdminManager $adminManager;
    private UserAuth $userAuth;
    private Session $session;
    private FlashBag $flashBag;
    private SocialNetworkManager $socialNetworkManager;

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
     * @throws TwigException
     * @throws \ReflectionException
     */
    public function executeShowProfile()
    {
        $user          = $this->session->get('user');
        $socialNetwork = $this->socialNetworkManager->findAll();

        if ($this->isFormSubmit('saveInfos')) {
            $this->executeSaveInfos($_POST);
        }
        if ($this->isFormSubmit('savePassword')) {
            $this->executeSavePassword($_POST);
        }
        if ($this->isFormSubmit('saveInfosAdmin')) {
            $this->executeSaveInfosAdmin($_POST);
        }
        if ($this->isFormSubmit('saveNetwork')) {
            $this->executeSaveNetwork($_POST);
        }

        $this->render('@admin/profile.html.twig', [
            'user'          => $user,
            'socialNetwork' => $socialNetwork
        ]);
    }

    /**
     * @param array $post
     * @throws EntityNotFoundException
     * @throws \ReflectionException
     */
    private function executeSaveInfos(array $post)
    {
        $formCheck = (new Validator($post));

        if (
            $formCheck->userInfoAdmValidation()
            && !$this->userAuth->isUserAlreadyRegistered($post)
            && !$this->userAuth->isUserNameAlreadyExist($post)
        ) {
            $user = $this->userManager->findOneBy(['email' => $this->session->get('user')['email']]);
            $user->hydrate($post);
            $this->userManager->update($user);

            $this->session->clear('user');
            $this->flashBag->set(FlashBag::INFO, 'Infos mis à jour, veuillez vous reconnecter.');
            $this->session->redirectUrl('/login');
        }
    }

    /**
     * @param array $post
     * @throws EntityNotFoundException
     * @throws \ReflectionException
     */
    private function executeSavePassword(array $post)
    {
        $formCheck = (new Validator($post));

        if ($formCheck->userPasswordAdmValidation()) {
            $user = $this->userManager->findOneBy(['email' => $this->session->get('user')['email']]);
            $user->hydrate($post);
            $user->setPassword($this->userAuth->setPassword($user->getPassword()));
            $this->userManager->update($user);

            $this->flashBag->set(FlashBag::INFO, 'Mot de passe mis à jour');
        }
    }

    /**
     * @param array $post
     * @throws EntityNotFoundException
     * @throws \ReflectionException
     */
    private function executeSaveInfosAdmin(array $post)
    {
        $formCheck = new Validator($post);

        if ($formCheck->registerValidationAdmin()) {
            $admin = $this->adminManager->findOneBy(['user_id' => $this->session->get('user')['id']]);
            $admin->hydrate($post);
            $this->adminManager->update($admin);

            $this->session->clear('user');
            $this->flashBag->set(FlashBag::INFO, 'Infos mis à jour, veuillez vous reconnecter.');
            $this->session->redirectUrl('/login');
        }
    }

    /**
     * @param array $post
     * @throws \ReflectionException
     */
    private function executeSaveNetwork(array $post)
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
    public function executeDeleteNetwork()
    {
        $network = $this->socialNetworkManager->findOneBy(['id' => $this->params['networkId']]);
        $this->socialNetworkManager->delete($network);

        $this->flashBag->set(FlashBag::SUCCESS, "Réseau social supprimé.");
        $this->redirectUrl('/dashboard/profil#sociaNetworks');
    }
}

