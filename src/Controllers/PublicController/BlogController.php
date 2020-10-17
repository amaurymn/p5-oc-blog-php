<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;

use App\Manager\ArticleManager;

class BlogController extends Controller
{
    public function executeShowBlog()
    {
        $manager = new ArticleManager();
        $articles = $manager->findBy([], ['created_at' => 'DESC'],3);

        $this->render('@public/blog.html.twig', [
            'articles' => $articles
        ]);
    }

    public function executeShowBlogSingle()
    {
        $this->render('@public/blogSingle.html.twig');
    }
}
