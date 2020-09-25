<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;

class BlogController extends Controller
{
    public function executeShowBlog()
    {
        $this->render('@public/blog.html.twig');
    }

    public function executeShowBlogSingle()
    {
        $this->render('@public/blogSingle.html.twig');
    }
}