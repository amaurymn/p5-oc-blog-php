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
        $articles = (new ArticleManager())->findAll();

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
            $article = new Article(['admin_id' => 1]);
            $article->setCreatedAt(new \DateTime());
            $article->setUpdatedAt(new \DateTime());

            $this->hasErrors = (new Validator($_POST))->articleValidation();

            if (!$this->hasErrors) {
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
        $getArticle  = (new ArticleManager())->findOneBy(['id' => $this->params['articleId']]);

        if ($this->isFormSubmit('publish')) {
            $editArticle = new Article(['admin_id' => 1, 'id' => $getArticle['id']]);
            $editArticle->setCreatedAt($getArticle['created_at']);
            $editArticle->setUpdatedAt(new \DateTime());

            $this->hasErrors = (new Validator($_POST))->articleValidation();

            if (!$this->hasErrors) {
                $editArticle->hydrate($_POST);
                (new ArticleManager())->update($editArticle);
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleEdit.html.twig', [
            'article' => $getArticle,
            'errors'  => $this->hasErrors
        ]);
    }

    public function executeDelete(): void
    {
        $article = (new ArticleManager())->findOneBy(['id' => $this->params['articleId']]);
//        $article    = new Article(['id' => $getArticle['id']]);

        (new ArticleManager())->delete($article);
        // TypeError: Argument 1 passed to App\Core\Manager::delete() must be an instance of App\Core\Entity, array given,
        $this->redirectUrl(self::ARTICLE_LIST);
    }
}
