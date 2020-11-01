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
    private $errors;

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function executeShowArticleList()
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
    public function executeShowArticleAdd()
    {
        if ($this->isFormSubmit('publish')) {
            $article = new Article(['admin_id' => 1]);
            $article->setCreatedAt(new \DateTime());
            $article->setUpdatedAt(new \DateTime());

            $this->errors = (new Validator($_POST))->validateArticle();

            if (!$this->errors) {
                $article->hydrate($_POST);
                (new ArticleManager())->create($article);
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleAdd.html.twig', [
            'errors' => $this->errors
        ]);
    }

    /**
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function executeShowArticleEdit()
    {
        $getArticle  = (new ArticleManager())->findOneBy(['id' => $this->params['articleId']]);

        if ($this->isFormSubmit('publish')) {
            $editArticle = new Article(['admin_id' => 1, 'id' => $getArticle['id']]);
            $editArticle->setCreatedAt($getArticle['created_at']);
            $editArticle->setUpdatedAt(new \DateTime());

            $this->errors = (new Validator($_POST))->validateArticle();

            if (!$this->errors) {
                $editArticle->hydrate($_POST);
                (new ArticleManager())->update($editArticle);
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleEdit.html.twig', [
            'article' => $getArticle,
            'errors'  => $this->errors
        ]);
    }

    public function executeDeleteArticle(): void
    {
        $getArticle = (new ArticleManager())->findOneBy(['id' => $this->params['articleId']]);
        $article    = new Article(['id' => $getArticle['id']]);

        (new ArticleManager())->delete($article);

        $this->redirectUrl(self::ARTICLE_LIST);
    }
}
