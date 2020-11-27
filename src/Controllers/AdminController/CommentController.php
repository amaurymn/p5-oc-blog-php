<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Exception\TwigException;
use App\Services\Session;

class CommentController extends Controller
{
    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        (new Session())->redirectIfNotAdmin();
    }

    /**
     * @throws TwigException
     */
    public function executeShowCommentList(): void
    {
        $this->render('@admin/commentList.html.twig');
    }
}
