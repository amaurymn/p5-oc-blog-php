<?php

namespace App\Services;

use App\Core\PDOFactory;
use PDO;

class DashboardStats
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new PDOFactory())->getPDO();
    }

    /**
     * @return mixed
     */
    public function getStats()
    {
        $stmt = $this->pdo->query("
            SELECT *
            FROM
                (SELECT COUNT(id) AS art_online FROM article) AS art,
                (SELECT COUNT(id) AS com_total FROM comment) AS cv,
                (SELECT COUNT(id) AS com_pending FROM comment WHERE online = 0) AS cp,
                (SELECT COUNT(id) AS usr_registered FROM user) AS usr;
        ");

        return $stmt->fetch();
    }
}
