<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Core\Validator;
use App\Entity\Article;
use App\Manager\ArticleManager;
use App\Services\FlashBag;
use App\Services\ImageUpload;
use Symfony\Component\Yaml\Yaml;

class ArticleController extends Controller
{
    const ARTICLE_LIST = '/dashboard/article/list';

    private ArticleManager $manager;
    private FlashBag $flashBag;

    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        $this->manager  = new ArticleManager();
        $this->flashBag = new FlashBag();
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function executeReadList()
    {
        $articles = $this->manager->findAll(['id' => 'DESC']);

        $this->render('@admin/articleList.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function executeCreate()
    {
        if ($this->isFormSubmit('publish')) {
            $formCheck = (new Validator($_POST));
            $file      = (new ImageUpload($_FILES));

            if ($formCheck->articleValidation() && $file->checkImage()) {
                $article = new Article(['admin_id' => 1]);
                $file->upload();

                $article->setImage($file->getName());
                $article->hydrate($_POST);

                $this->manager->create($article);
                $this->flashBag->set(FlashBag::SUCCESS, "Article crée.");
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleAdd.html.twig');
    }

    /**
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function executeEdit()
    {
        $article = $this->manager->findOneBy(['id' => $this->params['articleId']]);

        if ($this->isFormSubmit('publish')) {
            $formCheck = (new Validator($_POST));
            $file      = (new ImageUpload($_FILES));

            if ($formCheck->articleValidation() && $file->checkImage()) {
                $this->deleteImage($article->getImage());
                $file->upload();
                $article->setImage($file->getName());

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
     */
    private function deleteImage(string $image)
    {
        $config    = Yaml::parseFile(CONF_DIR . '/config.yml');
        $imagePath = PUBLIC_DIR . '/img' . $config['imgUploadPath'] . '/' . $image;

        return unlink($imagePath);
    }
}
