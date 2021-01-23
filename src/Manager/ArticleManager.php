<?php

namespace App\Manager;


use App\Core\Manager;
use \PDO;

class ArticleManager extends Manager
{
    /**
     * @param string $slug
     * @return mixed
     */
    public function checkSlugExist(string $slug)
    {
        $stmt = $this->pdo->prepare("SELECT slug from $this->table WHERE slug = :slug");
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }
}
