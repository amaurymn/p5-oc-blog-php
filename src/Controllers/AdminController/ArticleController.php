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
use App\Services\fileUploader;
use App\Services\Session;
use App\Services\Slugifier;
use ReflectionException;
use Symfony\Component\Yaml\Yaml;

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
        $articles = $this->manager->findAll(['id' => 'DESC']);

        $this->render('@admin/articleList.html.twig', [
            'articles' => $articles
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
            $file      = (new fileUploader($_FILES));

            if ($formCheck->articleValidation() && $file->checkFile()) {
                $article = new Article(['admin_id' => $this->session->get('user')['admin_id']]);
                $file->upload();

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
            $formCheck = (new Validator($_POST));
            $file      = (new fileUploader($_FILES));

            if ($formCheck->articleValidation() && $file->checkFile()) {
                $this->deleteImage($article->getImage());
                $file->upload();

                $article->setImage($file->getName());
                $article->hydrate($_POST);
                $article->setSlug($this->slugifier->getUniqueSlug($article->getTitle()));

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
        $article = $this->manager->findOneBy(['id' => $this->params['articleId']]);

        $this->deleteImage($article->getImage());
        $this->manager->delete($article);

        $this->flashBag->set(FlashBag::SUCCESS, "Article supprimé.");
        $this->redirectUrl(self::ARTICLE_LIST);
    }

    /**
     * @param string $image
     * @return bool
     * @throws FileException
     */
    private function deleteImage(string $image): bool
    {
        $config    = Yaml::parseFile(CONF_DIR . '/config.yml');
        $imagePath = PUBLIC_DIR . '/img' . $config['imgUploadPath'] . '/' . $image;

        try {
            return unlink($imagePath);
        } catch (\Exception $e) {
            throw new FileException("Erreur lors la suppression de l'image.");
        }
    }
}
