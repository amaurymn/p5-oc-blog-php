<?php

namespace App\Manager;

use App\Core\Manager;

class ArticleManager extends Manager
{
    public function setTable(string $tableName)
    {
        $this->table = $tableName;
    }
}
