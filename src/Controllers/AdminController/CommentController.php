<?php

namespace App\Controllers\AdminController;

use App\Core\Controller;
use App\Exception\EntityNotFoundException;
use App\Exception\TwigException;
use App\Manager\CommentManager;
use App\Services\FlashBag;
use App\Services\Paginator\Paginator;
use App\Services\Session;
use ReflectionException;

class CommentController extends Controller
{
    private const COMMENT_LIST = '/dashboard/comment/list';

    private CommentManager $commentManager;
    private FlashBag $flashBag;

    /**
     * CommentController constructor.
     * @param $action
     * @param $params
     */
    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        (new Session())->redirectIfNotAdmin();

        $this->commentManager = new CommentManager();
        $this->flashBag       = new FlashBag();
    }

    /**
     * show comment list page
     * @throws TwigException
     */
    public function executeShowCommentList(): void
    {
        $paginator = new Paginator($this->commentManager->getCommentAndAuthor());
        $paginator->setPath(self::COMMENT_LIST . '/');
        $comments = $paginator->paginateItems($this->params['page'] ?? 1, 10);

        $this->render('@admin/commentList.html.twig', [
            'comments'  => $comments,
            'paginator' => $paginator->getPager()
        ]);
    }

    /**
     * delete comment
     * @throws EntityNotFoundException
     */
    public function executeDelete(): void
    {
        $article = $this->commentManager->findOneBy(['id' => $this->params['commentId']]);
        $this->commentManager->delete($article);

        $this->flashBag->set(FlashBag::SUCCESS, "Commentaire supprimé.");
        $this->redirectUrl($_SERVER['HTTP_REFERER']);
    }

    /**
     * validate comment
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    public function executeValidate(): void
    {
        $comment = $this->commentManager->findOneBy(['id' => $this->params['commentId']]);

        switch ($this->params['state']) {
            case 'validate':
                $comment->setOnline(1);
                $this->flashBag->set(FlashBag::SUCCESS, "Commentaire validé.");
                break;
            case 'suspend':
                $comment->setOnline(0);
                $this->flashBag->set(FlashBag::WARNING, "Commentaire suspendu.");
                break;
            default:
                $this->flashBag->set(FlashBag::ERROR, "Un problème est survenu.");
                break;
        }

        $this->commentManager->update($comment);

        $this->redirectUrl($_SERVER['HTTP_REFERER']);
    }
}
