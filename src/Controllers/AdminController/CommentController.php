<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Exception\EntityNotFoundException;
use App\Exception\TwigException;
use App\Manager\CommentManager;
use App\Services\FlashBag;
use App\Services\Session;

class CommentController extends Controller
{
    private const COMMENT_LIST = '/dashboard/comment/list';

    private CommentManager $commentManager;
    private FlashBag $flashBag;

    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        (new Session())->redirectIfNotAdmin();

        $this->commentManager = new CommentManager();
        $this->flashBag       = new FlashBag();
    }

    /**
     * @throws TwigException
     */
    public function executeShowCommentList(): void
    {
        $comments = $this->commentManager->getCommentAndAuthor();

        $this->render('@admin/commentList.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function executeDelete(): void
    {
        $article = $this->commentManager->findOneBy(['id' => $this->params['commentId']]);
        $this->commentManager->delete($article);

        $this->flashBag->set(FlashBag::SUCCESS, "Commentaire supprimé.");
        $this->redirectUrl(self::COMMENT_LIST);
    }

    /**
     * @throws EntityNotFoundException
     * @throws \ReflectionException
     */
    public function executeValidate(): void
    {
        $comment = $this->commentManager->findOneBy(['id' => $this->params['commentId']]);

        switch ($this->params['state']) {
            case 'validate':
                $comment->setOnline(1);
                $this->flashBag->set(FlashBag::SUCCESS, "Article validé.");
                break;
            case 'suspend':
                $comment->setOnline(0);
                $this->flashBag->set(FlashBag::WARNING, "Article suspendu.");
                break;
            default:
                $this->flashBag->set(FlashBag::ERROR, "Un problème est survenu.");
                break;
        }

        $this->commentManager->update($comment);
        $this->redirectUrl(self::COMMENT_LIST);
    }
}
