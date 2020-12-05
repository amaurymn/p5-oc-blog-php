<?php

namespace App\Manager;

use App\Core\Manager;
use \PDO;

class CommentManager extends Manager
{
    /**
     * @param int $articleId
     * @param bool $online
     * @return array
     */
    public function getCommentsFromArticle(int $articleId, bool $online = true): array
    {
        $query = "
            SELECT c.id, c.content, c.online, c.created_at, u.user_name
            FROM comment AS c
            LEFT JOIN user AS u
            ON u.id = c.user_id
            WHERE c.article_id = :articleId
        ";

        if ($online) {
            $query .= " AND c.online = 1";
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':articleId', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
