<?php

namespace App\Entity;

use App\Core\Entity;
use App\Core\TimestampableEntity;

class Comment extends Entity
{
    use TimestampableEntity;

    private string $content;
    private int $online;
    private int $articleId;
    private int $userId;

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): Comment
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return int
     */
    public function getOnline(): int
    {
        return $this->online;
    }

    /**
     * @param int $online
     * @return $this
     */
    public function setOnline(int $online): Comment
    {
        $this->online = $online;

        return $this;
    }

    /**
     * @return int
     */
    public function getArticleId(): int
    {
        return $this->articleId;
    }

    /**
     * @param $articleId
     * @return $this
     */
    public function setArticleId($articleId): Comment
    {
        $this->articleId = $articleId;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId(int $userId): Comment
    {
        $this->userId = $userId;

        return $this;
    }
}
