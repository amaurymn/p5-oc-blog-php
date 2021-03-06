<?php

namespace App\Manager;

use App\Core\Manager;
use \PDO;

class CommentManager extends Manager
{
    /**
     * @param int $articleId
     * @return array
     */
    public function getCommentsFromArticle(int $articleId): array
    {
        $query = "
            SELECT c.id, c.content, c.online, c.created_at, u.user_name
            FROM $this->table AS c
            LEFT JOIN user AS u
                ON c.user_id = u.id
            WHERE c.article_id = :articleId
            AND c.online = 1
            ORDER BY c.created_at DESC
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param int|null $limit
     * @return array
     */
    public function getCommentAndAuthor(?int $limit = null)
    {
        $query = "
            SELECT c.id, c.content, c.online, c.created_at, u.user_name, u.email, a.title AS artTitle, a.slug AS artSlug
            FROM $this->table AS c
            LEFT JOIN user AS u
                ON c.user_id = u.id
            LEFT JOIN article AS a
                ON c.article_id = a.id
            ORDER BY c.created_at DESC
        ";

        if ($limit !== null) {
            $query .= sprintf(" LIMIT %d", $limit);
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
