<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Core\Validator;
use App\Entity\Article;
use App\Manager\ArticleManager;
use App\Services\ImageUpload;
use Symfony\Component\Yaml\Yaml;

class ArticleController extends Controller
{
    const ARTICLE_LIST = '/dashboard/article/list';

    /** @var array|false */
    private $hasErrors;
    private $file;

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function executeReadList()
    {
        $articles = (new ArticleManager())->findAll(['id' => 'DESC']);

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
            $this->hasErrors = (new Validator($_POST))->articleValidation();
            $this->file      = (new ImageUpload($_FILES));

            if (!$this->hasErrors && $this->file->checkImage()) {
                $article = new Article(['admin_id' => 1]);
                $this->file->upload();

                $article->setImage($this->file->getName());
                $article->hydrate($_POST);

                (new ArticleManager())->create($article);
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleAdd.html.twig', [
            'errors' => $this->hasErrors,
        ]);
    }

    /**
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function executeEdit()
    {
        $article = (new ArticleManager())->findOneBy(['id' => $this->params['articleId']]);

        if ($this->isFormSubmit('publish')) {
            $this->hasErrors = (new Validator($_POST))->articleValidation();
            $this->file      = (new ImageUpload($_FILES));

            if (!$this->hasErrors) {
                if (!$this->file->checkImage()) {
                    $article->setImage($article->getImage());
                } else {
                    $this->file->upload();
                    $article->setImage($this->file->getName());
                }

                $article->hydrate($_POST);

                (new ArticleManager())->update($article);
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleEdit.html.twig', [
            'article' => $article,
            'errors'  => $this->hasErrors
        ]);
    }

    public function executeDelete(): void
    {
        $article = (new ArticleManager())->findOneBy(['id' => $this->params['articleId']]);

        $this->deleteImage($article->getImage());
        (new ArticleManager())->delete($article);
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
