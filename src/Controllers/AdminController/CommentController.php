<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;

class CommentController extends Controller
{

    public function executeShowCommentList()
    {
        $this->render('@admin/commentList.html.twig');
    }
}
