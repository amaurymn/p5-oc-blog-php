<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Core\Validator;
use App\Entity\Article;
use App\Exception\EntityNotFoundException;
use App\Exception\FileException;
use App\Exception\TwigException;
use App\Manager\ArticleManager;
use App\Services\FlashBag;
use App\Services\FileUploader;
use App\Services\Paginator\Paginator;
use App\Services\Session;
use App\Services\Slugifier;
use ReflectionException;

class ArticleController extends Controller
{
    private const ARTICLE_LIST = '/dashboard/article/list';

    private ArticleManager $manager;
    private FlashBag $flashBag;
    private Session $session;
    private Slugifier $slugifier;

    /**
     * ArticleController constructor.
     * @param $action
     * @param $params
     */
    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        $this->manager   = new ArticleManager();
        $this->flashBag  = new FlashBag();
        $this->session   = new Session();
        $this->slugifier = new Slugifier();
        $this->session->redirectIfNotAdmin();
    }

    /**
     * @throws TwigException
     */
    public function executeReadList(): void
    {
        $paginator = new Paginator($this->manager->findAll(['created_at' => 'DESC']));
        $paginator->setPath(self::ARTICLE_LIST . '/');
        $articles = $paginator->paginateItems($this->params['page'] ?? 1, 6);

        $this->render('@admin/articleList.html.twig', [
            'articles'  => $articles,
            'paginator' => $paginator->getPager()
        ]);
    }

    /**
     * @throws ReflectionException
     * @throws TwigException
     */
    public function executeCreate(): void
    {
        if ($this->isFormSubmit('publish')) {
            $formCheck = (new Validator($_POST));
            $file      = (new FileUploader($_FILES));

            if ($formCheck->articleValidation() && $file->checkFile(FileUploader::FILE_IMG)) {
                $article = new Article(['admin_id' => $this->session->get('user')->getId()]);
                $file->upload(FileUploader::TYPE_POST);

                $article->setImage($file->getName());
                $article->hydrate($_POST);
                $article->setSlug($this->slugifier->getUniqueSlug($article->getTitle()));

                $this->manager->create($article);

                $this->flashBag->set(FlashBag::SUCCESS, "Article crée.");
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleAdd.html.twig');
    }

    /**
     * @throws EntityNotFoundException
     * @throws FileException
     * @throws ReflectionException
     * @throws TwigException
     */
    public function executeEdit(): void
    {
        $article = $this->manager->findOneBy(['id' => $this->params['articleId']]);

        if ($this->isFormSubmit('publish')) {
            $formCheck = new Validator($_POST);
            $file      = new FileUploader($_FILES);

            if ($formCheck->articleValidation()) {
                if ($_FILES['image']['size'] !== 0 && $file->checkFile(FileUploader::FILE_IMG)) {
                    $file->deleteFile(FileUploader::TYPE_POST, $article->getImage());
                    $file->upload(FileUploader::TYPE_POST);
                    $article->setImage($file->getName());
                } else {
                    $article->setImage($article->getImage());
                }

                $article->hydrate($_POST);
                $this->manager->update($article);

                $this->flashBag->set(FlashBag::SUCCESS, "Article édité.");
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleEdit.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @throws EntityNotFoundException
     * @throws FileException
     */
    public function executeDelete(): void
    {
        $file = new FileUploader();
        $article = $this->manager->findOneBy(['id' => $this->params['articleId']]);

        $file->deleteFile(FileUploader::TYPE_POST, $article->getImage());
        $this->manager->delete($article);

        $this->flashBag->set(FlashBag::SUCCESS, "Article supprimé.");
        $this->redirectUrl(self::ARTICLE_LIST);
    }
}
