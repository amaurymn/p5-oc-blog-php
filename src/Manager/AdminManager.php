<?php

namespace App\Manager;

use App\Core\Manager;
use \PDO;

class AdminManager extends Manager
{
    /**
     * @param int $userId
     * @return false|mixed
     */
    public function getAdminByUser(int $userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM $this->table
            WHERE user_id = :user_id
        ");

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        if (!$result) {
            return false;
        }

        return new $this->entity($result);
    }
}
