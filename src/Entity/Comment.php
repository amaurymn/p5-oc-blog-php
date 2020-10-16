<?php

namespace App\Entity;

use App\Core\Entity;
use App\Core\TimestampableEntity;

class Comment extends Entity
{
    use TimestampableEntity;

    private string $content;

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
