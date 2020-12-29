<?php

namespace App\Services;

use App\Core\PDOFactory;

class DashboardStats
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new PDOFactory())->getPDO();
    }

    /**
     * @return mixed
     */
    public function getStats()
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM
                (SELECT COUNT(*) AS art_online FROM article) AS art,
                (SELECT COUNT(*) AS com_total FROM comment) AS cv,
                (SELECT COUNT(*) AS com_pending FROM comment WHERE online = 0) AS cp,
                (SELECT COUNT(*) AS usr_registered FROM user) AS usr;
        ");
        $stmt->execute();

        return $stmt->fetch();
    }
}
