<?php

namespace App\Core;

use DateTime;

trait TimestampableEntity
{
    private $createdAt;
    private $updatedAt;

    /**
     * @return DateTime
     * @throws \Exception
     */
    public function getCreatedAt(): DateTime
    {
        return new DateTime($this->createdAt);
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt->format('Y-m-d H:i:s');
    }

    /**
     * @return DateTime
     * @throws \Exception
     */
    public function getUpdatedAt(): DateTime
    {
        return new DateTime($this->updatedAt);
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt->format('Y-m-d H:i:s');
    }
}
