<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Entity\Article;
use App\Manager\ArticleManager;

class ArticleController extends Controller
{

    public function executeShowArticleList()
    {
        $this->render('@admin/articleList.html.twig');
    }

    public function executeShowArticleAdd()
    {
        $article = new Article(['admin_id' => 1, 'id' => 1]);

        if($this->isFormSubmit('publish')) {
            $article->hydrate($_POST);

            (new ArticleManager())->update($article);
        } else {
            dump(false);
        }
        $this->render('@admin/articleAdd.html.twig');
    }

    public function executeShowArticleEdit()
    {
        $this->render('@admin/articleEdit.html.twig');
    }
}
