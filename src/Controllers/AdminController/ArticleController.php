<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Entity\Article;
use App\Manager\ArticleManager;
use App\Services\Validator;

class ArticleController extends Controller
{
    const ARTICLE_LIST = '/dashboard/article/list';

    /** @var array|bool */
    private $formErrors;

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
        $article = new Article(['admin_id' => 1]);
        $article->setCreatedAt(new \DateTime());
        $article->setUpdatedAt(new \DateTime());

        if($this->isFormSubmit('publish')) {
            $article->hydrate($_POST);

            $this->formErrors = $this->formValidator($_POST);

            if(!$this->formErrors) {
                (new ArticleManager())->create($article);
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleAdd.html.twig', [
            'errors' => $this->formErrors
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
        $getArticle = (new ArticleManager())->findOneBy(['id' => $this->params['articleId']]);
        $editArticle = new Article(['admin_id' => 1, 'id' => $getArticle['id']]);

        if($this->isFormSubmit('publish')) {
            $editArticle->setCreatedAt($getArticle['created_at']);
            $editArticle->setUpdatedAt(new \DateTime());
            $editArticle->hydrate($_POST);

            $this->formErrors = $this->formValidator($_POST);

            if(!$this->formErrors) {
                (new ArticleManager())->update($editArticle);
                $this->redirectUrl(self::ARTICLE_LIST);
            }
        }

        $this->render('@admin/articleEdit.html.twig', [
            'article' => $getArticle,
            'errors' => $this->formErrors
        ]);
    }

    public function executeDeleteArticle(): void
    {
        $getArticle = (new ArticleManager())->findOneBy(['id' => $this->params['articleId']]);
        $article = new Article(['id' => $getArticle['id']]);

        (new ArticleManager())->delete($article);

        $this->redirectUrl(self::ARTICLE_LIST);
    }

    /**
     * @param array $data
     * @return array|false
     */
    private function formValidator(array $data)
    {
        $validator = new Validator($data);

        $validator->validate('title', 'Le titre')->required()->maxLength(255);
        $validator->validate('textHeader', 'Le chapô')->required();
        $validator->validate('content', 'Le contenu')->required();

        return $validator->hasErrors();
    }
}
