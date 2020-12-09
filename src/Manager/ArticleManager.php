<?php

namespace App\Manager;


use App\Core\Manager;
use \PDO;

class ArticleManager extends Manager
{
    /**
     * @param string $slug
     * @return int
     */
    public function checkSlugExist(string $slug): int
    {
        $stmt = $this->pdo->prepare("SELECT slug from article WHERE slug LIKE :slug");
        $stmt->bindValue(':slug', '%' . $slug . '%', PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
