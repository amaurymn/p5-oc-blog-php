<?php

namespace App\Controllers\PublicController;

use App\Core\Controller;
use App\Core\Validator;
use App\Entity\Comment;
use App\Exception\EntityNotFoundException;
use App\Exception\TwigException;
use App\Manager\ArticleManager;
use App\Manager\CommentManager;
use App\Services\FlashBag;
use App\Services\Session;
use ReflectionException;

class BlogController extends Controller
{
    private ArticleManager $articleManager;
    private CommentManager $commentManager;
    private Session $session;
    private FlashBag $flashBag;

    public function __construct($action, $params)
    {
        parent::__construct($action, $params);
        $this->articleManager = new ArticleManager();
        $this->commentManager = new CommentManager();
        $this->session        = new Session();
        $this->flashBag       = new FlashBag();
    }

    /**
     * @throws TwigException
     */
    public function executeShowBlog(): void
    {
        $articles = $this->articleManager->findAll(['created_at' => 'DESC'], 3);

        $this->render('@public/blog.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @throws EntityNotFoundException
     * @throws ReflectionException
     * @throws TwigException
     */
    public function executeShowSingle(): void
    {
        $article  = $this->articleManager->findOneBy(['slug' => $this->params['slug']]);
        $comments = $this->commentManager->getCommentsFromArticle($article->getId(), true);

        if ($this->isFormSubmit('publish') && (new Validator($_POST))->commentValidation()) {
            $this->addNewComment($article->getId(), $_POST);
        }

        $this->render('@public/blogSingle.html.twig', [
            'article'  => $article,
            'comments' => $comments,
            'post'     => $_POST
        ]);
    }

    /**
     * @param int $articleId
     * @param array $post
     * @throws ReflectionException
     */
    private function addNewComment(int $articleId, array $post): void
    {
        $manager = new CommentManager();
        $comment = new Comment();

        $comment
            ->setContent($post['content'])
            ->setArticleId($articleId)
            ->setUserId($this->session->get('user')['id'])
            ->setOnline(1);
        $comment->hydrate($_POST);
        $manager->create($comment);

        $this->flashBag->set(FlashBag::SUCCESS, "Commentaire ajouté.");
        $this->redirectUrl($_SERVER['REQUEST_URI']);
    }
}
