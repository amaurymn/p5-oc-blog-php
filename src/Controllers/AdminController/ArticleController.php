<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Core\Validator;
use App\Entity\Article;
use App\Manager\ArticleManager;

class ArticleController extends Controller
{
    const ARTICLE_LIST = '/dashboard/article/list';

    /** @var array|false */
    private $hasErrors;

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

            if (!$this->hasErrors) {
                $article = new Article(['admin_id' => 1]);
                $article->hydrate($_POST);

                (new ArticleManager())->create($article);
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleAdd.html.twig', [
            'errors' => $this->hasErrors
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

            if (!$this->hasErrors) {
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

        (new ArticleManager())->delete($article);
        $this->redirectUrl(self::ARTICLE_LIST);
    }
}
