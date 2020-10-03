<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;

class ArticleController extends Controller
{

    public function executeShowArticleList()
    {
        $this->render('@admin/articleList.html.twig');
    }

    public function executeShowArticleAdd()
    {
        $this->render('@admin/articleAdd.html.twig');
    }

    public function executeShowArticleEdit()
    {
        $this->render('@admin/articleEdit.html.twig');
    }
}
