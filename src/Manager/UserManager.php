<?php

namespace App\Manager;

use App\Core\Manager;
use \PDO;

class UserManager extends Manager
{
    /**
     * @param string $email
     * @return mixed
     */
    public function getUser(string $email)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                u.id, a.id AS admin_id, u.first_name, u.last_name, u.user_name, u.email,
                u.password, u.role, a.image, a.alt_img, a.cv_link, a.short_description
            FROM user AS u
            LEFT JOIN admin AS a
            ON u.id = a.user_id
            WHERE u.email = :email
        ");

        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function checkAdminAlreadyExist()
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM user AS u
            WHERE u.role = :role
        ");

        $stmt->bindValue(':role', 'admin', PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }
}
