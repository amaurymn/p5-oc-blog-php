<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;

use App\Manager\ArticleManager;

class BlogController extends Controller
{
    private $manager;

    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        $this->manager = new ArticleManager();
    }

    public function executeShowBlog()
    {
        $articles = $this->manager->findAll(['created_at' => 'DESC'], 3);

        $this->render('@public/blog.html.twig', [
            'articles' => $articles
        ]);
    }

    public function executeShowBlogSingle()
    {

        $article = $this->manager->findOneBy(['slug' => $this->params['slug']]);

        $this->render('@public/blogSingle.html.twig', [
            'article' => $article
        ]);
    }
}
