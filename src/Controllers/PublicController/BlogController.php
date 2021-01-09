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
use App\Services\Paginator\Paginator;
use App\Services\Session;
use ReflectionException;

class BlogController extends Controller
{
    private ArticleManager $articleManager;
    private CommentManager $commentManager;
    private Session $session;
    private FlashBag $flashBag;

    /**
     * BlogController constructor.
     * @param $action
     * @param $params
     */
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
        $paginator = new Paginator($this->articleManager->findAll(['created_at' => 'DESC']));
        $paginator->setPath('/blog/page/');
        $articles = $paginator->paginateItems($this->params['page'] ?? 1);

        $this->render('@public/blog.html.twig', [
            'articles'  => $articles,
            'paginator' => $paginator->getPager()
        ]);
    }

    /**
     * @throws EntityNotFoundException
     * @throws ReflectionException
     * @throws TwigException
     */
    public function executeShowSingle(): void
    {
        $article   = $this->articleManager->findOneBy(['slug' => $this->params['slug']]);
        $paginator = new Paginator($this->commentManager->getCommentsFromArticle($article->getId()));
        $paginator->setPath('/blog/' . $article->getSlug() . '/page/');
        $comments = $paginator->paginateItems($this->params['page'] ?? 1, 6);

        if ($this->isFormSubmit('publish') && (new Validator($_POST))->commentValidation()) {
            $this->addNewComment($article->getId(), $_POST);
        }

        $this->render('@public/blogSingle.html.twig', [
            'article'   => $article,
            'comments'  => $comments,
            'post'      => $_POST,
            'paginator' => $paginator->getPager()
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
            ->setOnline(0);
        $comment->hydrate($_POST);
        $manager->create($comment);

        $this->flashBag->set(FlashBag::SUCCESS, "Commentaire ajouté, il sera visible après validation.");
        $this->redirectUrl($_SERVER['REQUEST_URI']);
    }
}
