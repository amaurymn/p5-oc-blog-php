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
    public function getUserByMail(string $email)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                u.id, a.id AS admin_id, u.first_name, u.last_name, u.user_name, u.email,
                u.password, u.role, a.image, a.alt_img, a.cv_link, a.short_description
            FROM $this->table AS u
            LEFT JOIN admin AS a
            ON u.id = a.user_id
            WHERE u.email = :email
        ");

        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @return mixed
     */
    public function getAdminInfos()
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, a.*
            FROM $this->table AS u
            LEFT JOIN admin AS a
            ON u.id = a.user_id
            WHERE u.role = 'admin'
        ");
        $stmt->execute();

        return $stmt->fetch();
    }

    public function checkAdminAlreadyExist()
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM $this->table AS u
            WHERE u.role = :role
        ");

        $stmt->bindValue(':role', 'admin', PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @param string $userName
     * @return mixed
     */
    public function checkUserNameAlreadyExist(string $userName)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM $this->table WHERE user_name = :userName");

        $stmt->bindValue(':userName', $userName, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }
}
